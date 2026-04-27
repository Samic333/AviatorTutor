<?php
/**
 * Application configuration.
 *
 * Production override: create config/app.local.php (gitignored) returning
 * an array with keys to override these defaults. Typical production values:
 *   [
 *     'debug'          => false,
 *     'base_url'       => 'https://aviatortutor.com',
 *     'encryption_key' => '...32+ random chars...',
 *   ]
 */

$defaults = [
    'name'    => 'AviatorTutor',
    'version' => '1.0.0',

    // Debug mode – shows full stack traces; set false in production
    'debug' => true,

    'timezone' => 'UTC',

    // Public origin (no trailing slash). Empty = serve from doc root.
    // Production: 'https://aviatortutor.com'.
    'base_url' => '',

    // Session
    'session_name'     => 'aviatortutor_session',
    'session_lifetime' => 7200,

    // Storage paths (relative to project root)
    'upload_path' => __DIR__ . '/../public/assets/uploads',
    'pdf_path'    => __DIR__ . '/../public/assets/uploads/pdfs',
    'log_path'    => __DIR__ . '/../storage/logs',

    'per_page'      => 15,
    'csrf_enabled'  => true,

    // 32+ chars; override per environment.
    'encryption_key' => 'aviatortutor-local-key-change-in-prod-please',

    // Contact form / outbound mail
    'mail_from'   => 'no-reply@aviatortutor.com',
    'contact_to'  => 'samickenya@gmail.com',
];

$localPath = __DIR__ . '/app.local.php';
if (is_file($localPath)) {
    $local = require $localPath;
    if (is_array($local)) {
        return array_replace($defaults, $local);
    }
}

return $defaults;
