<?php
/**
 * Create or promote an admin user.
 *
 * Usage:
 *   php tools/create_admin.php email@example.com 'StrongPass2026' "Display Name"
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$cfg = require BASE_PATH . '/config/database.php';

$email = $argv[1] ?? null;
$pass  = $argv[2] ?? null;
$name  = $argv[3] ?? 'Admin';

if (!$email || !$pass) {
    fwrite(STDERR, "Usage: php tools/create_admin.php email password [name]\n");
    exit(1);
}

$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$existing = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$existing->execute([strtolower($email)]);
$row = $existing->fetch();

$hash = password_hash($pass, PASSWORD_BCRYPT);

if ($row) {
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ?, role = "admin", name = ?,
                                              email_verified_at = COALESCE(email_verified_at, NOW())
                            WHERE id = ?');
    $stmt->execute([$hash, $name, $row['id']]);
    echo "Updated existing user (id={$row['id']}) → admin role.\n";
} else {
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, role, email_verified_at, created_at)
         VALUES (?, ?, ?, "admin", NOW(), NOW())'
    );
    $stmt->execute([$name, strtolower($email), $hash]);
    $id = $pdo->lastInsertId();
    echo "Created admin user id={$id}.\n";
}
