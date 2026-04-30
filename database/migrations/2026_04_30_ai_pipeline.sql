-- =============================================================================
-- AviatorTutor — Phase 3: AI content pipeline
--
-- Adds the async generation queue + draft/publish lifecycle columns + the
-- typed-flashcard attempt log + the system unlock map. This migration is
-- safe to re-run because every ALTER + CREATE uses IF NOT EXISTS or the
-- equivalent guard pattern (`ALTER TABLE ... ADD COLUMN IF NOT EXISTS` is
-- supported on MariaDB 10.0.2+ and MySQL 8.0+).
-- =============================================================================

-- ----------------------------------------------------------------------------
-- 1. Draft / publish + AI provenance on lesson_slides
-- ----------------------------------------------------------------------------
ALTER TABLE lesson_slides
    ADD COLUMN IF NOT EXISTS status ENUM('draft','published') NOT NULL DEFAULT 'published'
        AFTER question,
    ADD COLUMN IF NOT EXISTS source ENUM('manual','ai','ai_assisted') NOT NULL DEFAULT 'manual'
        AFTER status,
    ADD COLUMN IF NOT EXISTS ai_job_id BIGINT UNSIGNED NULL AFTER source,
    ADD COLUMN IF NOT EXISTS source_quote TEXT NULL AFTER ai_job_id,
    ADD INDEX IF NOT EXISTS idx_status (status),
    ADD INDEX IF NOT EXISTS idx_ai_job_id (ai_job_id);

-- Existing rows were created before the column existed and should remain live.
UPDATE lesson_slides SET status = 'published' WHERE status IS NULL OR status = '';

-- ----------------------------------------------------------------------------
-- 2. Analysis depth + draft state on lessons
-- ----------------------------------------------------------------------------
ALTER TABLE lessons
    ADD COLUMN IF NOT EXISTS analysis_depth ENUM('standard','detail') NOT NULL DEFAULT 'standard'
        AFTER content_type,
    ADD COLUMN IF NOT EXISTS draft_status ENUM('draft','published') NOT NULL DEFAULT 'published'
        AFTER is_published,
    ADD INDEX IF NOT EXISTS idx_draft_status (draft_status);

-- ----------------------------------------------------------------------------
-- 3. System-to-system unlock prerequisite
-- ----------------------------------------------------------------------------
ALTER TABLE systems
    ADD COLUMN IF NOT EXISTS unlock_after_system_id BIGINT UNSIGNED NULL AFTER sort_order,
    ADD INDEX IF NOT EXISTS idx_unlock_after (unlock_after_system_id);

-- ----------------------------------------------------------------------------
-- 4. Typed-answer flashcards
-- ----------------------------------------------------------------------------
ALTER TABLE flashcards
    ADD COLUMN IF NOT EXISTS expected_answer TEXT NULL AFTER back,
    ADD COLUMN IF NOT EXISTS grading_rubric TEXT NULL AFTER expected_answer,
    ADD COLUMN IF NOT EXISTS status ENUM('draft','published') NOT NULL DEFAULT 'published'
        AFTER difficulty,
    ADD COLUMN IF NOT EXISTS source ENUM('manual','ai','ai_assisted') NOT NULL DEFAULT 'manual'
        AFTER status,
    ADD COLUMN IF NOT EXISTS ai_job_id BIGINT UNSIGNED NULL AFTER source,
    ADD INDEX IF NOT EXISTS idx_flashcards_status (status),
    ADD INDEX IF NOT EXISTS idx_flashcards_ai_job_id (ai_job_id);

-- ----------------------------------------------------------------------------
-- 5. Draft state on quiz_questions
-- ----------------------------------------------------------------------------
ALTER TABLE quiz_questions
    ADD COLUMN IF NOT EXISTS status ENUM('draft','published') NOT NULL DEFAULT 'published'
        AFTER difficulty,
    ADD COLUMN IF NOT EXISTS source ENUM('manual','ai','ai_assisted') NOT NULL DEFAULT 'manual'
        AFTER status,
    ADD COLUMN IF NOT EXISTS ai_job_id BIGINT UNSIGNED NULL AFTER source,
    ADD INDEX IF NOT EXISTS idx_quiz_questions_status (status),
    ADD INDEX IF NOT EXISTS idx_quiz_questions_ai_job_id (ai_job_id);

-- ----------------------------------------------------------------------------
-- 6. ai_generation_jobs — async job queue
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ai_generation_jobs (
    id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    admin_user_id       BIGINT UNSIGNED NOT NULL,
    target_system_id    BIGINT UNSIGNED NULL,
    pdf_path            VARCHAR(500) NULL,
    original_filename   VARCHAR(255) NULL,
    source_label        VARCHAR(255) NULL,
    pasted_text         LONGTEXT NULL,
    mode                ENUM('manual','assisted','full') NOT NULL DEFAULT 'full',
    analysis_depth      ENUM('standard','detail') NOT NULL DEFAULT 'standard',
    status              ENUM('queued','running','review','published','failed','cancelled')
                        NOT NULL DEFAULT 'queued',
    progress_pct        TINYINT UNSIGNED DEFAULT 0,
    progress_message    VARCHAR(255) NULL,
    prompt_tokens       INT UNSIGNED DEFAULT 0,
    completion_tokens   INT UNSIGNED DEFAULT 0,
    request_ms          INT UNSIGNED DEFAULT 0,
    raw_response        LONGTEXT NULL,
    payload_json        LONGTEXT NULL,
    lesson_id           BIGINT UNSIGNED NULL,
    error               TEXT NULL,
    locked_at           DATETIME NULL,
    started_at          DATETIME NULL,
    finished_at         DATETIME NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_admin_user (admin_user_id),
    INDEX idx_target_system (target_system_id),
    INDEX idx_locked_at (locked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. flashcard_attempts — typed-input grading log (used in Phase 5)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS flashcard_attempts (
    id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id       BIGINT UNSIGNED NOT NULL,
    flashcard_id  BIGINT UNSIGNED NOT NULL,
    typed_answer  TEXT NOT NULL,
    ai_score      TINYINT UNSIGNED NULL,    -- 0-100
    ai_feedback   TEXT NULL,
    is_correct    TINYINT(1) NOT NULL DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (flashcard_id) REFERENCES flashcards(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_user_flashcard (user_id, flashcard_id),
    INDEX idx_user_correct (user_id, is_correct)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. user_system_unlocks — system N+1 visibility (Phase 5)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS user_system_unlocks (
    user_id     BIGINT UNSIGNED NOT NULL,
    system_id   BIGINT UNSIGNED NOT NULL,
    unlocked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, system_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_system (system_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
