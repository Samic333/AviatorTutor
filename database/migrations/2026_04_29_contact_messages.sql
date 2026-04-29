-- =============================================================================
-- AviatorTutor — Phase 1 (public polish) migration
-- Contact form → admin inbox
-- =============================================================================

CREATE TABLE IF NOT EXISTS contact_messages (
    id           BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name         VARCHAR(120)  NOT NULL,
    email        VARCHAR(255)  NOT NULL,
    message      TEXT          NOT NULL,
    status       ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
    ip           VARCHAR(45)   NULL,
    user_agent   TEXT          NULL,
    user_id      BIGINT UNSIGNED NULL,
    admin_notes  TEXT          NULL,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
