<?php
/**
 * HTTP Request Wrapper
 *
 * Encapsulates HTTP request data (GET, POST, FILES, SERVER)
 */

namespace App\Core;

class Request
{
    /**
     * GET parameters
     */
    private array $get;

    /**
     * POST parameters
     */
    private array $post;

    /**
     * FILES array
     */
    private array $files;

    /**
     * SERVER array
     */
    private array $server;

    /**
     * Parsed route parameters
     */
    private array $params = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get request path
     *
     * @return string
     */
    public function path(): string
    {
        $path = $this->server['REQUEST_URI'] ?? '/';

        // Remove query string
        if (strpos($path, '?') !== false) {
            $path = substr($path, 0, strpos($path, '?'));
        }

        // Remove base path
        $basePath = dirname($this->server['SCRIPT_NAME'] ?? '');
        if ($basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        return '/' . ltrim($path, '/');
    }

    /**
     * Get query string parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get POST parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get all POST parameters
     *
     * @return array
     */
    public function all(): array
    {
        return $this->post;
    }

    /**
     * Check if POST parameter exists
     *
     * @param string $key Parameter name
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->post[$key]);
    }

    /**
     * Get file from FILES array
     *
     * @param string $key File input name
     * @return array|null
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if file was uploaded
     *
     * @param string $key File input name
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Get all route parameters
     *
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Get specific route parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Set route parameters (used by router)
     *
     * @param array $params Route parameters
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Check if request is AJAX
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    /**
     * Check if request is POST
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * Check if request is GET
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    /**
     * Get request header
     *
     * @param string $key Header name
     * @param mixed $default Default value
     * @return mixed
     */
    public function header(string $key, mixed $default = null): mixed
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get HTTP host
     *
     * @return string
     */
    public function host(): string
    {
        return $this->server['HTTP_HOST'] ?? 'localhost';
    }

    /**
     * Get protocol (http or https)
     *
     * @return string
     */
    public function protocol(): string
    {
        if (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') {
            return 'https';
        }
        return 'http';
    }

    /**
     * Get full URL
     *
     * @return string
     */
    public function fullUrl(): string
    {
        return $this->protocol() . '://' . $this->host() . $this->server['REQUEST_URI'];
    }
}
