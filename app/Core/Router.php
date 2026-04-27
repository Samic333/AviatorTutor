<?php
/**
 * URL Router
 *
 * Simple router supporting GET/POST routes with named parameters
 * Pattern: /systems/{id} matches /systems/123 and sets id=123
 */

namespace App\Core;

class Router
{
    /**
     * Registered routes
     */
    private array $routes = [];

    /**
     * Current request
     */
    private Request $request;

    /**
     * Current response
     */
    private Response $response;

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Register a GET route
     *
     * @param string $path Route path (e.g., /systems/{id})
     * @param string $handler Controller@method format
     * @return self
     */
    public function get(string $path, string $handler): self
    {
        return $this->register('GET', $path, $handler);
    }

    /**
     * Register a POST route
     *
     * @param string $path Route path
     * @param string $handler Controller@method format
     * @return self
     */
    public function post(string $path, string $handler): self
    {
        return $this->register('POST', $path, $handler);
    }

    /**
     * Register a route for multiple methods
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Route path
     * @param string $handler Controller@method format
     * @return self
     */
    private function register(string $method, string $path, string $handler): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'pattern' => $this->pathToPattern($path),
            'handler' => $handler,
        ];
        return $this;
    }

    /**
     * Convert route path to regex pattern
     *
     * Converts /systems/{id} to a regex that matches /systems/123
     * and extracts id=123
     *
     * @param string $path Route path with placeholders
     * @return string Regex pattern
     */
    private function pathToPattern(string $path): string
    {
        // Escape special regex characters except {}
        $pattern = preg_quote($path, '#');

        // Replace {param} with capture group
        $pattern = preg_replace(
            '#\\\{(\w+)\\\}#',
            '(?<\1>[^/]+)',
            $pattern
        );

        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the request to matching route
     *
     * @return void
     */
    public function dispatch(): void
    {
        $path = $this->request->path();
        $method = $this->request->method();

        foreach ($this->routes as $route) {
            // Check method and path match
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            // Extract named parameters
            $params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
            $this->request->setParams($params);

            // Dispatch to controller
            $this->callHandler($route['handler']);
            return;
        }

        // Route not found
        $this->response->status(404);
        $this->response->html('<h1>404 Not Found</h1>', 404);
    }

    /**
     * Call the handler for a route
     *
     * @param string $handler Handler string (Controller@method)
     * @return void
     */
    private function callHandler(string $handler): void
    {
        if (strpos($handler, '@') === false) {
            throw new \Exception("Invalid handler format: {$handler}. Use Controller@method");
        }

        [$controllerName, $methodName] = explode('@', $handler);

        // Build full controller class name
        $className = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($className)) {
            throw new \Exception("Controller not found: {$className}");
        }

        // Instantiate controller with the shared request/response (so $this->param() etc. work)
        $controller = new $className($this->request, $this->response);

        // Check method exists
        if (!method_exists($controller, $methodName)) {
            throw new \Exception("Method {$methodName} not found in {$className}");
        }

        // Call controller method
        $controller->$methodName($this->request, $this->response);
    }

    /**
     * Get the request object
     *
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * Get the response object
     *
     * @return Response
     */
    public function response(): Response
    {
        return $this->response;
    }

    /**
     * Redirect to a URL (static helper)
     *
     * @param string $url Target URL
     * @param int $code HTTP status code
     * @return void
     */
    public static function redirect(string $url, int $code = 302): void
    {
        http_response_code($code);
        header("Location: {$url}");
        exit;
    }

    /**
     * Get all registered routes (for debugging)
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
