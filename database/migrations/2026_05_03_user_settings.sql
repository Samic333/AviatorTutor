-- =============================================================================
-- AviatorTutor — Phase 3 (overhaul) migration
-- Per-user reading preferences. Persisted server-side so settings follow the
-- learner across devices (the per-device localStorage mirror is still the
-- pre-FOUC source of truth on first paint, but logged-in pages reconcile to
-- this row on every request).
-- =============================================================================

CREATE TABLE IF NOT EXISTS user_settings (
    user_id        BIGINT UNSIGNED PRIMARY KEY,
    theme          VARCHAR(24) NOT NULL DEFAULT 'dark',
    font_size      VARCHAR(4)  NOT NULL DEFAULT 'm',     -- xs|s|m|l|xl
    font_family    VARCHAR(24) NOT NULL DEFAULT 'system', -- system|serif|dyslexic
    line_spacing   VARCHAR(8)  NOT NULL DEFAULT 'normal', -- tight|normal|loose
    reading_width  VARCHAR(8)  NOT NULL DEFAULT 'medium', -- narrow|medium|wide
    reduced_motion TINYINT(1)  NOT NULL DEFAULT 0,
    audio_accent   VARCHAR(8)  NOT NULL DEFAULT 'us',     -- us|uk
    updated_at     DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
