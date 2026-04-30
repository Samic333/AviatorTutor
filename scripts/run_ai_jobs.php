<?php
/**
 * Cron worker — run one queued AI generation job per tick.
 *
 * Recommended cPanel cron line (every minute):
 *   * * * * * /usr/local/bin/php /home/fruinxrj/aviatortutor/scripts/run_ai_jobs.php >> /home/fruinxrj/aviatortutor/storage/logs/ai-jobs.log 2>&1
 *
 * Adjust the absolute paths to match the deploy host. The script picks
 * the oldest queued job, atomically marks it running, runs the full
 * extract → call → parse → write-drafts pipeline, and exits. PHP's
 * default max_execution_time on cPanel is comfortably above one Claude
 * call so we don't bother streaming partial output.
 */

declare(strict_types=1);

// Resolve project root from this file's location (scripts/run_ai_jobs.php → ..)
$projectRoot = realpath(__DIR__ . '/..');
if (!is_string($projectRoot)) {
    fwrite(STDERR, "[ai-jobs] Could not resolve project root.\n");
    exit(2);
}

define('BASE_PATH', $projectRoot);

// Composer autoload (Smalot etc.)
$composer = $projectRoot . '/vendor/autoload.php';
if (is_file($composer)) {
    require_once $composer;
}

// PSR-4 autoloader for App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $rel = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $rel) . '.php';
    if (is_file($file)) require_once $file;
});

require_once BASE_PATH . '/app/Core/DB.php';

// Single-instance guard via flock — prevents two concurrent crons from
// stomping on each other if a job runs longer than a minute.
$lockPath = BASE_PATH . '/storage/cache/ai-jobs.lock';
@mkdir(dirname($lockPath), 0775, true);
$lockFp = fopen($lockPath, 'c');
if ($lockFp === false) {
    fwrite(STDERR, "[ai-jobs] Could not open lock file at {$lockPath}\n");
    exit(2);
}
if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
    // Another worker is running — silent exit.
    exit(0);
}

$logPrefix = '[' . date('c') . '] [ai-jobs] ';
echo $logPrefix . "tick\n";

try {
    $job = \App\Services\AIJobService::claimNextQueued();
    if ($job === null) {
        echo $logPrefix . "nothing queued\n";
        flock($lockFp, LOCK_UN);
        exit(0);
    }
    echo $logPrefix . "running job #" . $job['id']
       . " (mode=" . $job['mode']
       . ", depth=" . $job['analysis_depth']
       . ", admin_user_id=" . $job['admin_user_id'] . ")\n";

    \App\Services\AIJobService::run($job);

    $after = \App\Services\AIJobService::find((int) $job['id']);
    echo $logPrefix . "finished job #" . $job['id']
       . " status=" . ($after['status'] ?? '?')
       . " progress=" . ($after['progress_message'] ?? '?')
       . "\n";
} catch (\Throwable $e) {
    fwrite(STDERR, $logPrefix . "exception: " . $e->getMessage() . "\n");
} finally {
    flock($lockFp, LOCK_UN);
}
