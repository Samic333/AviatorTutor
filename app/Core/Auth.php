<?php
/**
 * Authentication Manager
 *
 * Handles user login, logout, and authentication state
 */

namespace App\Core;

class Auth
{
    /**
     * Session key for user data
     */
    private const USER_SESSION_KEY = 'auth_user';

    /**
     * User roles
     */
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * Login user - store in session
     *
     * @param int $userId User ID
     * @param array $data Additional user data (name, email, role, etc.)
     * @return bool
     */
    public static function login(int $userId, array $data = []): bool
    {
        $user = array_merge(
            ['id' => $userId],
            $data,
            ['logged_in_at' => time()]
        );

        $_SESSION[self::USER_SESSION_KEY] = $user;
        return true;
    }

    /**
     * Logout user - destroy session
     *
     * @return bool
     */
    public static function logout(): bool
    {
        if (isset($_SESSION[self::USER_SESSION_KEY])) {
            unset($_SESSION[self::USER_SESSION_KEY]);
        }
        session_destroy();
        return true;
    }

    /**
     * Get current authenticated user
     *
     * @return array|null User array or null if not authenticated
     */
    public static function user(): ?array
    {
        return $_SESSION[self::USER_SESSION_KEY] ?? null;
    }

    /**
     * Get current user ID
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    /**
     * Get current user by a specific key
     *
     * @param string $key User data key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $user = self::user();
        return $user[$key] ?? $default;
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public static function check(): bool
    {
        return isset($_SESSION[self::USER_SESSION_KEY]);
    }

    /**
     * Check if user is a guest (not authenticated)
     *
     * @return bool
     */
    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Check if user has admin role
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::get('role') === self::ROLE_ADMIN;
    }

    /**
     * Check if user has a specific role
     *
     * @param string $role Role name
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        return self::get('role') === $role;
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles Role names
     * @return bool
     */
    public static function hasAnyRole(array $roles): bool
    {
        return in_array(self::get('role'), $roles);
    }

    /**
     * Guard - redirect to login if not authenticated
     *
     * @param string $redirectUrl URL to redirect to after login
     * @return void
     */
    public static function guard(?string $redirectUrl = null): void
    {
        if (self::guest()) {
            $loginUrl = '/login';
            if ($redirectUrl) {
                $loginUrl .= '?redirect=' . urlencode($redirectUrl);
            }
            header("Location: {$loginUrl}");
            exit;
        }
    }

    /**
     * Guard - redirect to login if not admin
     *
     * @return void
     */
    public static function guardAdmin(): void
    {
        self::guard();

        if (!self::isAdmin()) {
            http_response_code(403);
            echo '<h1>403 Forbidden</h1>';
            exit;
        }
    }

    /**
     * Update user data in session
     *
     * @param array $data Data to update
     * @return bool
     */
    public static function update(array $data): bool
    {
        if (!self::check()) {
            return false;
        }

        $_SESSION[self::USER_SESSION_KEY] = array_merge(
            $_SESSION[self::USER_SESSION_KEY],
            $data
        );

        return true;
    }

    /**
     * Attempt login with credentials (to be implemented with database)
     * This is a placeholder for use with your User model
     *
     * @param string $email Email address
     * @param string $password Plain text password
     * @return bool
     */
    public static function attempt(string $email, string $password): bool
    {
        // This would be implemented in your controller with the User model
        // Example:
        // $user = User::where('email', '=', $email)->first();
        // if ($user && password_verify($password, $user->password_hash)) {
        //     self::login($user->id, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role]);
        //     return true;
        // }
        // return false;

        return false;
    }
}
