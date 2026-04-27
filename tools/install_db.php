<?php
/**
 * Import database/schema.sql into the configured DB.
 * Usage:
 *   php tools/install_db.php          # uses config/database.php
 *   php tools/install_db.php --seed   # also imports database/seed_data.sql
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
$cfg = require BASE_PATH . '/config/database.php';

$args  = $argv;
$seed  = in_array('--seed', $args, true);

$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$db = $cfg['database'];
echo "Target DB: $db\n";

// Make sure DB exists, then USE it.
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$db`");

// Run schema.sql, but rewrite any "CREATE DATABASE … / USE …" preamble to our target DB.
$schema = file_get_contents(BASE_PATH . '/database/schema.sql');
if ($schema === false) {
    fwrite(STDERR, "Cannot read database/schema.sql\n");
    exit(1);
}
// Neutralise the schema's hard-coded q400_study DB selection so this script can target ANY DB.
$schema = preg_replace('/^\s*DROP\s+DATABASE\s+IF\s+EXISTS\s+\S+\s*;\s*$/im', '', $schema);
$schema = preg_replace('/^\s*CREATE\s+DATABASE\s+IF\s+NOT\s+EXISTS\s+\S+[^;]*;\s*$/im', '', $schema);
$schema = preg_replace('/^\s*USE\s+\S+\s*;\s*$/im', '', $schema);

try {
    $pdo->exec($schema);
    echo "Schema imported.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Schema import failed: " . $e->getMessage() . "\n");
    exit(1);
}

if ($seed) {
    $seedFile = BASE_PATH . '/database/seed_data.sql';
    if (is_file($seedFile)) {
        $sql = file_get_contents($seedFile);
        $sql = preg_replace('/^\s*USE\s+\S+\s*;\s*$/im', '', $sql);
        try {
            $pdo->exec($sql);
            echo "Seed data imported.\n";
        } catch (Throwable $e) {
            fwrite(STDERR, "Seed failed: " . $e->getMessage() . "\n");
            exit(1);
        }
    } else {
        echo "(no seed file at database/seed_data.sql)\n";
    }
}

echo "Done.\n";
