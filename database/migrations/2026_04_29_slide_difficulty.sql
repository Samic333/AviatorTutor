-- =============================================================================
-- AviatorTutor — Phase 2 (study UX) migration
-- Wire the Beginner / Intermediate / Advanced UI dropdown to real slide-level
-- gating. Three independent boolean columns let the same slide appear at
-- multiple levels without duplicating content.
--
-- Default = visible at every level (1/1/1) so existing decks render
-- identically before authors mark slides as advanced-only.
--
-- Idempotent across MySQL 8/9 and MariaDB via INFORMATION_SCHEMA guards.
-- =============================================================================

-- show_beginner
SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'lesson_slides'
       AND COLUMN_NAME  = 'show_beginner'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE lesson_slides ADD COLUMN show_beginner TINYINT(1) NOT NULL DEFAULT 1 AFTER question',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- show_intermediate
SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'lesson_slides'
       AND COLUMN_NAME  = 'show_intermediate'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE lesson_slides ADD COLUMN show_intermediate TINYINT(1) NOT NULL DEFAULT 1 AFTER show_beginner',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- show_advanced
SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'lesson_slides'
       AND COLUMN_NAME  = 'show_advanced'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE lesson_slides ADD COLUMN show_advanced TINYINT(1) NOT NULL DEFAULT 1 AFTER show_intermediate',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Sensible defaults by slide_type for any deck that already exists:
-- beginner mode skips advanced/abnormal/qrh/scenario/quiz slides.
UPDATE lesson_slides
   SET show_beginner = 0
 WHERE slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
