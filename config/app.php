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

        // Overhaul flags (May 2026). Each new UI piece checks its flag and
        // falls back to the existing surface when off. Flip in app.local.php
        // per environment to roll out incrementally.
        'nav_my_subjects'   => false, // Phase 1 — My Subjects sidebar item + page
        'study_chrome_v2'   => false, // Phase 2 — single top bar, no sidebars
        'flashcards_v2'     => false, // Phase 3 — color-coded + SM2 swipe
        'mnemonics_v2'      => false, // Phase 3 — DB-backed mnemonics with explanations
        'mind_map'          => false, // Phase 3 — mind map study mode
        'deep_notes'        => false, // Phase 3 — full-text deep notes mode
        'theme_drawer'      => false, // Phase 3 — settings drawer + themes
        'dashboard_v2'      => false, // Phase 4 — compact KPIs + promo panel
        'system_picker_v2'  => false, // Phase 4 — searchable/grouped system picker
        'add_subject_flow'  => false, // Phase 4 — Add Subject request modal
        'analytics_v1'      => false, // Phase 5 — analytics dashboard
        'friendly_errors'   => false, // F2 — friendly 500 page + retry
    ],

    // ---------------------------------------------------------------
    // AI providers (Phase 6: multi-provider).
    // ---------------------------------------------------------------
    // Default provider used when admin doesn't pick one explicitly.
    // Allowed: 'anthropic' | 'openai' | 'gemini'.
    'ai_default_provider' => 'anthropic',

    // ---- Anthropic Claude ----
    'anthropic_api_key'      => '', // REQUIRED in app.local.php to use this provider
    'anthropic_api_url'      => 'https://api.anthropic.com/v1/messages',
    'anthropic_api_version'  => '2023-06-01',
    'anthropic_model_outline' => 'claude-opus-4-5',
    'anthropic_model_chunk'   => 'claude-sonnet-4-5',
    'anthropic_max_tokens'    => 16000,
    'anthropic_chunk_chars'   => 80000,
    'anthropic_timeout'       => 120,

    // ---- OpenAI GPT ----
    'openai_api_key'      => '', // sk-…  set in app.local.php to enable
    'openai_api_url'      => 'https://api.openai.com/v1/chat/completions',
    'openai_model_outline' => 'gpt-4.1',
    'openai_model_chunk'   => 'gpt-4.1-mini',
    'openai_max_tokens'    => 8192,
    'openai_timeout'       => 120,

    // ---- Google Gemini ----
    // Note the trailing slash on api_url — the model id is appended.
    'gemini_api_key'      => '', // AIza…  set in app.local.php to enable
    'gemini_api_url'      => 'https://generativelanguage.googleapis.com/v1beta/models/',
    'gemini_model_outline' => 'gemini-2.5-pro',
    'gemini_model_chunk'   => 'gemini-2.5-flash',
    'gemini_max_tokens'    => 8192,
    'gemini_timeout'       => 120,
];

$localPath = __DIR__ . '/app.local.php';
if (is_file($localPath)) {
    $local = require $localPath;
    if (is_array($local)) {
        return array_replace($defaults, $local);
    }
}

return $defaults;
