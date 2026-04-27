<?php
/**
 * PHP Built-in Server Router
 *
 * Routes all requests through index.php, while serving
 * static assets (CSS, JS, images) directly from disk.
 *
 * Usage: php -S localhost:8080 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly (CSS, JS, images, fonts, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Let the built-in server handle real files
    return false;
}

// Everything else goes through index.php
require_once __DIR__ . '/index.php';
