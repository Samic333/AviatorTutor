-- =============================================================================
-- AviatorTutor — Phase 3: interactive slide-based lesson player
-- New tables: lesson_slides, user_slide_progress
-- =============================================================================

CREATE TABLE IF NOT EXISTS lesson_slides (
    id           BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lesson_id    BIGINT UNSIGNED NOT NULL,
    sort_order   INT DEFAULT 0,
    slide_type   ENUM('intro','concept','system','normal_op','abnormal',
                      'operational','qrh','scenario','revision','quiz')
                 NOT NULL DEFAULT 'concept',
    title        VARCHAR(255) NOT NULL,
    body         TEXT NULL,
    media_type   ENUM('none','image','diagram','video','animation','model3d')
                 NOT NULL DEFAULT 'none',
    media_url    VARCHAR(500) NULL,
    media_alt    VARCHAR(255) NULL,
    key_point    TEXT NULL,
    ops_relevance TEXT NULL,
    question     JSON NULL,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson (lesson_id),
    INDEX idx_lesson_sort (lesson_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_slide_progress (
    id               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id          INT UNSIGNED NOT NULL,
    lesson_id        BIGINT UNSIGNED NOT NULL,
    slide_id         BIGINT UNSIGNED NOT NULL,
    answered_correct TINYINT(1) DEFAULT 0,
    attempts         INT DEFAULT 0,
    viewed_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)         ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)       ON DELETE CASCADE,
    FOREIGN KEY (slide_id)  REFERENCES lesson_slides(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_slide (user_id, slide_id),
    INDEX idx_user_lesson (user_id, lesson_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
