<?php
/**
 * Authentication Controller
 *
 * Handles user login, registration, and logout
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;

class AuthController extends Controller
{
    /**
     * Show login form
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function loginForm(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $response->redirect('/dashboard');
            return;
        }

        $data = [
            'title' => 'Login',
            'csrf_token' => CSRF::generate(),
        ];

        $html = $this->view('auth/login', $data);
        $response->html($html);
    }

    /**
     * Handle login request
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function login(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $this->loginFormWithError($response, 'CSRF token validation failed');
            return;
        }

        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($email) || empty($password)) {
            $this->loginFormWithError($response, 'Email and password are required');
            return;
        }

        $db = DB::instance();
        $user = $db->queryOne(
            'SELECT id, name, email, password_hash, role, study_streak FROM users WHERE email = ?',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->loginFormWithError($response, 'Invalid email or password');
            return;
        }

        Auth::login($user['id'], [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'study_streak' => $user['study_streak'],
        ]);

        CSRF::regenerate();
        $response->redirect('/dashboard');
    }

    /**
     * Show registration form
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function registerForm(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $response->redirect('/dashboard');
            return;
        }

        $data = [
            'title' => 'Register',
            'csrf_token' => CSRF::generate(),
        ];

        $html = $this->view('auth/register', $data);
        $response->html($html);
    }

    /**
     * Handle registration request
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function register(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $this->registerFormWithError($response, 'CSRF token validation failed');
            return;
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        if (empty($name) || empty($email) || empty($password)) {
            $this->registerFormWithError($response, 'All fields are required');
            return;
        }

        if (strlen($password) < 8) {
            $this->registerFormWithError($response, 'Password must be at least 8 characters');
            return;
        }

        if ($password !== $password_confirm) {
            $this->registerFormWithError($response, 'Passwords do not match');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->registerFormWithError($response, 'Invalid email format');
            return;
        }

        $db = DB::instance();
        $existingUser = $db->queryOne('SELECT id FROM users WHERE email = ?', [$email]);

        if ($existingUser) {
            $this->registerFormWithError($response, 'Email already registered');
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $db->insert(
            'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())',
            [$name, $email, $passwordHash, 'learner']
        );

        if (!$userId) {
            $this->registerFormWithError($response, 'Registration failed. Please try again.');
            return;
        }

        Auth::login((int)$userId, [
            'name' => $name,
            'email' => $email,
            'role' => 'learner',
            'study_streak' => 0,
        ]);

        CSRF::regenerate();
        $response->redirect('/dashboard');
    }

    /**
     * Handle logout request
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function logout(Request $request, Response $response): void
    {
        Auth::logout();
        $response->redirect('/login');
    }

    /**
     * Helper to render login form with error
     */
    private function loginFormWithError(Response $response, string $error): void
    {
        $data = [
            'title' => 'Login',
            'csrf_token' => CSRF::generate(),
            'error' => $error,
        ];

        $html = $this->view('auth/login', $data);
        $response->html($html);
    }

    /**
     * Helper to render register form with error
     */
    private function registerFormWithError(Response $response, string $error): void
    {
        $data = [
            'title' => 'Register',
            'csrf_token' => CSRF::generate(),
            'error' => $error,
        ];

        $html = $this->view('auth/register', $data);
        $response->html($html);
    }
}
