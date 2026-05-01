-- =============================================================================
-- AviatorTutor — Phase 6: multi-provider AI tracking
--
-- Adds provider + model columns to ai_generation_jobs so we can see
-- which AI produced which lesson. Defaults to 'anthropic' for back-compat
-- with rows created before this migration.
-- =============================================================================

ALTER TABLE ai_generation_jobs
    ADD COLUMN IF NOT EXISTS provider ENUM('anthropic','openai','gemini') NOT NULL DEFAULT 'anthropic'
        AFTER analysis_depth,
    ADD COLUMN IF NOT EXISTS model VARCHAR(100) NULL
        AFTER provider,
    ADD INDEX IF NOT EXISTS idx_ai_jobs_provider (provider);
