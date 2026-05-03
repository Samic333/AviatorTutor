<?php
/**
 * Idempotent migration runner.
 *
 * Tracks applied migration filenames in a schema_migrations table so a
 * second run is a safe no-op. Glob-orders by filename (the project's
 * existing convention is YYYY_MM_DD_*.sql, which sorts correctly).
 *
 * Usage:
 *   php tools/migrate.php             # run every pending migration
 *   php tools/migrate.php --dry-run   # list what would run, don't execute
 *   php tools/migrate.php --status    # show applied vs pending
 *   php tools/migrate.php --rerun=NAME # force-rerun one migration (rare)
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$opts = [
    'dry'    => in_array('--dry-run', $argv, true),
    'status' => in_array('--status',  $argv, true),
    'rerun'  => null,
];
foreach ($argv as $a) {
    if (str_starts_with((string)$a, '--rerun=')) {
        $opts['rerun'] = substr((string)$a, 8);
    }
}

$cfg = require BASE_PATH . '/config/database.php';
$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        filename   VARCHAR(255) PRIMARY KEY,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = [];
foreach ($pdo->query('SELECT filename FROM schema_migrations') as $row) {
    $applied[$row['filename']] = true;
}

$dir   = BASE_PATH . '/database/migrations';
$files = glob($dir . '/*.sql') ?: [];
sort($files);

$pending = array_filter($files, static function ($f) use ($applied) {
    return !isset($applied[basename($f)]);
});

if ($opts['status']) {
    echo "Applied (" . count($applied) . "):\n";
    foreach (array_keys($applied) as $name) echo "  ✓ {$name}\n";
    echo "\nPending (" . count($pending) . "):\n";
    foreach ($pending as $f) echo "  · " . basename($f) . "\n";
    exit(0);
}

if ($opts['rerun']) {
    $target = BASE_PATH . '/database/migrations/' . basename((string)$opts['rerun']);
    if (!is_file($target)) {
        fwrite(STDERR, "Not found: {$target}\n");
        exit(2);
    }
    echo "Force-rerunning " . basename($target) . "…\n";
    runMigration($pdo, $target);
    $pdo->prepare('REPLACE INTO schema_migrations (filename, applied_at) VALUES (?, NOW())')
        ->execute([basename($target)]);
    echo "Done.\n";
    exit(0);
}

if (empty($pending)) {
    echo "Up to date — no pending migrations.\n";
    exit(0);
}

echo "Pending migrations:\n";
foreach ($pending as $f) echo "  · " . basename($f) . "\n";

if ($opts['dry']) { echo "\n--dry-run: nothing executed.\n"; exit(0); }

$failed = [];
foreach ($pending as $f) {
    $name = basename($f);
    echo "\n→ {$name}\n";
    try {
        runMigration($pdo, $f);
        $pdo->prepare('INSERT INTO schema_migrations (filename) VALUES (?)')
            ->execute([$name]);
        echo "  ✓ applied\n";
    } catch (\Throwable $e) {
        echo "  ✗ failed: " . $e->getMessage() . "\n";
        $failed[] = $name;
        // Stop on first failure so later migrations don't compound the
        // damage; the runbook tells the operator to fix and re-run.
        break;
    }
}

if (!empty($failed)) {
    echo "\nFAILED: " . implode(', ', $failed) . "\n";
    exit(1);
}
echo "\nAll migrations applied.\n";

function runMigration(PDO $pdo, string $path): void
{
    $sql = (string) file_get_contents($path);
    if ($sql === '') throw new \RuntimeException("empty migration: {$path}");

    // Split on bare end-of-statement semicolons (same logic as the
    // existing tools/run_migration.php — safe for our migrations).
    $statements = preg_split('/;\s*\R/u', $sql) ?: [];
    foreach ($statements as $stmt) {
        $stripped = (string) preg_replace('/^\s*--.*$/m', '', $stmt);
        $stripped = trim($stripped);
        if ($stripped === '') continue;
        $pdo->exec($stripped);
    }
}
