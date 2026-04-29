<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;

class ProfileController extends Controller
{
    public function show(Request $request, Response $response): void
    {
        $this->requireAuth();
        $userId = (int)($this->user()['id'] ?? 0);
        $user   = DB::instance()->queryOne('SELECT * FROM users WHERE id = ?', [$userId]);
        if (!$user) { $this->redirect('/login'); return; }

        $response->html($this->view('pilot/profile', [
            'title'      => 'Profile',
            'user'       => $user,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'pilot'));
    }

    public function update(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/profile');
            return;
        }

        $userId = (int)($this->user()['id'] ?? 0);
        $name   = trim((string)$this->input('name', ''));
        $email  = trim((string)$this->input('email', ''));

        if ($name === '' || $email === '') {
            $_SESSION['flash_error'] = 'Name and email are required.';
            $this->redirect('/profile');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Invalid email address.';
            $this->redirect('/profile');
            return;
        }

        // Check email not taken by another user
        $existing = DB::instance()->queryOne(
            'SELECT id FROM users WHERE email = ? AND id != ?',
            [$email, $userId]
        );

        if ($existing) {
            $_SESSION['flash_error'] = 'That email is already in use by another account.';
            $this->redirect('/profile');
            return;
        }

        DB::instance()->execute(
            'UPDATE users SET name = ?, email = ? WHERE id = ?',
            [$name, $email, $userId]
        );

        // Refresh session with new name/email
        Auth::update(['name' => $name, 'email' => $email]);

        $_SESSION['flash_ok'] = 'Profile updated successfully.';
        $this->redirect('/profile');
    }

    public function avatar(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $this->json(['error' => 'CSRF'], 419);
            return;
        }

        $userId = (int)($this->user()['id'] ?? 0);
        $file   = $this->file('avatar');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Upload failed. Please try again.';
            $this->redirect('/profile');
            return;
        }

        // Validate image
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime    = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            $_SESSION['flash_error'] = 'Only JPEG, PNG, WebP, or GIF images are allowed.';
            $this->redirect('/profile');
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['flash_error'] = 'Image must be under 5 MB.';
            $this->redirect('/profile');
            return;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $userId . '_' . time() . '.jpg';
        $dest     = $uploadDir . $filename;

        // Convert to JPEG for consistency
        $image = match ($mime) {
            'image/png'  => imagecreatefrompng($file['tmp_name']),
            'image/webp' => imagecreatefromwebp($file['tmp_name']),
            'image/gif'  => imagecreatefromgif($file['tmp_name']),
            default      => imagecreatefromjpeg($file['tmp_name']),
        };

        if ($image === false) {
            $_SESSION['flash_error'] = 'Could not process image.';
            $this->redirect('/profile');
            return;
        }

        // Resize to 200x200
        $resized = imagescale($image, 200, 200, IMG_BICUBIC);
        imagejpeg($resized ?: $image, $dest, 90);
        imagedestroy($image);

        DB::instance()->execute('UPDATE users SET avatar = ? WHERE id = ?', [$filename, $userId]);
        Auth::update(['avatar' => $filename]);

        $_SESSION['flash_ok'] = 'Avatar updated.';
        $this->redirect('/profile');
    }

    public function passwordForm(Request $request, Response $response): void
    {
        $this->requireAuth();
        $response->html($this->view('pilot/password', [
            'title'      => 'Change Password',
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'pilot'));
    }

    public function passwordChange(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/profile/password');
            return;
        }

        $userId      = (int)($this->user()['id'] ?? 0);
        $current     = (string)$this->input('current_password', '');
        $new         = (string)$this->input('new_password', '');
        $confirm     = (string)$this->input('confirm_password', '');

        if ($current === '' || $new === '' || $confirm === '') {
            $_SESSION['flash_error'] = 'All fields are required.';
            $this->redirect('/profile/password');
            return;
        }

        if (strlen($new) < 8) {
            $_SESSION['flash_error'] = 'New password must be at least 8 characters.';
            $this->redirect('/profile/password');
            return;
        }

        if ($new !== $confirm) {
            $_SESSION['flash_error'] = 'Passwords do not match.';
            $this->redirect('/profile/password');
            return;
        }

        $user = DB::instance()->queryOne('SELECT password_hash FROM users WHERE id = ?', [$userId]);
        if (!$user || !password_verify($current, (string)$user['password_hash'])) {
            $_SESSION['flash_error'] = 'Current password is incorrect.';
            $this->redirect('/profile/password');
            return;
        }

        $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
        DB::instance()->execute('UPDATE users SET password_hash = ? WHERE id = ?', [$hash, $userId]);

        $_SESSION['flash_ok'] = 'Password changed successfully.';
        $this->redirect('/profile/password');
    }
}
