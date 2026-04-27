<?php
/**
 * Phase 10a migration: aircrafts catalog + scope columns.
 *
 * Idempotent — safe to run on a fresh install or against the live DB. Adds:
 *   1. aircrafts table (12 rows seeded from database/seeds/aircrafts.json)
 *   2. aircraft_panels table
 *   3. asset_imports table
 *   4. systems.aircraft_id FK -> aircrafts.id  (existing rows backfilled to Q400)
 *   5. users.preferred_aircraft_id FK -> aircrafts.id (NULL by default)
 *
 * Usage:
 *   php tools/migrate_aircraft.php
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/DB.php';

use App\Core\DB;

$config = require BASE_PATH . '/config/database.php';
$db = DB::instance();

echo "Target DB: {$config['database']}\n";

// ---------- Step 1 — create new tables (idempotent) ----------
$createStatements = [
    'aircrafts' => <<<'SQL'
CREATE TABLE IF NOT EXISTS aircrafts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(64) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL,
    short_name VARCHAR(40) NOT NULL,
    manufacturer VARCHAR(80) NOT NULL,
    category ENUM('regional','narrowbody','widebody','ga','training') NOT NULL,
    status ENUM('live','beta','coming_soon','archived') NOT NULL DEFAULT 'coming_soon',
    tagline VARCHAR(255) NULL,
    description TEXT NULL,
    cockpit_image_path VARCHAR(255) NULL,
    cockpit_poster_path VARCHAR(255) NULL,
    cockpit_model_path VARCHAR(255) NULL,
    hero_image_path VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 100,
    waitlist_count INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status, sort_order),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
    'aircraft_panels' => <<<'SQL'
CREATE TABLE IF NOT EXISTS aircraft_panels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    aircraft_id BIGINT UNSIGNED NOT NULL,
    slug VARCHAR(80) NOT NULL,
    label VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    system_id BIGINT UNSIGNED NULL,
    sprite_path VARCHAR(255) NULL,
    pos_x DECIMAL(6,3) NOT NULL DEFAULT 0,
    pos_y DECIMAL(6,3) NOT NULL DEFAULT 0,
    width DECIMAL(6,3) NOT NULL DEFAULT 10,
    height DECIMAL(6,3) NOT NULL DEFAULT 10,
    sort_order INT NOT NULL DEFAULT 100,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_aircraft_panel (aircraft_id, slug),
    INDEX idx_aircraft (aircraft_id),
    INDEX idx_system (system_id),
    FOREIGN KEY (aircraft_id) REFERENCES aircrafts(id) ON DELETE CASCADE,
    FOREIGN KEY (system_id)   REFERENCES systems(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
    'asset_imports' => <<<'SQL'
CREATE TABLE IF NOT EXISTS asset_imports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    aircraft_id BIGINT UNSIGNED NULL,
    uploader_user_id BIGINT UNSIGNED NULL,
    view_slug VARCHAR(80) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL,
];
foreach ($createStatements as $name => $sql) {
    $db->execute($sql);
    echo "  ensured table {$name}\n";
}

// ---------- Step 2 — seed aircrafts from JSON ----------
$seedFile = BASE_PATH . '/database/seeds/aircrafts.json';
if (!is_file($seedFile)) {
    fwrite(STDERR, "ERROR: missing seed file {$seedFile}\n");
    exit(1);
}
$rows = json_decode((string) file_get_contents($seedFile), true);
if (!is_array($rows)) {
    fwrite(STDERR, "ERROR: aircrafts.json failed to decode\n");
    exit(1);
}

$upsertSql = "INSERT INTO aircrafts (slug, name, short_name, manufacturer, category, status, tagline, description, sort_order)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE
                  name = VALUES(name),
                  short_name = VALUES(short_name),
                  manufacturer = VALUES(manufacturer),
                  category = VALUES(category),
                  status = VALUES(status),
                  tagline = VALUES(tagline),
                  description = VALUES(description),
                  sort_order = VALUES(sort_order)";
foreach ($rows as $r) {
    $db->execute($upsertSql, [
        $r['slug'],
        $r['name'],
        $r['short_name'],
        $r['manufacturer'],
        $r['category'],
        $r['status'],
        $r['tagline']     ?? null,
        $r['description'] ?? null,
        $r['sort_order']  ?? 100,
    ]);
}
echo "  upserted " . count($rows) . " aircrafts\n";

// ---------- Step 3 — add scope columns to systems + users ----------
$dbName = $config['database'];

$ensureColumn = function (string $table, string $column, string $columnDef, ?string $constraintSql = null) use ($db, $dbName) {
    $exists = (int) $db->queryOne(
        'SELECT COUNT(*) c FROM information_schema.COLUMNS
         WHERE table_schema=? AND table_name=? AND column_name=?',
        [$dbName, $table, $column]
    )['c'];
    if ($exists === 0) {
        $db->execute("ALTER TABLE {$table} ADD COLUMN {$columnDef}");
        echo "  added column {$table}.{$column}\n";
        if ($constraintSql !== null) {
            $db->execute($constraintSql);
            echo "  added FK on {$table}.{$column}\n";
        }
    } else {
        echo "  column {$table}.{$column} already present\n";
    }
};

$ensureColumn(
    'systems',
    'aircraft_id',
    'aircraft_id BIGINT UNSIGNED NULL AFTER id',
    'ALTER TABLE systems
        ADD INDEX idx_aircraft (aircraft_id),
        ADD CONSTRAINT fk_systems_aircraft
        FOREIGN KEY (aircraft_id) REFERENCES aircrafts(id) ON DELETE SET NULL'
);

$ensureColumn(
    'users',
    'preferred_aircraft_id',
    'preferred_aircraft_id BIGINT UNSIGNED NULL',
    'ALTER TABLE users
        ADD INDEX idx_preferred_aircraft (preferred_aircraft_id),
        ADD CONSTRAINT fk_users_preferred_aircraft
        FOREIGN KEY (preferred_aircraft_id) REFERENCES aircrafts(id) ON DELETE SET NULL'
);

// ---------- Step 4 — backfill existing systems to Q400 ----------
$q400 = $db->queryOne("SELECT id FROM aircrafts WHERE slug = 'q400'");
if (!$q400) {
    fwrite(STDERR, "ERROR: q400 row missing after seed; aborting backfill\n");
    exit(1);
}
$q400Id = (int) $q400['id'];
$updated = $db->execute(
    'UPDATE systems SET aircraft_id = ? WHERE aircraft_id IS NULL',
    [$q400Id]
);
echo "  backfilled {$updated} systems rows to aircraft_id={$q400Id} (q400)\n";

// ---------- Done ----------
$counts = [
    'aircrafts'        => (int) $db->queryOne('SELECT COUNT(*) c FROM aircrafts')['c'],
    'aircraft_panels'  => (int) $db->queryOne('SELECT COUNT(*) c FROM aircraft_panels')['c'],
    'asset_imports'    => (int) $db->queryOne('SELECT COUNT(*) c FROM asset_imports')['c'],
    'systems'          => (int) $db->queryOne('SELECT COUNT(*) c FROM systems')['c'],
    'systems_q400'     => (int) $db->queryOne('SELECT COUNT(*) c FROM systems WHERE aircraft_id = ?', [$q400Id])['c'],
];
echo "Done. Counts: " . json_encode($counts) . "\n";
