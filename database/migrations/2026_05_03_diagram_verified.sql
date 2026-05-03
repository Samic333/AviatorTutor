-- =============================================================================
-- AviatorTutor — Phase 1 (overhaul) migration
-- Track per-slide diagram verification so future audits are easy.
--   diagram_verified      = 1 once an audit confirms the media_url shows the
--                            correct system for the slide's lesson.
--   diagram_audit_notes   = free text from the audit (e.g. "missing file",
--                            "system slug doesn't match URL", "manually
--                            curated set" for the hydraulics PNG bundle).
-- =============================================================================

ALTER TABLE lesson_slides
    ADD COLUMN IF NOT EXISTS diagram_verified TINYINT(1) NOT NULL DEFAULT 0
        AFTER media_alt,
    ADD COLUMN IF NOT EXISTS diagram_audit_notes VARCHAR(255) NULL
        AFTER diagram_verified;
