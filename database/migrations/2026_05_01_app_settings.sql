-- =============================================================================
-- AviatorTutor — Phase 6: app_settings table for admin-editable config
--
-- Generic key/value store for runtime-editable settings (API keys, default
-- provider, model overrides, etc.). The is_secret flag tells the admin UI
-- to mask the value after save (only shows last 4 chars).
--
-- AppSetting service reads from this table first, falls back to the
-- gitignored config/app.local.php for back-compat with the cutover-era
-- deploy where keys lived in PHP only.
-- =============================================================================

CREATE TABLE IF NOT EXISTS app_settings (
    setting_key    VARCHAR(100)    NOT NULL PRIMARY KEY,
    setting_value  LONGTEXT        NULL,
    is_secret      TINYINT(1)      NOT NULL DEFAULT 0,
    updated_at     DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by     BIGINT UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
