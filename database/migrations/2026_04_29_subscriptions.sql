-- 2026_04_29_subscriptions.sql
--
-- Adds the three subscription / activation-code tables that were defined
-- in database/schema.sql but never made it into the local `aviatortutor`
-- DB. Without these tables, AdminMetricsService::dashboard() and the
-- /admin user-list query both throw and the page returns 500.
--
-- Type-mismatch note: schema.sql declared user-facing FKs as
-- BIGINT UNSIGNED, but local users.id is INT UNSIGNED. We keep INT UNSIGNED
-- for every column that references users(id) so the FK creates cleanly.
-- Internal autoincrement IDs (subscriptions.id, activation_codes.id,
-- subscription_events.id) stay BIGINT UNSIGNED to match the local
-- convention used by aircrafts / lessons / contact_messages / lead_signups.
--
-- Idempotent: CREATE TABLE IF NOT EXISTS — safe to re-run.

CREATE TABLE IF NOT EXISTS subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    plan ENUM('monthly') NOT NULL DEFAULT 'monthly',
    status ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
    payment_provider ENUM('activation_code','stripe') NOT NULL DEFAULT 'activation_code',
    starts_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    stripe_customer_id VARCHAR(120) NULL,
    stripe_subscription_id VARCHAR(120) NULL,
    activation_code_id BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_user_status (user_id, status),
    INDEX idx_status_expires (status, expires_at),
    CONSTRAINT fk_subs_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activation_codes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(32) NOT NULL,
    plan ENUM('monthly') NOT NULL DEFAULT 'monthly',
    days INT UNSIGNED NOT NULL DEFAULT 30,
    status ENUM('unused','redeemed','revoked') NOT NULL DEFAULT 'unused',
    created_by_admin_id INT UNSIGNED NULL,
    redeemed_by_user_id INT UNSIGNED NULL,
    redeemed_at DATETIME NULL,
    expires_at DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_code (code),
    INDEX idx_status (status),
    INDEX idx_redeemed_by_user_id (redeemed_by_user_id),
    CONSTRAINT fk_codes_admin
        FOREIGN KEY (created_by_admin_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_codes_redeemer
        FOREIGN KEY (redeemed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscription_events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    subscription_id BIGINT UNSIGNED NULL,
    event_type VARCHAR(40) NOT NULL,
    payload_json JSON NULL,
    ip VARCHAR(45) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_subscription_id (subscription_id),
    INDEX idx_event_type (event_type),
    CONSTRAINT fk_subevt_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_subevt_sub
        FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coming-soon waitlist signups (lead capture). Read by AdminMetricsService
-- (counts + recent leads) and written by the public coming-soon form. Defined
-- in schema.sql but never made it into the local DB.
CREATE TABLE IF NOT EXISTS lead_signups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    requested_module_slug VARCHAR(120) NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_module (requested_module_slug),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
