<?php
/**
 * Q400 Aircraft Systems Study App
 * Front Controller - Entry Point
 *
 * All requests are routed through this file via .htaccess
 */

// 301 redirects from the OLD instructor-marketplace concept → new platform.
// This runs BEFORE session_start so search engines crawling old URLs aren't
// hit with cookies. Match against the parsed path only.
$__path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$__legacyMap = [
    '/instructors'  => '/',
    '/classes'      => '/',
    '/student'      => '/dashboard',
    '/instructor'   => '/dashboard',
];
foreach ($__legacyMap as $prefix => $target) {
    if ($__path === $prefix || str_starts_with($__path, $prefix . '/')) {
        header('Location: ' . $target, true, 301);
        header('Cache-Control: public, max-age=86400');
        exit;
    }
}
unset($__path, $__legacyMap, $prefix, $target);

// Start session
session_name('aviatortutor_session');
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

// Composer autoload (optional — present after `composer install`).
// We only require it here if the file exists so the project still boots
// on a server that hasn't run composer yet.
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

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
} catch (\Throwable $e) {
    $config = require BASE_PATH . '/config/app.php';

    // Generate a request id so a learner can quote it back to support and
    // we can find the matching log entry. 8 hex chars is plenty.
    $requestId = bin2hex(random_bytes(4));

    // Always log the trace, even when debug is off — production was
    // previously only logging $e->getMessage() with no trace, which made
    // root-causing the HTTP 500 on first slide much harder than it should
    // have been.
    $logDir  = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
    $logLine = sprintf(
        "[%s] req=%s %s %s\n  %s in %s:%d\n%s\n",
        date('c'),
        $requestId,
        $_SERVER['REQUEST_METHOD'] ?? '-',
        $_SERVER['REQUEST_URI']    ?? '-',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents($logDir . '/errors.log', $logLine, FILE_APPEND);
    error_log($e->getMessage());

    http_response_code(500);

    // Friendly error page when the flag is on, OR always in production
    // (no excuse for a bare "500 Internal Server Error" wall to a learner).
    $useFriendly = !empty($config['features']['friendly_errors']) || empty($config['debug']);
    $errorView   = BASE_PATH . '/views/errors/friendly-500.php';
    if ($useFriendly && is_file($errorView)) {
        $debugMessage = !empty($config['debug'])
            ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            : null;
        $debugTrace   = !empty($config['debug']) ? $e->getTraceAsString() : null;
        require $errorView;
    } elseif (!empty($config['debug'])) {
        // Legacy <pre> trace for fastest local debugging.
        echo '<pre>';
        echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo '<strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n\n";
        echo '<strong>Request ID:</strong> ' . htmlspecialchars($requestId) . "\n\n";
        echo '<strong>Stack Trace:</strong>' . "\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>Request ID: ' . htmlspecialchars($requestId) . '</p>';
    }
}
