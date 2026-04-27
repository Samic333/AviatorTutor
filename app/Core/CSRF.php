<?php
/**
 * CSRF Protection
 *
 * Generate and validate CSRF tokens to protect against cross-site request forgery
 */

namespace App\Core;

class CSRF
{
    /**
     * Session key for CSRF token
     */
    private const TOKEN_SESSION_KEY = 'csrf_token';

    /**
     * CSRF token length
     */
    private const TOKEN_LENGTH = 32;

    /**
     * Generate or get existing CSRF token
     *
     * @return string
     */
    public static function generate(): string
    {
        if (isset($_SESSION[self::TOKEN_SESSION_KEY])) {
            return $_SESSION[self::TOKEN_SESSION_KEY];
        }

        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::TOKEN_SESSION_KEY] = $token;

        return $token;
    }

    /**
     * Get current CSRF token
     *
     * @return string|null
     */
    public static function token(): ?string
    {
        return $_SESSION[self::TOKEN_SESSION_KEY] ?? null;
    }

    /**
     * Verify a CSRF token
     *
     * @param string $token Token to verify
     * @return bool
     */
    public static function verify(string $token): bool
    {
        $sessionToken = $_SESSION[self::TOKEN_SESSION_KEY] ?? null;

        if ($sessionToken === null) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Regenerate token (call after login for security)
     *
     * @return string New token
     */
    public static function regenerate(): string
    {
        unset($_SESSION[self::TOKEN_SESSION_KEY]);
        return self::generate();
    }

    /**
     * Return hidden input field for forms
     *
     * @return string HTML for hidden input
     */
    public static function field(): string
    {
        $token = self::generate();
        return sprintf(
            '<input type="hidden" name="csrf_token" value="%s">',
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Middleware check - verify token from POST data or header
     *
     * Call this at the start of POST request handlers
     *
     * @param Request $request
     * @return bool
     */
    public static function check(Request $request): bool
    {
        if (!$request->isPost()) {
            return true;
        }

        // Get token from POST data or X-CSRF-Token header
        $token = $request->input('csrf_token') ?? $request->header('X-CSRF-Token');

        if ($token === null) {
            return false;
        }

        return self::verify($token);
    }

    /**
     * Verify with automatic error on failure
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public static function checkOrFail(Request $request, Response $response): bool
    {
        if (!self::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return false;
        }
        return true;
    }
}
