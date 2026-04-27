<?php
/**
 * Application Configuration
 * Q400 Aircraft Systems Study App
 *
 * base_url: set this to match where the app is served.
 * Example: If Apache serves /q400-study/public → 'http://localhost/q400-study/public'
 *          If the app root IS the document root → '' (empty string)
 */

return [
    // Application name and version
    'name'    => 'Q400 System Study',
    'version' => '1.0.0',

    // Debug mode – shows full stack traces; set false in production
    'debug' => true,

    // Application timezone
    'timezone' => 'UTC',

    // Base URL (no trailing slash).
    // Leave empty if public/ is your document root.
    // Set to 'http://localhost/q400-study/public' for standard XAMPP/WAMP setups.
    'base_url' => '',

    // Session configuration
    'session_name'     => 'q400_study_session',
    'session_lifetime' => 7200, // 2 hours

    // Upload storage (relative to project root)
    'upload_path' => __DIR__ . '/../public/assets/uploads',
    'pdf_path'    => __DIR__ . '/../public/assets/uploads/pdfs',
    'log_path'    => __DIR__ . '/../storage/logs',

    // Pagination
    'per_page' => 15,

    // CSRF protection
    'csrf_enabled' => true,

    // Change this in production – must be 32+ chars
    'encryption_key' => 'q400-study-local-key-change-in-prod',
];
