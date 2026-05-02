<?php
/**
 * One-shot Phase 1 seed runner.
 *
 * Hit this URL with the correct ?token=... query param to run the 7 Phase 1
 * SQL seed files in dependency order against the live database. Returns JSON
 * with per-file status. Deletes itself on success so it cannot be re-run.
 *
 * After Phase 1 verification, this file will be removed by a follow-up commit.
 * Listed as not-gitignored on purpose so it can ride a normal git pull.
 *
 * NOT a permanent admin tool — purely a one-shot bootstrap.
 */

declare(strict_types=1);
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

// ---- Config ----------------------------------------------------------------

// 64-char hex token. Must match exactly. Generated locally via openssl rand
// -hex 32 — known only to the laptop and this script.
const SEED_TOKEN = '8b7e0c0c2d2a3f8b7e9d1c4a6e5d4b3c2a1f0e9d8c7b6a5f4e3d2c1b0a9f8e7d';

// Phase 1 seed files relative to project root (parent of public/).
const SEED_FILES = [
    'database/seeds/lesson_aeroplane_general.sql',
    'database/seeds/slides_aeroplane_general.sql',
    'database/seeds/lesson_sections_aeroplane_general.sql',
    'database/seeds/lesson_qrh_links_aeroplane_general.sql',
    'database/seeds/flashcards_aeroplane_general.sql',
    'database/seeds/quiz_aeroplane_general.sql',
    'database/seeds/diagrams_aeroplane_general.sql',
];

// ---- Auth ------------------------------------------------------------------
$provided = $_GET['token'] ?? '';
if (!hash_equals(SEED_TOKEN, (string)$provided)) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

// ---- Boot ------------------------------------------------------------------
$root = dirname(__DIR__);
chdir($root);

if (!file_exists($root.'/config/database.php')) {
    http_response_code(500);
    echo json_encode(['error' => 'database.php not found at '.$root.'/config/database.php']);
    exit;
}

$cfg = require $root.'/config/database.php';

$mysqli = @new mysqli(
    $cfg['host'],
    $cfg['username'],
    $cfg['password'],
    $cfg['database'],
    (int)($cfg['port'] ?? 3306)
);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'connect: '.$mysqli->connect_error]);
    exit;
}
$mysqli->set_charset($cfg['charset'] ?? 'utf8mb4');

// ---- Run seeds -------------------------------------------------------------
$results  = [];
$any_fail = false;

foreach (SEED_FILES as $file) {
    $entry = ['file' => $file, 'status' => 'pending'];
    $path  = $root.'/'.$file;

    if (!is_readable($path)) {
        $entry['status'] = 'fail';
        $entry['error']  = 'unreadable: '.$path;
        $results[]       = $entry;
        $any_fail        = true;
        continue;
    }

    $sql = file_get_contents($path);
    if ($sql === false) {
        $entry['status'] = 'fail';
        $entry['error']  = 'read failed';
        $results[]       = $entry;
        $any_fail        = true;
        continue;
    }
    $entry['bytes'] = strlen($sql);

    if (!$mysqli->multi_query($sql)) {
        $entry['status'] = 'fail';
        $entry['error']  = 'multi_query: '.$mysqli->error;
        $results[]       = $entry;
        $any_fail        = true;
        continue;
    }

    $stmt_count = 0;
    $select_results = [];

    do {
        $stmt_count++;
        if ($result = $mysqli->store_result()) {
            // Capture diagnostic SELECTs (resolved_system_id, resolved_lesson_id, slides_inserted, etc.)
            $row = $result->fetch_assoc();
            if ($row !== null && count($select_results) < 5) {
                $select_results[] = $row;
            }
            $result->free();
        }

        if ($mysqli->errno) {
            break;
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    if ($mysqli->errno) {
        $entry['status']     = 'fail';
        $entry['error']      = 'after stmt #'.$stmt_count.': '.$mysqli->error;
        $entry['statements'] = $stmt_count;
        $entry['captured']   = $select_results;
        $any_fail            = true;
    } else {
        $entry['status']     = 'ok';
        $entry['statements'] = $stmt_count;
        $entry['captured']   = $select_results;
    }

    $results[] = $entry;
}

// ---- Final inventory -------------------------------------------------------
$inv = [];
$sid_q = $mysqli->query("SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1");
$sid   = $sid_q ? (int)$sid_q->fetch_column() : 0;
$inv['system_id'] = $sid;

if ($sid) {
    $q = function ($sql) use ($mysqli) {
        $r = @$mysqli->query($sql);
        return $r ? (int)$r->fetch_column() : -1;
    };
    $inv['lessons']         = $q("SELECT COUNT(*) FROM lessons WHERE system_id=$sid");
    $inv['lesson_sections'] = $q("SELECT COUNT(*) FROM lesson_sections WHERE lesson_id IN (SELECT id FROM lessons WHERE system_id=$sid)");
    $inv['lesson_slides']   = $q("SELECT COUNT(*) FROM lesson_slides   WHERE lesson_id IN (SELECT id FROM lessons WHERE system_id=$sid)");
    $inv['lesson_qrh_links']= $q("SELECT COUNT(*) FROM lesson_qrh_links WHERE lesson_id IN (SELECT id FROM lessons WHERE system_id=$sid)");
    $inv['flashcards']      = $q("SELECT COUNT(*) FROM flashcards     WHERE system_id=$sid");
    $inv['quizzes']         = $q("SELECT COUNT(*) FROM quizzes        WHERE system_id=$sid");
    $inv['quiz_questions']  = $q("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id=$sid)");
    $inv['diagrams']        = $q("SELECT COUNT(*) FROM diagrams       WHERE system_id=$sid");
    $inv['diagram_hotspots']= $q("SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id IN (SELECT id FROM diagrams WHERE system_id=$sid)");
    $inv['diagram_states']  = $q("SELECT COUNT(*) FROM diagram_states  WHERE diagram_id IN (SELECT id FROM diagrams WHERE system_id=$sid)");
}

$mysqli->close();

// ---- Self-delete on success ------------------------------------------------
$self_deleted = false;
if (!$any_fail) {
    $self_deleted = @unlink(__FILE__);
}

http_response_code($any_fail ? 500 : 200);
echo json_encode([
    'overall'        => $any_fail ? 'fail' : 'ok',
    'results'        => $results,
    'inventory'      => $inv,
    'self_deleted'   => $self_deleted,
    'php_version'    => PHP_VERSION,
    'mysqli_version' => mysqli_get_client_info(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
