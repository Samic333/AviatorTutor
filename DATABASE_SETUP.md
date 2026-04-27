# Q400 Aircraft Systems Study App - Database Setup Guide

## Overview

This document describes the complete MySQL database schema and setup procedures for the Q400 Aircraft Systems Study App.

## Directory Structure

```
database/
├── schema.sql           # Complete MySQL schema (InnoDB, utf8mb4)
├── seed_data.sql        # Initial seed data (users, systems, lessons, etc)
└── seeds/
    ├── systems_index.json       # Index of all Q400 systems
    ├── electrical.json          # Sample content for Electrical system
    └── [other_systems].json     # Additional system content files

scripts/
├── setup.php            # Interactive setup wizard
└── seed_database.php    # Database seeder with JSON import

config/
└── database.php         # Database configuration
```

## Database Tables (30 Total)

### Core Tables
- **users** - User accounts (admin, learner)
- **systems** - Q400 ATA systems (22 total)
- **subtopics** - Subtopics within systems

### Content Tables
- **lessons** - Study lessons (overview, detail, revision, procedure)
- **lesson_sections** - Detailed lesson content sections
- **study_assets** - PDFs, images, diagrams, SVGs
- **diagrams** - Interactive diagrams with hotspots
- **diagram_hotspots** - Clickable diagram elements
- **diagram_states** - Interactive diagram states

### Learning Tables
- **flashcards** - Spaced repetition flashcards
- **flashcard_reviews** - SM2 algorithm tracking (SM2 parameters)
- **quizzes** - Practice/exam quizzes
- **quiz_questions** - Individual questions
- **quiz_attempts** - Quiz attempt tracking
- **quiz_answers** - Individual question responses

### Tracking Tables
- **study_sessions** - User study activity
- **study_plans** - Exam preparation plans
- **study_plan_items** - Daily study items
- **revision_schedule** - Spaced repetition schedule
- **user_progress** - Lesson completion tracking
- **user_topic_strength** - Topic mastery tracking

### Notes & Organization
- **notes** - User study notes
- **tags** - Content tags
- **content_tags** - Tag mappings
- **content_import_logs** - Import tracking

## Key Features

### 1. InnoDB Storage Engine
- ACID compliance
- Foreign key support
- Crash recovery

### 2. UTF8MB4 Charset
- Full Unicode support
- Emoji support if needed
- International character handling

### 3. Comprehensive Indexing
- Primary keys on all tables
- Foreign key relationships
- Composite indexes for common queries:
  - user_id + next_review_at (flashcard reviews)
  - user_id + system_id + lesson_id (progress uniqueness)
  - user_id + next_review_date (revision schedule)

### 4. Spaced Repetition Support
- SM2 algorithm fields (ease_factor, interval_days)
- Flashcard review tracking
- Revision schedule management
- Next review date indexing for efficient queries

### 5. Multi-Type Content
- Lessons with sections
- Interactive diagrams with hotspots
- Flashcards with hints
- Multiple quiz types (MCQ, true/false, sequencing, labeling)

### 6. Complete Audit Trail
- created_at timestamps on all tables
- updated_at on mutable tables
- Import logging
- Session tracking

## Setup Instructions

### Option 1: Automated Setup (Recommended)

```bash
cd /path/to/q400-study
php scripts/setup.php
```

This script will:
1. Check PHP version (>= 8.0)
2. Verify required extensions (PDO, PDO MySQL)
3. Test database connection
4. Create necessary directories
5. Run schema.sql
6. Load seed_data.sql
7. Create sample content from JSON

### Option 2: Manual Setup

#### Step 1: Configure Database

Edit `config/database.php`:
```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'q400_study',
    'username' => 'root',
    'password' => '',
];
```

Or use environment variables:
```bash
export DB_HOST=localhost
export DB_PORT=3306
export DB_NAME=q400_study
export DB_USER=root
export DB_PASS=''
```

#### Step 2: Create Database

```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS q400_study; CREATE DATABASE q400_study CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### Step 3: Load Schema

```bash
mysql -u root -p q400_study < database/schema.sql
```

#### Step 4: Load Seed Data

```bash
mysql -u root -p q400_study < database/seed_data.sql
```

#### Step 5: Run Seeder (Optional)

```bash
php scripts/seed_database.php
```

## Seed Data Included

### Users (4 total)
- 1 Admin user (admin@q400study.local)
- 3 Learner accounts

### Systems (22 total - All Q400 ATA Systems)
1. Electrical Power (ATA 24)
2. Hydraulic Power (ATA 29)
3. Fuel (ATA 28)
4. Powerplant (ATA 71)
5. Propeller (ATA 61)
6. Flight Controls (ATA 27)
7. Landing Gear (ATA 32)
8. Air Conditioning & Pressurization (ATA 21)
9. Pneumatics (ATA 36)
10. Ice & Rain Protection (ATA 30)
11. Fire Protection (ATA 26)
12. Autoflight (ATA 22)
13. Navigation (ATA 34)
14. Communications (ATA 23)
15. Indicating & Recording (ATA 31)
16. Oxygen (ATA 35)
17. Lighting (ATA 33)
18. Aeroplane General (ATA 21)
19. FMS (ATA 22B)
20. Caution & Warning (CW)
21. DU Messages (DU)
22. Quick Reference Handbook (QRH)

### Sample Content
- 4 system overview lessons
- 20 flashcards (electrical, hydraulic, fuel, powerplant)
- 1 practice quiz with 5 questions
- User progress tracking
- Revision schedules
- Study sessions

## Schema Highlights

### Flashcard Spaced Repetition

The `flashcard_reviews` table implements the SM2 (SuperMemo 2) algorithm:

```sql
CREATE TABLE flashcard_reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    flashcard_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT,              -- 0-5 quality of response
    next_review_at DATETIME,     -- When to review next
    interval_days INT DEFAULT 1, -- Days until next review
    ease_factor DECIMAL(4,2) DEFAULT 2.50, -- SM2 ease factor
    review_count INT DEFAULT 0,  -- Total reviews
    reviewed_at DATETIME,
    UNIQUE KEY unique_user_flashcard (flashcard_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_next_review_at (next_review_at),
    INDEX idx_user_next_review (user_id, next_review_at)
) ENGINE=InnoDB;
```

Query due flashcards efficiently:
```sql
SELECT fc.* FROM flashcards fc
JOIN flashcard_reviews fr ON fc.id = fr.flashcard_id
WHERE fr.user_id = ? AND fr.next_review_at <= NOW()
ORDER BY fr.next_review_at ASC;
```

### User Progress Tracking

```sql
CREATE TABLE user_progress (
    -- ...
    UNIQUE KEY unique_user_system_lesson (user_id, system_id, lesson_id)
);
```

Prevents duplicate progress records and enables efficient upsert operations:

```sql
INSERT INTO user_progress (user_id, system_id, lesson_id, status, confidence)
VALUES (?, ?, ?, 'in_progress', 65)
ON DUPLICATE KEY UPDATE 
    confidence = 65,
    updated_at = NOW();
```

### Lesson Content Organization

Lessons can have multiple sections for detailed organization:

```sql
-- Create lesson
INSERT INTO lessons (system_id, title, slug, content_type, body)
VALUES (1, 'Electrical Overview', 'electrical-overview', 'overview', '...');

-- Add detailed sections
INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order)
VALUES
(1, 'System Overview', '...', 'overview', 1),
(1, 'Components', '...', 'components', 2),
(1, 'Normal Operation', '...', 'normal', 3),
(1, 'Abnormal Situations', '...', 'abnormal', 4),
(1, 'Memory Items', '...', 'memory', 5);
```

## Content Import Format

### systems_index.json

```json
{
  "version": "1.0",
  "app": "Q400 Study System",
  "systems": [
    {
      "id": 1,
      "name": "Electrical Power",
      "slug": "electrical",
      "ata_code": "ATA24"
    }
  ]
}
```

### System Content JSON (electrical.json)

```json
{
  "name": "Electrical Power",
  "slug": "electrical",
  "ata_code": "ATA24",
  "description": "...",
  "full_text": "Complete system description...",
  "sections": [
    {
      "title": "System Overview",
      "type": "overview",
      "content": "..."
    },
    {
      "title": "Components",
      "type": "components",
      "content": "..."
    }
  ]
}
```

The seeder will:
1. Read systems_index.json
2. Find matching system by slug
3. Read system-specific JSON file
4. Create lesson record
5. Create section records
6. Log import status

## Performance Considerations

### Query Optimization

1. **Flashcard Due Reviews** - Uses composite index:
   ```sql
   INDEX idx_user_next_review (user_id, next_review_at)
   ```

2. **User Progress** - Unique constraint enables efficient upserts:
   ```sql
   UNIQUE KEY unique_user_system_lesson (user_id, system_id, lesson_id)
   ```

3. **Revision Schedule** - Similar unique index:
   ```sql
   UNIQUE KEY unique_user_system_lesson (user_id, system_id, lesson_id)
   ```

### JSON Columns

- `key_facts` - Array of critical facts
- `must_know` - Array of essential knowledge
- `exam_traps` - Common exam mistakes
- `tags` - Array of tags
- `options` - Quiz answer options
- `correct_answer` - Correct quiz answer
- `hotspot_overrides` - Diagram state changes

JSON columns allow flexible data without schema changes while remaining queryable.

## Backup & Restore

### Backup Database

```bash
mysqldump -u root -p q400_study > q400_study_backup.sql
```

### Restore Database

```bash
mysql -u root -p < q400_study_backup.sql
```

### Backup Specific Table

```bash
mysqldump -u root -p q400_study users > users_backup.sql
```

## Environment-Based Configuration

The app supports environment-based database configuration:

```bash
# .env file or environment variables
DB_HOST=localhost
DB_PORT=3306
DB_NAME=q400_study
DB_USER=root
DB_PASS=

# Access in config/database.php
return [
    'host' => getenv('DB_HOST') ?? 'localhost',
    'port' => getenv('DB_PORT') ?? 3306,
    'database' => getenv('DB_NAME') ?? 'q400_study',
    'username' => getenv('DB_USER') ?? 'root',
    'password' => getenv('DB_PASS') ?? '',
];
```

## Testing the Setup

### Check Tables Created

```bash
mysql -u root -p q400_study -e "SHOW TABLES;"
```

Should show 30 tables.

### Verify Seed Data

```bash
mysql -u root -p q400_study -e "SELECT COUNT(*) FROM systems;"
```

Should return 22 systems.

### Test Flashcard Query

```bash
mysql -u root -p q400_study -e "
SELECT fc.front, fc.back FROM flashcards fc
JOIN flashcard_reviews fr ON fc.id = fr.flashcard_id
WHERE fr.user_id = 2
LIMIT 5;
"
```

## Troubleshooting

### Connection Issues

Check credentials in config/database.php and verify MySQL is running:
```bash
mysql -u root -p -e "SELECT 1;"
```

### Permission Issues

Ensure database user has full privileges:
```sql
GRANT ALL PRIVILEGES ON q400_study.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Import Errors

Check SQL file syntax and MySQL error log:
```bash
tail -f /var/log/mysql/error.log
```

### Large Data Imports

Increase MySQL limits for large imports:
```sql
SET GLOBAL max_allowed_packet = 256M;
SET GLOBAL net_read_timeout = 3600;
SET GLOBAL net_write_timeout = 3600;
```

## Support

For issues or questions about the database schema, refer to:
- schema.sql - Complete schema with comments
- seed_data.sql - Example data and relationships
- scripts/setup.php - Automated setup process
- scripts/seed_database.php - Content importer
