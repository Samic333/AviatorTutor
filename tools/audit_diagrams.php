<?php
/**
 * Diagram audit — walks every published lesson_slides row, joins to lessons
 * → systems, and reports whether the slide's media_url plausibly matches the
 * system being taught. The known-good live SVG set is enumerated below; rows
 * pointing at a different system's SVG, or at a non-existent file, are flagged.
 *
 * Usage:
 *   php tools/audit_diagrams.php                    # CSV to stdout
 *   php tools/audit_diagrams.php --out=path.csv     # CSV to file
 *   php tools/audit_diagrams.php --apply            # also UPDATEs media_url
 *                                                     to expected for clear
 *                                                     slug-vs-url mismatches
 *                                                     and sets diagram_verified
 *                                                     for matches
 *   php tools/audit_diagrams.php --summary          # one-line health summary
 *
 * Exit code: 0 if no mismatches, 1 if any mismatch found (so CI can gate).
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$opts = [
    'out'     => null,
    'apply'   => in_array('--apply', $argv, true),
    'summary' => in_array('--summary', $argv, true),
];
foreach ($argv as $a) {
    if (str_starts_with((string)$a, '--out=')) {
        $opts['out'] = substr((string)$a, 6);
    }
}

$cfg = require BASE_PATH . '/config/database.php';
$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Canonical mapping: system slug → expected media_url. Hydraulic uses a
// hand-curated PNG bundle from /assets/uploads/hydraulics/* — any URL in
// that namespace is considered correct.
$expected = [
    'electrical'           => '/assets/aircraft/q400/electrical-flow.svg',
    'hydraulic'            => '/assets/uploads/hydraulics/',
    'fuel'                 => '/assets/aircraft/q400/fuel-flow.svg',
    'powerplant'           => '/assets/aircraft/q400/powerplant-flow.svg',
    'propeller'            => '/assets/aircraft/q400/propeller-flow.svg',
    'flight-controls'      => '/assets/aircraft/q400/flight-controls-flow.svg',
    'landing-gear'         => '/assets/aircraft/q400/landing-gear-flow.svg',
    'air-cond-press'       => '/assets/aircraft/q400/air-cond-press-flow.svg',
    'pneumatics'           => '/assets/aircraft/q400/pneumatics-flow.svg',
    'ice-rain'             => '/assets/aircraft/q400/ice-rain-flow.svg',
    'fire-protection'      => '/assets/aircraft/q400/fire-protection-flow.svg',
    'autoflight'           => '/assets/aircraft/q400/autoflight-flow.svg',
    'navigation'           => '/assets/aircraft/q400/navigation-flow.svg',
    'communications'       => '/assets/aircraft/q400/communications-flow.svg',
    'indicating-recording' => '/assets/aircraft/q400/indicating-recording-flow.svg',
    'oxygen'               => '/assets/aircraft/q400/oxygen-flow.svg',
    'lighting'             => '/assets/aircraft/q400/lighting-flow.svg',
    'aeroplane-general'    => '/assets/aircraft/q400/aeroplane-general-flow.svg',
    'fms'                  => '/assets/aircraft/q400/navigation-flow.svg', // FMS shares the nav diagram
    'caution-warning'      => null, // text-only; any media_url is suspect
    'du-messages'          => null,
    'qrh'                  => null,
];

// Pull every slide with media. NULL/none media skipped — that's fine.
try {
    $rows = $pdo->query(
        "SELECT s.id          AS slide_id,
                s.lesson_id,
                s.sort_order,
                s.title       AS slide_title,
                s.media_type,
                s.media_url,
                s.diagram_verified,
                l.title       AS lesson_title,
                sys.slug      AS system_slug,
                sys.name      AS system_name
           FROM lesson_slides s
           JOIN lessons l   ON l.id   = s.lesson_id
           JOIN systems sys ON sys.id = l.system_id
          WHERE s.media_type IN ('image','diagram')
            AND s.media_url IS NOT NULL
            AND s.media_url <> ''
          ORDER BY sys.slug, l.id, s.sort_order"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    fwrite(STDERR, "Audit query failed: {$e->getMessage()}\n");
    exit(2);
}

$report   = [];
$mismatch = 0;
$missing  = 0;
$ok       = 0;
$updates  = [];

foreach ($rows as $r) {
    $slug = (string) $r['system_slug'];
    $url  = (string) $r['media_url'];
    $exp  = $expected[$slug] ?? null;

    [$status, $reason] = audit_one($slug, $url, $exp);

    if ($status === 'ok')       $ok++;
    if ($status === 'missing')  $missing++;
    if ($status === 'mismatch') $mismatch++;

    // For a clear slug-vs-url mismatch where we have an exact expected file,
    // queue an UPDATE to the canonical URL.
    if ($status === 'mismatch' && is_string($exp) && !str_ends_with($exp, '/')) {
        $updates[] = ['slide_id' => (int)$r['slide_id'], 'expected' => $exp];
    }

    $report[] = [
        'slide_id'        => (int) $r['slide_id'],
        'lesson_id'       => (int) $r['lesson_id'],
        'system_slug'     => $slug,
        'system_name'     => (string) $r['system_name'],
        'lesson_title'    => (string) $r['lesson_title'],
        'slide_title'     => (string) $r['slide_title'],
        'current_url'     => $url,
        'expected_url'    => is_string($exp) ? $exp : ($exp === null ? '(no diagram expected)' : ''),
        'status'          => $status,
        'reason'          => $reason,
        'verified_now'    => (int) $r['diagram_verified'],
    ];
}

if ($opts['summary']) {
    printf("audited=%d ok=%d mismatch=%d missing=%d\n", count($rows), $ok, $mismatch, $missing);
    exit($mismatch === 0 && $missing === 0 ? 0 : 1);
}

$out  = $opts['out'] !== null ? fopen($opts['out'], 'w') : STDOUT;
fputcsv($out, array_keys($report[0] ?? ['slide_id'=>'']));
foreach ($report as $row) fputcsv($out, $row);
if ($opts['out'] !== null) fclose($out);

if ($opts['apply']) {
    if ($updates) {
        echo "\nApplying " . count($updates) . " URL fixes…\n";
        $upd = $pdo->prepare(
            'UPDATE lesson_slides
                SET media_url           = ?,
                    diagram_verified    = 1,
                    diagram_audit_notes = "auto-fixed by audit_diagrams.php"
              WHERE id = ?'
        );
        foreach ($updates as $u) $upd->execute([$u['expected'], $u['slide_id']]);
    }
    // Mark already-correct rows as verified.
    $okIds = array_map(static fn($r) => $r['slide_id'], array_filter($report, static fn($r) => $r['status'] === 'ok'));
    if ($okIds) {
        $place = implode(',', array_fill(0, count($okIds), '?'));
        $pdo->prepare("UPDATE lesson_slides SET diagram_verified = 1 WHERE id IN ($place)")->execute($okIds);
    }
    echo "Done. Re-run without --apply to confirm.\n";
}

exit(($mismatch + $missing) === 0 ? 0 : 1);

/**
 * Decide if a slide's media_url is correct for its system.
 *
 * @param string $slug System slug.
 * @param string $url  Current media URL.
 * @param mixed  $exp  Expected URL (string), prefix (string ending /), or null.
 * @return array{0:string,1:string} [status, reason]. Status: ok|mismatch|missing|unknown.
 */
function audit_one(string $slug, string $url, mixed $exp): array
{
    if ($exp === null) {
        // Text-only system; any media URL is just suspicious, not wrong.
        return ['unknown', "system '{$slug}' isn't expected to ship a diagram — review manually"];
    }
    if (is_string($exp) && str_ends_with($exp, '/')) {
        // Prefix match (hydraulics PNG bundle).
        if (str_starts_with($url, $exp)) {
            return file_on_disk($url) ? ['ok', ''] : ['missing', "file not found on disk: {$url}"];
        }
        return ['mismatch', "expected URL to start with '{$exp}', got '{$url}'"];
    }
    if (!file_on_disk($url)) {
        return ['missing', "file not found on disk: {$url}"];
    }
    if ($url === $exp) {
        return ['ok', ''];
    }
    // The URL exists but points at a different system — most damaging case.
    return ['mismatch', "system '{$slug}' should use '{$exp}', got '{$url}'"];
}

function file_on_disk(string $url): bool
{
    $rel = ltrim(parse_url($url, PHP_URL_PATH) ?: $url, '/');
    return is_file(BASE_PATH . '/public/' . $rel);
}
