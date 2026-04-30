<?php
/**
 * Run a single .sql migration file against the configured DB.
 *
 * Usage:
 *   php tools/run_migration.php database/migrations/2026_04_30_ai_pipeline.sql
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if ($argc < 2) {
    fwrite(STDERR, "Usage: php tools/run_migration.php <relative-path-to-migration.sql>\n");
    exit(1);
}

$rel  = (string) $argv[1];
$path = BASE_PATH . '/' . ltrim($rel, '/');
if (!is_file($path) || !is_readable($path)) {
    fwrite(STDERR, "Migration not found or unreadable: $path\n");
    exit(1);
}

$cfg = require BASE_PATH . '/config/database.php';
$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

echo "Target DB: {$cfg['database']}\nMigration: $rel\n";

$sql = (string) file_get_contents($path);
if ($sql === '') {
    fwrite(STDERR, "Migration file is empty: $path\n");
    exit(1);
}

// Split on bare semicolons that end a statement. The migration files we
// write don't embed semicolons inside string literals, so a simple split
// is safe enough.
$rawStatements = preg_split('/;\s*\R/u', $sql) ?: [];

$count = 0;
foreach ($rawStatements as $i => $stmt) {
    // Strip whole-line `--` comments AND blank lines so we can tell whether
    // there's any executable SQL left in this chunk.
    $stripped = (string) preg_replace('/^\s*--.*$/m', '', $stmt);
    $stripped = trim($stripped);
    if ($stripped === '') {
        continue; // pure comment / whitespace
    }

    // Hand the original chunk (comments included) to the driver — MySQL
    // is happy to ignore them, and keeping them preserves error context.
    try {
        $pdo->exec($stmt);
        $count++;
    } catch (\PDOException $e) {
        // Tolerate "Duplicate column" / "Duplicate key" / "already exists"
        // so the migration is idempotent on hosts whose ALTER ... IF NOT
        // EXISTS isn't supported.
        $msg = $e->getMessage();
        if (preg_match('/Duplicate (column|key|index|entry)|already exists/i', $msg)) {
            fwrite(STDERR, "  (skip) $msg\n");
            continue;
        }
        fwrite(STDERR, "Statement #" . ($i + 1) . " failed:\n$stmt\n\n$msg\n");
        exit(1);
    }
}

echo "Applied $count statements.\n";
