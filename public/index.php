<?php
/**
 * Q400 Aircraft Systems Study App
 * Front Controller - Entry Point
 *
 * All requests are routed through this file via .htaccess
 */

// Start session
session_name('q400_study_session');
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Error handling
if (php_sapi_name() !== 'cli') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Set timezone
date_default_timezone_set('UTC');

// Autoload classes using PSR-4
spl_autoload_register(function ($class) {
    // PSR-4 namespace prefix
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load core classes
require_once BASE_PATH . '/app/Core/DB.php';
require_once BASE_PATH . '/app/Core/Request.php';
require_once BASE_PATH . '/app/Core/Response.php';
require_once BASE_PATH . '/app/Core/Router.php';
require_once BASE_PATH . '/app/Core/Auth.php';
require_once BASE_PATH . '/app/Core/CSRF.php';
require_once BASE_PATH . '/app/Core/View.php';
require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Core/Model.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

try {
    // Create request and response objects
    $request = new Request();
    $response = new Response();

    // Initialize router
    $router = new Router($request, $response);

    // Load route definitions
    require_once BASE_PATH . '/routes/web.php';

    // Dispatch the request
    $router->dispatch();
} catch (\Exception $e) {
    $config = require BASE_PATH . '/config/app.php';

    if ($config['debug']) {
        // In debug mode, show full error
        http_response_code(500);
        echo '<pre>';
        echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo '<strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n\n";
        echo '<strong>Stack Trace:</strong>' . "\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        // In production, log error and show generic message
        error_log($e->getMessage());
        http_response_code(500);
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>An error occurred while processing your request.</p>';
    }
}
