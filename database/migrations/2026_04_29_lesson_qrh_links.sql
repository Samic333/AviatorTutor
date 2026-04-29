-- =============================================================================
-- AviatorTutor — Phase 4 (QRH integration) migration
-- Cross-reference table linking lessons (or specific slides) to QRH sections.
-- Lets the slide player render QRH excerpts directly inside qrh-type slides
-- with memory-item flags, recognition cues, and crew-action prompts.
-- =============================================================================

CREATE TABLE IF NOT EXISTS lesson_qrh_links (
    id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lesson_id         BIGINT UNSIGNED NOT NULL,
    -- Optional: scope the link to a specific slide. NULL = applies to every
    -- qrh-type slide in the lesson.
    slide_id          BIGINT UNSIGNED NULL,
    qrh_section_title VARCHAR(255) NOT NULL,
    qrh_excerpt       TEXT NOT NULL,
    -- Memory item = first crew action(s) that must be done from memory before
    -- opening the QRH (engine fire, dual-hydraulic loss, etc.).
    memory_item       TINYINT(1) NOT NULL DEFAULT 0,
    -- Operational meaning of this section in plain language.
    ops_meaning       TEXT NULL,
    -- Recognition cue: how the crew first detects this condition (caution
    -- light, EICAS message, indication, sound, smell, etc.).
    recognition_cue   VARCHAR(255) NULL,
    -- Memory-trigger phrase used as a mnemonic (e.g. "FUEL OFF, AGENT ONE").
    memory_trigger    VARCHAR(255) NULL,
    sort_order        INT NOT NULL DEFAULT 0,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)       ON DELETE CASCADE,
    FOREIGN KEY (slide_id)  REFERENCES lesson_slides(id) ON DELETE CASCADE,
    INDEX idx_lesson (lesson_id),
    INDEX idx_lesson_slide (lesson_id, slide_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
