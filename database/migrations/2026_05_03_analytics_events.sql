-- =============================================================================
-- AviatorTutor — Phase 5 (overhaul) migration
-- Lightweight analytics: one row per tracked user action. The admin
-- dashboard reads from this table; we never join it into hot paths.
-- =============================================================================

CREATE TABLE IF NOT EXISTS analytics_events (
    id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NULL,
    event       VARCHAR(64) NOT NULL,
    props_json  JSON NULL,
    session_id  VARCHAR(64) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_created (event, created_at),
    INDEX idx_user_created  (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
