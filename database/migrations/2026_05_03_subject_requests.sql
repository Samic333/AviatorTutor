-- =============================================================================
-- AviatorTutor — Phase 1 (overhaul) migration
-- subject_requests: pilot-initiated request to enroll in a new subject.
-- Admin sees the request, sends a quote, then grants access manually
-- (Stripe / Chapa integration is a later phase).
-- =============================================================================

CREATE TABLE IF NOT EXISTS subject_requests (
    id                 BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id            BIGINT UNSIGNED NOT NULL,
    requested_subject  VARCHAR(160) NOT NULL,
    subject_slug       VARCHAR(80)  NULL,
    notes              TEXT NULL,
    status             ENUM('pending','quoted','paid','declined','cancelled')
                       NOT NULL DEFAULT 'pending',
    admin_notes        TEXT NULL,
    quoted_amount_usd  DECIMAL(8,2) NULL,
    created_at         DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
