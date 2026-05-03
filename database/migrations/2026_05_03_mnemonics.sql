-- =============================================================================
-- AviatorTutor — Phase 3 (overhaul) migration
-- DB-backed mnemonics with explanations. Replaces the inline hardcoded list
-- in views/study/detail.php (4 ATA chapters only) with a structured table
-- the admin can edit and that the new mnemonics study mode reads from.
-- =============================================================================

CREATE TABLE IF NOT EXISTS mnemonics (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id       BIGINT UNSIGNED NULL,
    lesson_id       BIGINT UNSIGNED NULL,
    phrase          VARCHAR(255) NOT NULL,
    breakdown_json  JSON NULL,         -- [{"letter":"M","meaning":"Master caution"}, ...]
    why_it_works    TEXT NULL,
    worked_example  TEXT NULL,
    audio_url       VARCHAR(500) NULL, -- optional pre-recorded TTS
    sort_order      INT NOT NULL DEFAULT 100,
    is_published    TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE SET NULL,
    INDEX idx_system_published (system_id, is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
