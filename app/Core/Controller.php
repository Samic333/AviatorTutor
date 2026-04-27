<?php
/**
 * Base Controller
 *
 * Abstract base class for all controllers with common methods
 */

namespace App\Core;

abstract class Controller
{
    /**
     * Current request
     */
    protected Request $request;

    /**
     * Current response
     */
    protected Response $response;

    /**
     * View renderer
     */
    protected View $view;

    /**
     * Authentication manager
     */
    protected Auth $auth;

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request ?? new Request();
        $this->response = $response ?? new Response();
        $this->view = new View();
        $this->auth = new Auth();
    }

    /**
     * Render a view template with layout
     *
     * @param string $template Template name (e.g., 'dashboard/index')
     * @param array $data Data to pass to template
     * @param string $layout Layout name (default: 'app')
     * @return string Rendered HTML
     */
    protected function view(string $template, array $data = [], string $layout = 'app'): string
    {
        // Auto-inject current user and path into every view
        $data = array_merge([
            'currentUser' => Auth::user(),
            'currentPath' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
        ], $data);
        return $this->view->renderWithLayout($template, $layout, $data);
    }

    /**
     * Render without layout
     *
     * @param string $template Template name
     * @param array $data Data to pass
     * @return string
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }

    /**
     * Send response with view
     *
     * @param string $template Template name
     * @param array $data Data to pass
     * @param string $layout Layout name
     * @return void
     */
    protected function renderView(string $template, array $data = [], string $layout = 'app'): void
    {
        $html = $this->view($template, $data, $layout);
        $this->response->html($html);
    }

    /**
     * Redirect to URL
     *
     * @param string $url Target URL
     * @param int $code HTTP status code
     * @return void
     */
    protected function redirect(string $url, int $code = 302): void
    {
        $this->response->redirect($url, $code);
    }

    /**
     * Redirect back to previous page
     *
     * @return void
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    /**
     * Send JSON response
     *
     * @param mixed $data Data to encode
     * @param int $code HTTP status code
     * @return void
     */
    protected function json(mixed $data, int $code = 200): void
    {
        $this->response->json($data, $code);
    }

    /**
     * Send success JSON response
     *
     * @param mixed $data Data to send
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return void
     */
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): void
    {
        $response = ['message' => $message, 'success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->response->json($response, $code);
    }

    /**
     * Send error JSON response
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @return void
     */
    protected function error(string $message, int $code = 400): void
    {
        $this->response->json(['message' => $message, 'error' => true], $code);
    }

    /**
     * Set response status code
     *
     * @param int $code HTTP status code
     * @return Response
     */
    protected function status(int $code): Response
    {
        return $this->response->status($code);
    }

    /**
     * Get request input
     *
     * @param string $key Input name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    /**
     * Get all request input
     *
     * @return array
     */
    protected function all(): array
    {
        return $this->request->all();
    }

    /**
     * Get query parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $this->request->query($key, $default);
    }

    /**
     * Get route parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function param(string $key, mixed $default = null): mixed
    {
        return $this->request->param($key, $default);
    }

    /**
     * Check if request has input
     *
     * @param string $key Input name
     * @return bool
     */
    protected function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * Get uploaded file
     *
     * @param string $key File input name
     * @return array|null
     */
    protected function file(string $key): ?array
    {
        return $this->request->file($key);
    }

    /**
     * Check if file was uploaded
     *
     * @param string $key File input name
     * @return bool
     */
    protected function hasFile(string $key): bool
    {
        return $this->request->hasFile($key);
    }

    /**
     * Check CSRF token (for POST requests)
     *
     * @return bool
     */
    protected function checkCsrf(): bool
    {
        return CSRF::check($this->request);
    }

    /**
     * Get current authenticated user
     *
     * @return array|null
     */
    protected function user(): ?array
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        return Auth::isAdmin();
    }

    /**
     * Require authentication
     *
     * @param string $redirectUrl Optional redirect URL after login
     * @return void
     */
    protected function requireAuth(?string $redirectUrl = null): void
    {
        Auth::guard($redirectUrl ?? $this->request->fullUrl());
    }

    /**
     * Require admin role
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        Auth::guardAdmin();
    }
}
