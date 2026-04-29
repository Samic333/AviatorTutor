-- =============================================================================
-- AviatorTutor — Phase 5 (admin in-dashboard reply)
-- Stores admin replies to contact-form inquiries so the conversation thread
-- is visible inside the dashboard and the audit trail is preserved even if
-- the outbound mail() send fails. Decoupled from the original message so
-- multiple back-and-forth replies are supported.
--
-- Note: user FKs intentionally omitted to stay tolerant of schemas where
-- users.id is INT UNSIGNED vs BIGINT UNSIGNED. Application logic enforces
-- the relationships.
-- =============================================================================

CREATE TABLE IF NOT EXISTS contact_replies (
    id                 BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    contact_message_id BIGINT UNSIGNED NOT NULL,
    admin_user_id      INT UNSIGNED    NOT NULL,
    body               TEXT NOT NULL,
    sent_at            DATETIME DEFAULT CURRENT_TIMESTAMP,
    mail_status        ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',
    error              TEXT NULL,
    INDEX idx_message (contact_message_id),
    INDEX idx_admin (admin_user_id),
    INDEX idx_sent (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
