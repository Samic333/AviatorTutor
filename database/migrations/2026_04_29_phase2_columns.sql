-- =============================================================================
-- AviatorTutor — Phase 2 conditional column/table migration
-- 2026-04-29
--
-- Purpose: Bring older `aviatortutor` databases (set up before the canonical
-- `database/schema.sql` was the source of truth) up to the column/table set
-- the dashboard, study, and revision controllers query.
--
-- This file is INTENTIONALLY non-destructive — it only ADDS missing columns
-- and creates missing tables (using IF NOT EXISTS where MySQL allows). Run
-- it ONLY if the Step 3 schema probes in DEPLOY notes report missing columns.
--
-- MySQL < 8.0.29 does NOT support `ALTER TABLE ... ADD COLUMN IF NOT EXISTS`.
-- Each ALTER block is therefore wrapped in a small dynamic-SQL helper that
-- only runs the ALTER when the column doesn't already exist. Safe to re-run.
--
-- Usage on the production server:
--   mysql -h DBHOST -u DBUSER -p DBNAME \
--     < database/migrations/2026_04_29_phase2_columns.sql
-- =============================================================================

DELIMITER $$

-- Helper: add a column only if it doesn't exist on the given table.
DROP PROCEDURE IF EXISTS phase2_add_column_if_missing $$
CREATE PROCEDURE phase2_add_column_if_missing(
    IN p_table  VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_def    TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
          FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME   = p_table
           AND COLUMN_NAME  = p_column
    ) THEN
        SET @sql := CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DELIMITER ;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CALL phase2_add_column_if_missing('users', 'study_streak',          'INT UNSIGNED NOT NULL DEFAULT 0');
CALL phase2_add_column_if_missing('users', 'is_premium',            'TINYINT(1) NOT NULL DEFAULT 0');
CALL phase2_add_column_if_missing('users', 'avatar',                'VARCHAR(255) NULL');
CALL phase2_add_column_if_missing('users', 'last_active',           'DATETIME NULL');
CALL phase2_add_column_if_missing('users', 'last_login_at',         'DATETIME NULL');
CALL phase2_add_column_if_missing('users', 'preferred_aircraft_id', 'INT UNSIGNED NULL');

-- ---------------------------------------------------------------------------
-- user_progress  (controllers reference system_id, lesson_id, status,
-- time_spent_secs, confidence, last_studied — older schemas only had module_id)
-- ---------------------------------------------------------------------------
CALL phase2_add_column_if_missing('user_progress', 'system_id',       'INT UNSIGNED NULL');
CALL phase2_add_column_if_missing('user_progress', 'lesson_id',       'INT UNSIGNED NULL');
CALL phase2_add_column_if_missing('user_progress', 'status',          "ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started'");
CALL phase2_add_column_if_missing('user_progress', 'time_spent_secs', 'INT UNSIGNED NOT NULL DEFAULT 0');
CALL phase2_add_column_if_missing('user_progress', 'confidence',      'TINYINT UNSIGNED NOT NULL DEFAULT 0');
CALL phase2_add_column_if_missing('user_progress', 'last_studied',    'DATETIME NULL');

-- ---------------------------------------------------------------------------
-- flashcards
-- ---------------------------------------------------------------------------
CALL phase2_add_column_if_missing('flashcards', 'system_id',  'INT UNSIGNED NULL');
CALL phase2_add_column_if_missing('flashcards', 'front',      'TEXT NULL');
CALL phase2_add_column_if_missing('flashcards', 'back',       'TEXT NULL');
CALL phase2_add_column_if_missing('flashcards', 'difficulty', "ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium'");

-- If older schemas have NOT NULL front_md/back_md but no `front`/`back`,
-- backfill the new columns from the old ones to keep existing data live.
UPDATE flashcards
   SET front = front_md
 WHERE (front IS NULL OR front = '')
   AND front_md IS NOT NULL
   AND front_md <> '';

UPDATE flashcards
   SET back = back_md
 WHERE (back IS NULL OR back = '')
   AND back_md IS NOT NULL
   AND back_md <> '';

-- ---------------------------------------------------------------------------
-- quiz_attempts
-- ---------------------------------------------------------------------------
CALL phase2_add_column_if_missing('quiz_attempts', 'quiz_id',      'INT UNSIGNED NULL');
CALL phase2_add_column_if_missing('quiz_attempts', 'score',        'TINYINT UNSIGNED NULL');
CALL phase2_add_column_if_missing('quiz_attempts', 'status',       "ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress'");
CALL phase2_add_column_if_missing('quiz_attempts', 'completed_at', 'DATETIME NULL');

-- ---------------------------------------------------------------------------
-- New tables (idempotent via IF NOT EXISTS)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS study_sessions (
    id              INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id         INT UNSIGNED NOT NULL,
    system_id       INT UNSIGNED NULL,
    session_type    ENUM('lesson','flashcard','quiz','revision') NOT NULL DEFAULT 'lesson',
    duration_seconds INT NOT NULL DEFAULT 0,
    started_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ended_at        DATETIME NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_started_at (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quizzes (
    id                 INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id          INT UNSIGNED NOT NULL,
    title              VARCHAR(255) NOT NULL,
    quiz_type          ENUM('standard','exam_prep','rapid_fire') NOT NULL DEFAULT 'standard',
    time_limit_minutes INT NULL,
    pass_score         TINYINT UNSIGNED NOT NULL DEFAULT 70,
    is_published       TINYINT(1) NOT NULL DEFAULT 1,
    sort_order         INT NOT NULL DEFAULT 0,
    created_at         DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_system_id (system_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS flashcard_reviews (
    id              INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id         INT UNSIGNED NOT NULL,
    flashcard_id    INT UNSIGNED NOT NULL,
    grade           ENUM('again','hard','good','easy') NOT NULL DEFAULT 'good',
    reviewed_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_at          DATETIME NULL,
    next_review_at  DATETIME NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_flashcard_id (flashcard_id),
    INDEX idx_due_at (due_at),
    INDEX idx_next_review_at (next_review_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Drop the helper procedure so we don't leave it cluttering the DB.
-- ---------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS phase2_add_column_if_missing;
