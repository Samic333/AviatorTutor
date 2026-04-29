-- Q400 Aircraft Systems Study Database Schema
-- MySQL InnoDB with UTF8MB4 character set
-- Version: 1.0

-- Drop and recreate database
DROP DATABASE IF EXISTS q400_study;
CREATE DATABASE IF NOT EXISTS q400_study CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE q400_study;

-- ============================================================================
-- CORE TABLES
-- ============================================================================

-- Users table
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','learner') NOT NULL DEFAULT 'learner',
    avatar VARCHAR(255) NULL,
    study_streak INT DEFAULT 0,
    last_active DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aircraft Systems table (22 Q400 systems)
CREATE TABLE systems (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    ata_code VARCHAR(10) NOT NULL,
    description LONGTEXT,
    color_hex VARCHAR(7),
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_sort_order (sort_order),
    INDEX idx_ata_code (ata_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subtopics within systems
CREATE TABLE subtopics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_system_slug (system_id, slug),
    INDEX idx_system_id (system_id),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- LESSON & CONTENT TABLES
-- ============================================================================

-- Lessons table
CREATE TABLE lessons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NOT NULL,
    subtopic_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content_type ENUM('overview','detail','revision','procedure') NOT NULL DEFAULT 'overview',
    body LONGTEXT,
    summary TEXT,
    key_facts JSON NULL,
    must_know JSON NULL,
    exam_traps JSON NULL,
    sort_order INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (subtopic_id) REFERENCES subtopics(id) ON DELETE SET NULL,
    INDEX idx_system_id (system_id),
    INDEX idx_subtopic_id (subtopic_id),
    INDEX idx_slug (slug),
    INDEX idx_content_type (content_type),
    INDEX idx_is_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lesson sections for detailed content organization
CREATE TABLE lesson_sections (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lesson_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    body LONGTEXT,
    section_type ENUM('overview','components','operation','normal','abnormal','indications','limitations','memory') NOT NULL DEFAULT 'overview',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson_id (lesson_id),
    INDEX idx_section_type (section_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ASSETS & DIAGRAMS
-- ============================================================================

-- Study assets (PDFs, images, diagrams, SVGs)
CREATE TABLE study_assets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NULL,
    lesson_id BIGINT UNSIGNED NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_type ENUM('pdf','image','diagram','svg') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_system_id (system_id),
    INDEX idx_lesson_id (lesson_id),
    INDEX idx_file_type (file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Interactive diagrams
CREATE TABLE diagrams (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(500),
    svg_data MEDIUMTEXT NULL,
    is_interactive TINYINT(1) DEFAULT 0,
    diagram_type ENUM('schematic','flow','location','electrical','hydraulic') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    INDEX idx_system_id (system_id),
    INDEX idx_diagram_type (diagram_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diagram hotspots (clickable elements)
CREATE TABLE diagram_hotspots (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    diagram_id BIGINT UNSIGNED NOT NULL,
    label VARCHAR(100),
    description TEXT,
    x_pct DECIMAL(5,2),
    y_pct DECIMAL(5,2),
    hotspot_type ENUM('component','flow','note') NOT NULL DEFAULT 'component',
    color_hex VARCHAR(7),
    state_group VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (diagram_id) REFERENCES diagrams(id) ON DELETE CASCADE,
    INDEX idx_diagram_id (diagram_id),
    INDEX idx_state_group (state_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diagram states (interactive states)
CREATE TABLE diagram_states (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    diagram_id BIGINT UNSIGNED NOT NULL,
    state_name VARCHAR(100) NOT NULL,
    state_label VARCHAR(100),
    description TEXT,
    hotspot_overrides JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (diagram_id) REFERENCES diagrams(id) ON DELETE CASCADE,
    INDEX idx_diagram_id (diagram_id),
    UNIQUE KEY unique_diagram_state (diagram_id, state_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FLASHCARD TABLES
-- ============================================================================

-- Flashcards for spaced repetition learning
CREATE TABLE flashcards (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NULL,
    subtopic_id BIGINT UNSIGNED NULL,
    front TEXT NOT NULL,
    back TEXT NOT NULL,
    hint TEXT,
    difficulty ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    tags JSON NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (subtopic_id) REFERENCES subtopics(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_system_id (system_id),
    INDEX idx_subtopic_id (subtopic_id),
    INDEX idx_difficulty (difficulty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Flashcard review tracking (spaced repetition SM2 algorithm)
CREATE TABLE flashcard_reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    flashcard_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT,
    next_review_at DATETIME,
    interval_days INT DEFAULT 1,
    ease_factor DECIMAL(4,2) DEFAULT 2.50,
    review_count INT DEFAULT 0,
    reviewed_at DATETIME,
    FOREIGN KEY (flashcard_id) REFERENCES flashcards(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_flashcard (flashcard_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_next_review_at (next_review_at),
    INDEX idx_user_next_review (user_id, next_review_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- QUIZ TABLES
-- ============================================================================

-- Quizzes
CREATE TABLE quizzes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    system_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    quiz_type ENUM('practice','exam','scenario') NOT NULL DEFAULT 'practice',
    time_limit_mins INT NULL,
    pass_score TINYINT DEFAULT 70,
    is_published TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    INDEX idx_system_id (system_id),
    INDEX idx_quiz_type (quiz_type),
    INDEX idx_is_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quiz questions
CREATE TABLE quiz_questions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq','true_false','sequence','label') NOT NULL DEFAULT 'mcq',
    options JSON,
    correct_answer JSON,
    explanation TEXT,
    difficulty ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_question_type (question_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quiz attempts
CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    score TINYINT,
    time_taken_secs INT,
    started_at DATETIME NOT NULL,
    completed_at DATETIME NULL,
    status ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_status (status),
    INDEX idx_completed_at (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quiz answers (individual question responses)
CREATE TABLE quiz_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    user_answer JSON,
    is_correct TINYINT(1) DEFAULT 0,
    time_taken_secs INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
    INDEX idx_attempt_id (attempt_id),
    INDEX idx_is_correct (is_correct)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STUDY TRACKING TABLES
-- ============================================================================

-- Study sessions
CREATE TABLE study_sessions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NULL,
    session_type ENUM('detail','revision','flashcard','quiz','diagram') NOT NULL,
    started_at DATETIME NOT NULL,
    ended_at DATETIME NULL,
    duration_secs INT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_system_id (system_id),
    INDEX idx_session_type (session_type),
    INDEX idx_started_at (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Study plans
CREATE TABLE study_plans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    exam_date DATE NULL,
    daily_minutes INT DEFAULT 60,
    status ENUM('active','paused','completed') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Study plan items
CREATE TABLE study_plan_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    plan_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NOT NULL,
    scheduled_date DATE NOT NULL,
    duration_mins INT,
    status ENUM('pending','completed','skipped') NOT NULL DEFAULT 'pending',
    completed_at DATETIME NULL,
    FOREIGN KEY (plan_id) REFERENCES study_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    INDEX idx_plan_id (plan_id),
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Revision schedule (spaced repetition)
CREATE TABLE revision_schedule (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    next_review_date DATE NOT NULL,
    interval_days INT DEFAULT 1,
    priority TINYINT DEFAULT 5,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_system_lesson (user_id, system_id, lesson_id),
    INDEX idx_user_id (user_id),
    INDEX idx_next_review_date (next_review_date),
    INDEX idx_user_next_review (user_id, next_review_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User progress tracking
CREATE TABLE user_progress (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    status ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
    confidence TINYINT DEFAULT 0,
    time_spent_secs INT DEFAULT 0,
    last_studied DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_system_lesson (user_id, system_id, lesson_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_last_studied (last_studied)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User topic strength tracking
CREATE TABLE user_topic_strength (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NOT NULL,
    subtopic_id BIGINT UNSIGNED NULL,
    strength_score TINYINT DEFAULT 50,
    quiz_attempts INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (subtopic_id) REFERENCES subtopics(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_system_subtopic (user_id, system_id, subtopic_id),
    INDEX idx_user_id (user_id),
    INDEX idx_strength_score (strength_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- NOTES & TAGGING
-- ============================================================================

-- User notes
CREATE TABLE notes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    system_id BIGINT UNSIGNED NULL,
    lesson_id BIGINT UNSIGNED NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_system_id (system_id),
    INDEX idx_lesson_id (lesson_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags for content
CREATE TABLE tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    color_hex VARCHAR(7),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Content tags mapping
CREATE TABLE content_tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tag_id BIGINT UNSIGNED NOT NULL,
    content_type ENUM('lesson','flashcard','quiz') NOT NULL,
    content_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_content_tag (tag_id, content_type, content_id),
    INDEX idx_content_type_id (content_type, content_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- IMPORT & LOGGING
-- ============================================================================

-- Content import logs
CREATE TABLE content_import_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    import_type VARCHAR(50),
    status ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
    records_imported INT DEFAULT 0,
    errors JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- AviatorTutor self-study platform additions
-- ============================================================================

-- Password reset tokens (hashed)
CREATE TABLE password_resets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_token_hash (token_hash),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email verification tokens (hashed)
CREATE TABLE email_verifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_token_hash (token_hash),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-user email_verified_at column (separate ALTER for forward compatibility)
ALTER TABLE users
    ADD COLUMN email_verified_at DATETIME NULL AFTER role,
    ADD COLUMN last_login_at DATETIME NULL AFTER email_verified_at;

-- Subscriptions (one row per active period)
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activation codes (one-time use, admin-generated)
CREATE TABLE activation_codes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(32) NOT NULL,
    plan ENUM('monthly') NOT NULL DEFAULT 'monthly',
    days INT UNSIGNED NOT NULL DEFAULT 30,
    status ENUM('unused','redeemed','revoked') NOT NULL DEFAULT 'unused',
    created_by_admin_id BIGINT UNSIGNED NULL,
    redeemed_by_user_id BIGINT UNSIGNED NULL,
    redeemed_at DATETIME NULL,
    expires_at DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_code (code),
    INDEX idx_status (status),
    INDEX idx_redeemed_by_user_id (redeemed_by_user_id),
    FOREIGN KEY (created_by_admin_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (redeemed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscription audit log
CREATE TABLE subscription_events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    subscription_id BIGINT UNSIGNED NULL,
    event_type VARCHAR(40) NOT NULL,
    payload_json JSON NULL,
    ip VARCHAR(45) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_subscription_id (subscription_id),
    INDEX idx_event_type (event_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coming-soon waitlist signups (lead capture)
CREATE TABLE lead_signups (
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

-- Generic admin audit log
CREATE TABLE audit_log (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(60) NOT NULL,
    target_type VARCHAR(60) NULL,
    target_id BIGINT UNSIGNED NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    payload_json JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_target (target_type, target_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- AIRCRAFT CATALOG (Phase 10: multi-aircraft platform)
-- ============================================================================

-- Aircraft catalog. Q400 is the only "live" entry today; the others advertise
-- the platform's planned coverage and capture lead-signups via /aircraft/{slug}.
CREATE TABLE IF NOT EXISTS aircrafts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(64) NOT NULL UNIQUE,                                      -- 'q400', 'b737', 'cessna-172'
    name VARCHAR(120) NOT NULL,                                            -- 'Bombardier Q400 NextGen'
    short_name VARCHAR(40) NOT NULL,                                       -- 'Q400'
    manufacturer VARCHAR(80) NOT NULL,                                     -- 'Bombardier'
    category ENUM('regional','narrowbody','widebody','ga','training') NOT NULL,
    status ENUM('live','beta','coming_soon','archived') NOT NULL DEFAULT 'coming_soon',
    tagline VARCHAR(255) NULL,                                             -- one-liner for catalog tile
    description TEXT NULL,                                                 -- detail page body
    cockpit_image_path VARCHAR(255) NULL,                                  -- /assets/aircraft/<slug>/cockpit.webp
    cockpit_poster_path VARCHAR(255) NULL,                                 -- /assets/aircraft/<slug>/cockpit-poster.webp
    cockpit_model_path VARCHAR(255) NULL,                                  -- /assets/aircraft/<slug>/cockpit.glb (future)
    hero_image_path VARCHAR(255) NULL,                                     -- catalog tile background
    sort_order INT NOT NULL DEFAULT 100,
    waitlist_count INT NOT NULL DEFAULT 0,                                 -- denormalized count of lead_signups
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status, sort_order),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aircraft cockpit panels. Each row is a clickable hotspot on the cockpit
-- image, mapping a (x,y,w,h) percent-rect to a study system. Tier-2 of the
-- cockpit experience.
CREATE TABLE IF NOT EXISTS aircraft_panels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    aircraft_id BIGINT UNSIGNED NOT NULL,
    slug VARCHAR(80) NOT NULL,                                             -- 'pfd-pilot', 'throttle-quadrant'
    label VARCHAR(120) NOT NULL,                                           -- 'Pilot PFD'
    description VARCHAR(255) NULL,
    system_id BIGINT UNSIGNED NULL,                                        -- where clicking takes the user
    sprite_path VARCHAR(255) NULL,                                         -- optional standalone PNG/WebP
    pos_x DECIMAL(6,3) NOT NULL DEFAULT 0,                                 -- left % of cockpit image
    pos_y DECIMAL(6,3) NOT NULL DEFAULT 0,                                 -- top  % of cockpit image
    width DECIMAL(6,3) NOT NULL DEFAULT 10,                                -- width %
    height DECIMAL(6,3) NOT NULL DEFAULT 10,                               -- height %
    sort_order INT NOT NULL DEFAULT 100,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_aircraft_panel (aircraft_id, slug),
    INDEX idx_aircraft (aircraft_id),
    INDEX idx_system (system_id),
    FOREIGN KEY (aircraft_id) REFERENCES aircrafts(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id)   REFERENCES systems(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit trail of cockpit / diagram image uploads through the admin UI.
CREATE TABLE IF NOT EXISTS asset_imports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    aircraft_id BIGINT UNSIGNED NULL,
    uploader_user_id BIGINT UNSIGNED NULL,
    view_slug VARCHAR(80) NOT NULL,                                        -- 'cockpit','panels','overhead'
    source_filename VARCHAR(255) NULL,
    source_bytes INT UNSIGNED NULL,
    output_path VARCHAR(255) NULL,
    crop_rect_json JSON NULL,
    mask_rects_json JSON NULL,
    note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_aircraft (aircraft_id),
    INDEX idx_uploader (uploader_user_id),
    FOREIGN KEY (aircraft_id)      REFERENCES aircrafts(id) ON DELETE SET NULL,
    FOREIGN KEY (uploader_user_id) REFERENCES users(id)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Public contact-form inbox surfaced in the admin dashboard.
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
