<?php
/**
 * CLI: bulk-generate activation codes.
 *
 * Usage:
 *   php tools/generate_codes.php --count=10 --days=30 --plan=monthly
 *   php tools/generate_codes.php --count=5  --days=7  --notes='Friends & family'
 *
 * Output: prints each new code formatted as XXXX-XXXX-XXXX-XXXX, one per line.
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/Core/DB.php';
require_once BASE_PATH . '/app/Services/ActivationCodeService.php';

$opts = getopt('', ['count::', 'days::', 'plan::', 'admin::', 'notes::']);
$count = (int) ($opts['count'] ?? 5);
$days  = (int) ($opts['days']  ?? 30);
$plan  = (string) ($opts['plan'] ?? 'monthly');
$admin = isset($opts['admin']) ? (int) $opts['admin'] : null;
$notes = $opts['notes'] ?? null;

if ($count < 1 || $count > 1000) {
    fwrite(STDERR, "count must be between 1 and 1000\n");
    exit(1);
}
if ($days < 1 || $days > 366) {
    fwrite(STDERR, "days must be between 1 and 366\n");
    exit(1);
}

$created = \App\Services\ActivationCodeService::generate($count, $days, $plan, $admin, $notes);

echo "Generated " . count($created) . " activation codes ($plan, $days days):\n";
foreach ($created as $row) {
    echo "  {$row['code']}\n";
}
