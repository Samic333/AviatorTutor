<?php
/**
 * Cockpit image processor — CLI fallback for the /admin/cockpits UI.
 *
 * Usage:
 *   php tools/process_cockpit.php \
 *       --aircraft=q400 \
 *       --src=/tmp/q400-cockpit.png \
 *       --mask=20,20,180,160        # one or more --mask args allowed
 *       [--crop=auto|x,y,w,h]       # default = auto
 *       [--width=2400]              # main output width (default 2400)
 *       [--poster-width=1280]       # poster width
 *
 * Writes:
 *   public/assets/aircraft/<slug>/cockpit.webp        (main, 2400 wide)
 *   public/assets/aircraft/<slug>/cockpit-2x.webp     (alias)
 *   public/assets/aircraft/<slug>/cockpit-poster.webp (1280 wide)
 *
 * And updates aircrafts.cockpit_image_path / cockpit_poster_path.
 * Records a row in asset_imports for audit.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Tiny custom autoloader (mirrors public/index.php).
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $rel = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $rel) . '.php';
    if (is_file($file)) require_once $file;
});
require_once BASE_PATH . '/app/Core/DB.php';

use App\Core\DB;
use App\Services\CockpitImageService;
use App\Services\AircraftService;

// ---------- Arg parsing ----------
$opts = ['aircraft' => null, 'src' => null, 'crop' => 'auto', 'width' => 2400, 'poster-width' => 1280];
$masks = [];
foreach ($argv as $i => $arg) {
    if ($i === 0) continue;
    if (preg_match('/^--([a-z\-]+)=(.+)$/', (string) $arg, $m)) {
        $key = $m[1]; $val = $m[2];
        if ($key === 'mask') {
            $parts = array_map('intval', explode(',', $val));
            if (count($parts) === 4) $masks[] = $parts;
        } else {
            $opts[$key] = $val;
        }
    }
}
if (!$opts['aircraft'] || !$opts['src']) {
    fwrite(STDERR, "Usage: php tools/process_cockpit.php --aircraft=<slug> --src=<path> [--crop=auto|x,y,w,h] [--mask=x,y,w,h ...]\n");
    exit(2);
}

$aircraft = AircraftService::bySlug((string) $opts['aircraft']);
if (!$aircraft) {
    fwrite(STDERR, "ERROR: aircraft '{$opts['aircraft']}' not found in catalog.\n");
    exit(1);
}

$crop = null;
if ($opts['crop'] !== 'auto') {
    $parts = array_map('intval', explode(',', (string) $opts['crop']));
    if (count($parts) === 4) $crop = $parts;
}

$outDir = BASE_PATH . '/public/assets/aircraft/' . $aircraft['slug'];

echo "Processing {$opts['src']} -> {$outDir}\n";
echo "  crop=" . ($crop === null ? 'auto' : implode(',', $crop)) . "\n";
echo "  masks=" . count($masks) . "\n";

$service = new CockpitImageService();
$result = $service->process(
    $opts['src'],
    $outDir,
    $crop,
    $masks,
    (int) $opts['width'],
    (int) $opts['poster-width']
);

$webPath  = '/assets/aircraft/' . $aircraft['slug'] . '/cockpit.webp';
$posterPath = '/assets/aircraft/' . $aircraft['slug'] . '/cockpit-poster.webp';

DB::instance()->execute(
    'UPDATE aircrafts SET cockpit_image_path = ?, cockpit_poster_path = ? WHERE id = ?',
    [$webPath, $posterPath, (int) $aircraft['id']]
);

DB::instance()->execute(
    'INSERT INTO asset_imports (aircraft_id, view_slug, source_filename, source_bytes, output_path, crop_rect_json, mask_rects_json, note)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
    [
        (int) $aircraft['id'],
        'cockpit',
        basename((string) $opts['src']),
        (int) (filesize((string) $opts['src']) ?: 0),
        $webPath,
        json_encode($result['crop']),
        json_encode($result['masks']),
        'CLI process_cockpit',
    ]
);

echo "Done.\n";
echo "  out         = {$result['out']}  ({$result['bytes']} bytes)\n";
echo "  out_poster  = {$result['out_poster']}  ({$result['poster_bytes']} bytes)\n";
echo "  crop        = " . json_encode($result['crop']) . "\n";
echo "  webPath     = {$webPath}\n";
