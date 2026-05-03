-- =============================================================================
-- AviatorTutor — Phase 3 (overhaul) migration
-- Flashcards v2: category column for color-coded study + per-card stats.
-- The SM2 spaced-repetition machinery already lives in flashcard_reviews;
-- this migration only extends flashcards itself.
-- =============================================================================

ALTER TABLE flashcards
    ADD COLUMN IF NOT EXISTS category ENUM('warning','normal','abnormal','memory','limitation') NOT NULL DEFAULT 'normal' AFTER difficulty,
    ADD COLUMN IF NOT EXISTS theme_color VARCHAR(20) NULL AFTER category,
    ADD COLUMN IF NOT EXISTS why_it_matters VARCHAR(255) NULL AFTER theme_color,
    ADD COLUMN IF NOT EXISTS source_slide_id BIGINT UNSIGNED NULL AFTER why_it_matters,
    ADD COLUMN IF NOT EXISTS times_correct INT UNSIGNED NOT NULL DEFAULT 0 AFTER source_slide_id,
    ADD COLUMN IF NOT EXISTS times_wrong   INT UNSIGNED NOT NULL DEFAULT 0 AFTER times_correct,
    ADD COLUMN IF NOT EXISTS last_seen_at  DATETIME NULL AFTER times_wrong,
    ADD INDEX IF NOT EXISTS idx_system_category (system_id, category);
