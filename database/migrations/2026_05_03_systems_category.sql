-- =============================================================================
-- AviatorTutor — Phase 4 (overhaul) migration
-- Group systems into categories so the picker can collapse 22+ Q400 systems
-- (and a future 30+ B777 list) into ~6 collapsible category sections.
-- =============================================================================

ALTER TABLE systems
    ADD COLUMN IF NOT EXISTS category VARCHAR(40) NOT NULL DEFAULT 'general' AFTER ata_code,
    ADD INDEX IF NOT EXISTS idx_category (category);

-- Backfill Q400 systems based on ATA chapters.
UPDATE systems SET category = 'powerplant'   WHERE slug IN ('powerplant','propeller','fuel');
UPDATE systems SET category = 'electrical'   WHERE slug IN ('electrical','lighting');
UPDATE systems SET category = 'hydraulics'   WHERE slug IN ('hydraulic','landing-gear','flight-controls');
UPDATE systems SET category = 'avionics'     WHERE slug IN ('autoflight','navigation','communications','indicating-recording','fms');
UPDATE systems SET category = 'environmental'WHERE slug IN ('air-cond-press','pneumatics','ice-rain','oxygen','fire-protection');
UPDATE systems SET category = 'reference'    WHERE slug IN ('aeroplane-general','caution-warning','du-messages','qrh');
