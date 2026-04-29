-- =============================================================================
-- AviatorTutor — Phase 4 migration
-- Per-subject catalog + purchase model
-- Run after schema.sql is in place.
-- =============================================================================

-- 1. Subject catalog ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS subjects (
    id             BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    slug           VARCHAR(80)  NOT NULL UNIQUE,
    name           VARCHAR(160) NOT NULL,
    category       ENUM('aircraft_pack','airline_interview','aviation_subject') NOT NULL,
    description    TEXT NULL,
    short_blurb    VARCHAR(255) NULL,
    price_usd      DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    is_published   TINYINT(1)   NOT NULL DEFAULT 0,
    is_coming_soon TINYINT(1)   NOT NULL DEFAULT 1,
    sort_order     INT          NOT NULL DEFAULT 100,
    aircraft_id    BIGINT UNSIGNED NULL,
    icon_slug      VARCHAR(40)  NULL,
    color_hex      VARCHAR(7)   NULL,
    cover_image    VARCHAR(255) NULL,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aircraft_id) REFERENCES aircrafts(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_published (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Per-user per-subject purchases (one row = lifetime access by default) ----
CREATE TABLE IF NOT EXISTS purchases (
    id                       BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id                  BIGINT UNSIGNED NOT NULL,
    subject_id               BIGINT UNSIGNED NOT NULL,
    payment_provider         ENUM('stripe','activation_code','admin_grant','free') NOT NULL DEFAULT 'stripe',
    stripe_payment_intent_id VARCHAR(120) NULL,
    activation_code_id       BIGINT UNSIGNED NULL,
    amount_paid_usd          DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    status                   ENUM('active','refunded','disputed') NOT NULL DEFAULT 'active',
    granted_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at               DATETIME NULL,
    notes                    TEXT NULL,
    created_at               DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at               DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_subject (user_id, subject_id),
    FOREIGN KEY (user_id)            REFERENCES users(id)            ON DELETE CASCADE,
    FOREIGN KEY (subject_id)         REFERENCES subjects(id)         ON DELETE CASCADE,
    FOREIGN KEY (activation_code_id) REFERENCES activation_codes(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Extend activation codes to support per-subject grants --------------------
ALTER TABLE activation_codes
    ADD COLUMN IF NOT EXISTS subject_id  BIGINT UNSIGNED NULL AFTER plan,
    ADD COLUMN IF NOT EXISTS access_type ENUM('subscription','subject') NOT NULL DEFAULT 'subscription' AFTER subject_id;

-- Add the FK separately so re-runs are safe (MySQL has no IF NOT EXISTS for FKs).
-- Wrap in conditional logic at deploy time, or just rely on this failing harmlessly on re-run.
ALTER TABLE activation_codes
    ADD CONSTRAINT fk_codes_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL;

-- 4. Link existing systems to a subject (Q400 systems will FK to q400-pack) ---
ALTER TABLE systems
    ADD COLUMN IF NOT EXISTS subject_id BIGINT UNSIGNED NULL AFTER id,
    ADD INDEX IF NOT EXISTS idx_subject_id (subject_id);

ALTER TABLE systems
    ADD CONSTRAINT fk_systems_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL;

-- =============================================================================
-- 5. Seed subjects ------------------------------------------------------------
-- =============================================================================

-- Aircraft packs ($29 each) ---------------------------------------------------
INSERT INTO subjects (slug, name, category, short_blurb, price_usd, is_published, is_coming_soon, sort_order, color_hex, icon_slug)
VALUES
  ('q400-pack',  'Q400 Aircraft Pack',  'aircraft_pack', '22 ATA-organised systems with flashcards & quizzes.', 29.00, 1, 0, 10, '#38BDF8', 'plane'),
  ('b737-pack',  'B737 Aircraft Pack',  'aircraft_pack', 'Boeing 737 systems study — coming soon.',             29.00, 1, 1, 20, '#0EA5E9', 'plane'),
  ('b787-pack',  'B787 Aircraft Pack',  'aircraft_pack', 'Boeing 787 systems study — coming soon.',             29.00, 1, 1, 30, '#0284C7', 'plane'),
  ('a320-pack',  'A320 Aircraft Pack',  'aircraft_pack', 'Airbus A320 family systems — coming soon.',           29.00, 1, 1, 40, '#22D3EE', 'plane'),
  ('atr72-pack', 'ATR 72 Aircraft Pack','aircraft_pack', 'ATR 72 turboprop systems — coming soon.',             29.00, 1, 1, 50, '#06B6D4', 'plane'),
  ('a350-pack',  'A350 Aircraft Pack',  'aircraft_pack', 'Airbus A350 systems — coming soon.',                  29.00, 1, 1, 60, '#0891B2', 'plane'),
  ('b777-pack',  'B777 Aircraft Pack',  'aircraft_pack', 'Boeing 777 systems — coming soon.',                   29.00, 1, 1, 70, '#155E75', 'plane'),
  ('b747-pack',  'B747 Aircraft Pack',  'aircraft_pack', 'Boeing 747 systems — coming soon.',                   29.00, 1, 1, 80, '#0C4A6E', 'plane');

-- Airline interview packs ($19 each) ------------------------------------------
INSERT INTO subjects (slug, name, category, short_blurb, price_usd, is_published, is_coming_soon, sort_order, color_hex, icon_slug)
VALUES
  ('emirates-interview',          'Emirates Interview Prep',         'airline_interview', 'Tech, HR, and sim assessment prep.',           19.00, 1, 1, 100, '#D71921', 'briefcase'),
  ('qatar-interview',             'Qatar Airways Interview Prep',    'airline_interview', 'Stage interviews + sim profile guide.',        19.00, 1, 1, 110, '#5C0F2B', 'briefcase'),
  ('ethiopian-interview',         'Ethiopian Airlines Interview',    'airline_interview', 'Tech & HR rounds for Star Alliance carrier.',  19.00, 1, 1, 120, '#078930', 'briefcase'),
  ('kenya-airways-interview',     'Kenya Airways Interview',         'airline_interview', 'Pride of Africa interview & sim prep.',        19.00, 1, 1, 130, '#C8102E', 'briefcase'),
  ('egyptair-interview',          'EgyptAir Interview',              'airline_interview', 'Star Alliance interview prep.',                19.00, 1, 1, 140, '#E4002B', 'briefcase'),
  ('south-african-interview',     'South African Airways Interview', 'airline_interview', 'SAA interview & assessment prep.',             19.00, 1, 1, 150, '#003DA5', 'briefcase'),
  ('rwandair-interview',          'RwandAir Interview',              'airline_interview', 'Dream of the Sky interview prep.',             19.00, 1, 1, 160, '#0072CE', 'briefcase'),
  ('flydubai-interview',          'flydubai Interview',              'airline_interview', 'flydubai screening & sim prep.',               19.00, 1, 1, 170, '#001A70', 'briefcase'),
  ('air-arabia-interview',        'Air Arabia Interview',            'airline_interview', 'Air Arabia LCC interview prep.',               19.00, 1, 1, 180, '#E20E18', 'briefcase'),
  ('turkish-airlines-interview',  'Turkish Airlines Interview',      'airline_interview', 'TK assessment + sim profile.',                 19.00, 1, 1, 190, '#C70A0C', 'briefcase'),
  ('lufthansa-interview',         'Lufthansa Interview',             'airline_interview', 'DLR test + LH selection prep.',                19.00, 1, 1, 200, '#05164D', 'briefcase'),
  ('british-airways-interview',   'British Airways Interview',       'airline_interview', 'BA cadet & direct entry prep.',                19.00, 1, 1, 210, '#075AAA', 'briefcase'),
  ('air-france-interview',        'Air France Interview',            'airline_interview', 'AF selection & sim prep.',                     19.00, 1, 1, 220, '#002157', 'briefcase'),
  ('singapore-airlines-interview','Singapore Airlines Interview',    'airline_interview', 'SQ stage interviews & sim.',                   19.00, 1, 1, 230, '#1A2D5A', 'briefcase'),
  ('cathay-pacific-interview',    'Cathay Pacific Interview',        'airline_interview', 'CX cadet/DE pilot selection.',                 19.00, 1, 1, 240, '#006564', 'briefcase'),
  ('korean-air-interview',        'Korean Air Interview',            'airline_interview', 'KE interview & assessment prep.',              19.00, 1, 1, 250, '#00256C', 'briefcase'),
  ('delta-interview',             'Delta Air Lines Interview',       'airline_interview', 'DL pilot interview prep.',                     19.00, 1, 1, 260, '#003366', 'briefcase'),
  ('united-interview',            'United Airlines Interview',       'airline_interview', 'UA pilot interview prep.',                     19.00, 1, 1, 270, '#002244', 'briefcase'),
  ('american-airlines-interview', 'American Airlines Interview',     'airline_interview', 'AA pilot interview prep.',                     19.00, 1, 1, 280, '#0078D2', 'briefcase'),
  ('ryanair-interview',           'Ryanair Interview',               'airline_interview', 'FR pilot type-rating & interview.',            19.00, 1, 1, 290, '#073590', 'briefcase');

-- Aviation subject packs ($14 each) -------------------------------------------
INSERT INTO subjects (slug, name, category, short_blurb, price_usd, is_published, is_coming_soon, sort_order, color_hex, icon_slug)
VALUES
  ('weather-meteorology',  'Weather & Meteorology',                 'aviation_subject', 'METAR/TAF, fronts, hazards, performance.',  14.00, 1, 1, 300, '#0EA5E9', 'cloud'),
  ('performance-wb',       'Performance & Weight & Balance',        'aviation_subject', 'V-speeds, runway analysis, W&B.',           14.00, 1, 1, 310, '#F59E0B', 'scale'),
  ('crm',                  'Crew Resource Management (CRM)',        'aviation_subject', 'TEM, communication, leadership.',           14.00, 1, 1, 320, '#22D3EE', 'users'),
  ('sms',                  'Safety Management Systems (SMS)',       'aviation_subject', 'ICAO Annex 19 SMS pillars.',                14.00, 1, 1, 330, '#10B981', 'shield'),
  ('dgr',                  'Dangerous Goods (DGR)',                 'aviation_subject', 'IATA DGR awareness for flight crew.',       14.00, 1, 1, 340, '#EF4444', 'alert-triangle'),
  ('air-law',              'Air Law',                               'aviation_subject', 'ICAO Annexes, JAR/FAR, national rules.',    14.00, 1, 1, 350, '#6366F1', 'book'),
  ('human-factors',        'Human Factors',                         'aviation_subject', 'Physiology, fatigue, decision-making.',     14.00, 1, 1, 360, '#EC4899', 'brain'),
  ('navigation-pbn',       'Navigation (RNAV/RNP/PBN)',             'aviation_subject', 'PBN concepts, RNP AR, GNSS.',               14.00, 1, 1, 370, '#A855F7', 'compass'),
  ('communications-icao',  'Communications & ICAO Phraseology',     'aviation_subject', 'Standard phraseology, lost-comm.',          14.00, 1, 1, 380, '#3B82F6', 'radio'),
  ('ops-control',          'Operations Control & Dispatch',         'aviation_subject', 'Flight planning, dispatch, MEL/CDL.',       14.00, 1, 1, 390, '#14B8A6', 'clipboard');

-- 6. Backfill: Q400 systems → q400-pack ---------------------------------------
UPDATE systems
   SET subject_id = (SELECT id FROM subjects WHERE slug = 'q400-pack' LIMIT 1)
 WHERE subject_id IS NULL;
