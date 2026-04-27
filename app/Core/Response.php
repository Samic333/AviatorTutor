<?php
/**
 * HTTP Response Helper
 *
 * Manages response headers, content, redirects, and JSON responses
 */

namespace App\Core;

class Response
{
    /**
     * Response status code
     */
    private int $statusCode = 200;

    /**
     * Response headers
     */
    private array $headers = [];

    /**
     * Response body
     */
    private string $body = '';

    /**
     * Status messages
     */
    private static array $statusMessages = [
        200 => 'OK',
        201 => 'Created',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        header_remove('X-Powered-By');
    }

    /**
     * Set response status code
     *
     * @param int $code HTTP status code
     * @return self
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get response status code
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->statusCode;
    }

    /**
     * Set response header
     *
     * @param string $key Header name
     * @param string $value Header value
     * @return self
     */
    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set content type header
     *
     * @param string $type Content type
     * @return self
     */
    public function contentType(string $type): self
    {
        return $this->header('Content-Type', $type);
    }

    /**
     * Set response body
     *
     * @param string $body Response body
     * @return self
     */
    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Send redirect response
     *
     * @param string $url Target URL
     * @param int $code HTTP status code (301 or 302)
     * @return void
     */
    public function redirect(string $url, int $code = 302): void
    {
        $this->status($code);
        $this->header('Location', $url);
        $this->send();
        exit;
    }

    /**
     * Send JSON response
     *
     * @param mixed $data Data to encode
     * @param int $code HTTP status code
     * @return void
     */
    public function json(mixed $data, int $code = 200): void
    {
        $this->status($code);
        $this->contentType('application/json');
        $this->body(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->send();
    }

    /**
     * Send HTML response
     *
     * @param string $html HTML content
     * @param int $code HTTP status code
     * @return void
     */
    public function html(string $html, int $code = 200): void
    {
        $this->status($code);
        $this->contentType('text/html; charset=utf-8');
        $this->body($html);
        $this->send();
    }

    /**
     * Send error response
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @return void
     */
    public function error(string $message, int $code = 500): void
    {
        $this->json(['error' => $message], $code);
    }

    /**
     * Send success response
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return void
     */
    public function success(mixed $data = null, string $message = 'Success'): void
    {
        $response = ['message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->json($response, 200);
    }

    /**
     * Send response to client
     *
     * @return void
     */
    public function send(): void
    {
        // Send status header
        http_response_code($this->statusCode);

        // Send custom headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Send body
        if ($this->body !== '') {
            echo $this->body;
        }
    }

    /**
     * Check if headers have been sent
     *
     * @return bool
     */
    public static function headersSent(): bool
    {
        return headers_sent();
    }
}
