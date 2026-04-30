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
    'mail_from'      => 'no-reply@aviatortutor.com',
    'mail_from_name' => 'AviatorTutor',
    'contact_to'     => 'samickenya@gmail.com',

    // Auth
    'require_email_verification' => true, // set false to let unverified users log in
    'verification_token_ttl_hours' => 48,
    'password_reset_ttl_hours' => 2,

    // Feature flags — toggle UI elements that aren't ready to ship yet so
    // the navigation never shows "Coming Soon" placeholders that learners
    // can't actually use. Flip to true once the underlying feature lands.
    'features' => [
        'planner' => false, // /planner placeholder
        'notes'   => false, // /notes placeholder
        'search'  => true,  // SearchController is wired and useful today
        'ai_admin_test' => true, // /admin/ai-test smoke-test page (Phase 2)
    ],

    // Anthropic Claude API configuration. The API key MUST be set in
    // config/app.local.php (gitignored) — never commit it. The other keys
    // here are sensible defaults; override per environment if needed.
    'anthropic_api_key'      => '', // REQUIRED in app.local.php
    'anthropic_api_url'      => 'https://api.anthropic.com/v1/messages',
    'anthropic_api_version'  => '2023-06-01',
    // Model identifiers — bump these to the current generation when needed.
    // Outline pass uses the more capable model; per-chunk slide generation
    // uses the cheaper one. For Phase 2 (smoke test) only the chunk model
    // is exercised.
    'anthropic_model_outline' => 'claude-opus-4-5',
    'anthropic_model_chunk'   => 'claude-sonnet-4-5',
    'anthropic_max_tokens'    => 16000,
    // Per-chunk text size for the eventual chunked-PDF pipeline.
    'anthropic_chunk_chars'   => 80000,
    // HTTP timeout (seconds) for the API call. Generation can take a while
    // for big prompts — bump if you hit time-outs.
    'anthropic_timeout'       => 120,
];

$localPath = __DIR__ . '/app.local.php';
if (is_file($localPath)) {
    $local = require $localPath;
    if (is_array($local)) {
        return array_replace($defaults, $local);
    }
}

return $defaults;
