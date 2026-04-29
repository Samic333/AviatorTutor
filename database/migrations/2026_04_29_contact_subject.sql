-- =============================================================================
-- AviatorTutor — Phase 1 (public polish) migration
-- Add `subject` column to contact_messages so the admin inbox shows what
-- each inquiry is about and the public Get-in-Touch form can send a Subject.
-- Idempotent across MySQL 8/9 and MariaDB via INFORMATION_SCHEMA guards.
-- =============================================================================

SET @col_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'contact_messages'
       AND COLUMN_NAME  = 'subject'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE contact_messages ADD COLUMN subject VARCHAR(255) NULL AFTER email',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill existing rows so the inbox listing never renders an empty subject.
UPDATE contact_messages
   SET subject = 'General enquiry'
 WHERE subject IS NULL;
