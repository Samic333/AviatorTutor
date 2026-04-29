<?php
/**
 * Authentication Controller
 *
 * Login, registration, logout, email verification, password reset.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Services\EmailService;

class AuthController extends Controller
{
    /* ============================================================ LOGIN */

    public function loginForm(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $response->redirect('/dashboard');
            return;
        }
        // Marketing layout reads flash_ok / flash_error from session directly.
        $response->html($this->view('auth/login', [
            'title'      => 'Login',
            'csrf_token' => CSRF::generate(),
        ], 'marketing'));
    }

    public function login(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $this->loginFormWithError($response, 'Session expired. Please try again.');
            return;
        }

        $email    = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        if ($email === '' || $password === '') {
            $this->loginFormWithError($response, 'Email and password are required.');
            return;
        }

        $db   = DB::instance();
        $user = $db->queryOne(
            'SELECT id, name, email, password_hash, role, study_streak, email_verified_at
               FROM users WHERE email = ?',
            [$email]
        );

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $this->loginFormWithError($response, 'Invalid email or password.');
            return;
        }

        // Block unverified accounts if config requires it.
        $cfg = require BASE_PATH . '/config/app.php';
        if (!empty($cfg['require_email_verification']) && empty($user['email_verified_at'])) {
            $_SESSION['flash_error'] = 'Please verify your email first. We sent a verification link when you registered.';
            $_SESSION['unverified_email'] = $user['email'];
            $response->redirect('/verify-email');
            return;
        }

        Auth::login((int) $user['id'], [
            'name'         => $user['name'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'study_streak' => $user['study_streak'],
        ]);

        // Track last login.
        $db->execute('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);

        CSRF::regenerate();
        $response->redirect('/dashboard');
    }

    /* ===================================================== REGISTRATION */

    public function registerForm(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $response->redirect('/dashboard');
            return;
        }
        $response->html($this->view('auth/register', [
            'title'      => 'Register',
            'csrf_token' => CSRF::generate(),
        ], 'marketing'));
    }

    public function register(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $this->registerFormWithError($response, 'Session expired. Please try again.');
            return;
        }

        $name             = trim((string) $request->input('name'));
        $email            = trim((string) $request->input('email'));
        $password         = (string) $request->input('password');
        $password_confirm = (string) $request->input('password_confirm');

        if ($name === '' || $email === '' || $password === '') {
            $this->registerFormWithError($response, 'All fields are required.');
            return;
        }
        if (strlen($password) < 8) {
            $this->registerFormWithError($response, 'Password must be at least 8 characters.');
            return;
        }
        if ($password !== $password_confirm) {
            $this->registerFormWithError($response, 'Passwords do not match.');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->registerFormWithError($response, 'Invalid email format.');
            return;
        }

        $db = DB::instance();
        if ($db->queryOne('SELECT id FROM users WHERE email = ?', [$email])) {
            $this->registerFormWithError($response, 'Email already registered.');
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userId = (int) $db->insert(
            'INSERT INTO users (name, email, password_hash, role, created_at)
             VALUES (?, ?, ?, ?, NOW())',
            [$name, $email, $passwordHash, 'learner']
        );
        if ($userId === 0) {
            $this->registerFormWithError($response, 'Registration failed. Please try again.');
            return;
        }

        $cfg = require BASE_PATH . '/config/app.php';

        // If verification is disabled, behave like before — auto-login.
        if (empty($cfg['require_email_verification'])) {
            Auth::login($userId, [
                'name' => $name, 'email' => $email, 'role' => 'learner', 'study_streak' => 0,
            ]);
            CSRF::regenerate();
            $response->redirect('/onboarding/aircraft');
            return;
        }

        // Issue verification token + send email.
        $rawToken = EmailService::generateToken();
        $hash     = EmailService::hashToken($rawToken);
        $ttlHours = (int) ($cfg['verification_token_ttl_hours'] ?? 48);
        $expires  = date('Y-m-d H:i:s', time() + $ttlHours * 3600);

        $db->insert(
            'INSERT INTO email_verifications (user_id, token_hash, expires_at)
             VALUES (?, ?, ?)',
            [$userId, $hash, $expires]
        );

        EmailService::sendVerification(
            ['id' => $userId, 'name' => $name, 'email' => $email],
            $rawToken
        );

        $_SESSION['unverified_email'] = $email;
        $response->redirect('/verify-email');
    }

    /* ============================================ EMAIL VERIFICATION */

    /** "Check your email" holding page after registration. */
    public function verifyEmailPage(Request $request, Response $response): void
    {
        $email = (string) ($_SESSION['unverified_email'] ?? '');
        $response->html($this->view('auth/verify-sent', [
            'title'      => 'Check your email',
            'email'      => $email,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'marketing'));
    }

    /** Click handler from the verification email. */
    public function verifyEmail(Request $request, Response $response): void
    {
        $rawToken = (string) $this->param('token');
        if ($rawToken === '') {
            $this->renderVerifyFailed($response, 'Missing verification token.');
            return;
        }

        $hash = EmailService::hashToken($rawToken);
        $db   = DB::instance();

        $row = $db->queryOne(
            'SELECT id, user_id, expires_at, used_at
               FROM email_verifications
              WHERE token_hash = ? LIMIT 1',
            [$hash]
        );

        if (!$row) {
            $this->renderVerifyFailed($response, 'This verification link is invalid or already used.');
            return;
        }
        if (!empty($row['used_at'])) {
            $this->renderVerifyFailed($response, 'This verification link has already been used.');
            return;
        }
        if (strtotime((string) $row['expires_at']) < time()) {
            $this->renderVerifyFailed($response, 'This verification link has expired. You can request a new one below.');
            return;
        }

        // Mark token used + set email_verified_at on the user.
        $db->execute('UPDATE email_verifications SET used_at = NOW() WHERE id = ?', [$row['id']]);
        $db->execute('UPDATE users SET email_verified_at = NOW() WHERE id = ?', [$row['user_id']]);

        // Auto-login after successful verification.
        $user = $db->queryOne(
            'SELECT id, name, email, role, study_streak FROM users WHERE id = ?',
            [$row['user_id']]
        );
        if ($user) {
            Auth::login((int) $user['id'], [
                'name'         => $user['name'],
                'email'        => $user['email'],
                'role'         => $user['role'],
                'study_streak' => $user['study_streak'],
            ]);
            CSRF::regenerate();
        }

        unset($_SESSION['unverified_email']);
        $_SESSION['flash_ok'] = 'Email verified — welcome aboard!';
        $response->redirect('/onboarding/aircraft');
    }

    /** Re-send verification link. */
    public function resendVerification(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $response->redirect('/verify-email');
            return;
        }

        $email = trim((string) $request->input('email'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email address.';
            $response->redirect('/verify-email');
            return;
        }

        $db   = DB::instance();
        $user = $db->queryOne(
            'SELECT id, name, email, email_verified_at FROM users WHERE email = ?',
            [$email]
        );

        // Always show the same flash to avoid leaking which emails exist.
        $_SESSION['flash_ok'] = 'If that email is registered and unverified, we sent a fresh link.';

        if ($user && empty($user['email_verified_at'])) {
            $cfg      = require BASE_PATH . '/config/app.php';
            $rawToken = EmailService::generateToken();
            $hash     = EmailService::hashToken($rawToken);
            $expires  = date('Y-m-d H:i:s', time() + ((int) ($cfg['verification_token_ttl_hours'] ?? 48)) * 3600);

            $db->insert(
                'INSERT INTO email_verifications (user_id, token_hash, expires_at)
                 VALUES (?, ?, ?)',
                [$user['id'], $hash, $expires]
            );
            EmailService::sendVerification($user, $rawToken);
        }

        $response->redirect('/verify-email');
    }

    /* ============================================ PASSWORD RESET */

    public function forgotForm(Request $request, Response $response): void
    {
        $response->html($this->view('auth/forgot', [
            'title'      => 'Forgot password',
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'marketing'));
    }

    public function forgotSend(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $response->redirect('/forgot-password');
            return;
        }

        $email = trim((string) $request->input('email'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email address.';
            $response->redirect('/forgot-password');
            return;
        }

        $db   = DB::instance();
        $user = $db->queryOne('SELECT id, name, email FROM users WHERE email = ?', [$email]);

        // Always show success — don't leak existence.
        $_SESSION['flash_ok'] = 'If that email is registered, we sent a reset link.';

        if ($user) {
            $cfg      = require BASE_PATH . '/config/app.php';
            $rawToken = EmailService::generateToken();
            $hash     = EmailService::hashToken($rawToken);
            $expires  = date('Y-m-d H:i:s', time() + ((int) ($cfg['password_reset_ttl_hours'] ?? 2)) * 3600);

            $db->insert(
                'INSERT INTO password_resets (user_id, token_hash, expires_at)
                 VALUES (?, ?, ?)',
                [$user['id'], $hash, $expires]
            );
            EmailService::sendPasswordReset($user, $rawToken);
        }

        $response->redirect('/forgot-password');
    }

    public function resetForm(Request $request, Response $response): void
    {
        $rawToken = (string) $this->param('token');
        if ($rawToken === '') {
            $this->renderResetFailed($response, 'Invalid reset link.');
            return;
        }

        $row = DB::instance()->queryOne(
            'SELECT id, user_id, expires_at, used_at FROM password_resets WHERE token_hash = ?',
            [EmailService::hashToken($rawToken)]
        );

        if (!$row || !empty($row['used_at']) || strtotime((string) $row['expires_at']) < time()) {
            $this->renderResetFailed($response, 'This reset link is invalid or expired.');
            return;
        }

        $response->html($this->view('auth/reset', [
            'title'      => 'Reset password',
            'token'      => $rawToken,
            'csrf_token' => CSRF::generate(),
            'flashError' => $this->popFlash('flash_error'),
        ], 'marketing'));
    }

    public function resetPassword(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $response->redirect('/forgot-password');
            return;
        }

        $rawToken         = (string) $this->param('token');
        $password         = (string) $request->input('password');
        $password_confirm = (string) $request->input('password_confirm');

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = 'Password must be at least 8 characters.';
            $response->redirect('/reset-password/' . urlencode($rawToken));
            return;
        }
        if ($password !== $password_confirm) {
            $_SESSION['flash_error'] = 'Passwords do not match.';
            $response->redirect('/reset-password/' . urlencode($rawToken));
            return;
        }

        $db   = DB::instance();
        $row  = $db->queryOne(
            'SELECT id, user_id, expires_at, used_at FROM password_resets WHERE token_hash = ?',
            [EmailService::hashToken($rawToken)]
        );

        if (!$row || !empty($row['used_at']) || strtotime((string) $row['expires_at']) < time()) {
            $this->renderResetFailed($response, 'This reset link is invalid or expired.');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->execute('UPDATE users SET password_hash = ? WHERE id = ?', [$hash, $row['user_id']]);
        $db->execute('UPDATE password_resets SET used_at = NOW() WHERE id = ?', [$row['id']]);

        $_SESSION['flash_ok'] = 'Password updated. You can now log in.';
        $response->redirect('/login');
    }

    /* =========================================================== LOGOUT */

    public function logout(Request $request, Response $response): void
    {
        Auth::logout();
        $response->redirect('/login');
    }

    /* ============================================================= helpers */

    private function loginFormWithError(Response $response, string $error): void
    {
        $response->html($this->view('auth/login', [
            'title'      => 'Login',
            'csrf_token' => CSRF::generate(),
            'error'      => $error,
        ], 'marketing'));
    }

    private function registerFormWithError(Response $response, string $error): void
    {
        $response->html($this->view('auth/register', [
            'title'      => 'Register',
            'csrf_token' => CSRF::generate(),
            'error'      => $error,
        ], 'marketing'));
    }

    private function renderVerifyFailed(Response $response, string $error): void
    {
        $response->html($this->view('auth/verify-failed', [
            'title'      => 'Verification failed',
            'error'      => $error,
            'csrf_token' => CSRF::generate(),
        ], 'marketing'));
    }

    private function renderResetFailed(Response $response, string $error): void
    {
        $response->html($this->view('auth/reset-failed', [
            'title'      => 'Reset link invalid',
            'error'      => $error,
        ], 'marketing'));
    }
}
