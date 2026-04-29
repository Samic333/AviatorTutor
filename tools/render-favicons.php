<?php
/**
 * Render PNG favicon variants from a procedural design (no external deps).
 *
 *   php tools/render-favicons.php
 *
 * Outputs:
 *   public/favicon-32.png         (32x32, navy bg + sky airplane wing-stripe)
 *   public/apple-touch-icon.png   (180x180, same design)
 *
 * GD is used so this runs everywhere PHP is installed. The artwork is a
 * navy rounded square with a stylised aviator wing-stripe in sky-blue —
 * legible at 16/32px because we draw geometric primitives, not the complex
 * multi-segment path that's used in favicon.svg for browsers that support it.
 */

declare(strict_types=1);

if (!function_exists('imagecreatetruecolor')) {
    fwrite(STDERR, "GD extension required.\n");
    exit(1);
}

function renderFavicon(int $size, string $outPath): void
{
    $im = imagecreatetruecolor($size, $size);
    imagesavealpha($im, true);
    imagealphablending($im, false);

    // Transparent fill first
    $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefilledrectangle($im, 0, 0, $size, $size, $transparent);
    imagealphablending($im, true);

    $navy   = imagecolorallocate($im, 0x0F, 0x17, 0x2A);
    $skyBg  = imagecolorallocate($im, 0x1E, 0x29, 0x3B);
    $sky    = imagecolorallocate($im, 0x38, 0xBD, 0xF8);
    $skyHi  = imagecolorallocate($im, 0x7D, 0xD3, 0xFC);

    // Rounded-rect background — approximate with filled rect + corner cuts.
    $r = (int) round($size * 0.22);
    imagefilledrectangle($im, $r, 0, $size - $r, $size, $navy);
    imagefilledrectangle($im, 0, $r, $size, $size - $r, $navy);
    imagefilledellipse($im, $r,           $r,           $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $size - $r,   $r,           $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $r,           $size - $r,   $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $size - $r,   $size - $r,   $r * 2, $r * 2, $navy);

    // Subtle inner ring
    $cx = $cy = $size / 2;
    $ringR = (int) ($size * 0.42);
    imageellipse($im, (int) $cx, (int) $cy, $ringR * 2, $ringR * 2, $skyBg);

    // Aviator wing-stripe: a chevron pointing upper-right, drawn with
    // anti-aliased filled polygons.
    imagesetthickness($im, max(1, (int) round($size * 0.06)));

    // Centre dot
    $dotR = (int) max(2, $size * 0.08);
    imagefilledellipse($im, (int) $cx, (int) $cy, $dotR * 2, $dotR * 2, $sky);

    // Wings: two upward-tilted strokes
    $wingHalf = (int) ($size * 0.30);
    $wingTop  = (int) ($size * 0.34);
    $wingBot  = (int) ($size * 0.50);
    // Left wing
    imageline($im, (int) $cx - $wingHalf, $wingTop + (int) ($size * 0.08), (int) $cx - 2, $wingBot, $sky);
    imageline($im, (int) $cx - $wingHalf, $wingTop + (int) ($size * 0.10), (int) $cx - 2, $wingBot - 2, $skyHi);
    // Right wing
    imageline($im, (int) $cx + $wingHalf, $wingTop + (int) ($size * 0.08), (int) $cx + 2, $wingBot, $sky);
    imageline($im, (int) $cx + $wingHalf, $wingTop + (int) ($size * 0.10), (int) $cx + 2, $wingBot - 2, $skyHi);

    // Vertical fuselage line (short)
    imageline($im, (int) $cx, (int) ($size * 0.32), (int) $cx, (int) ($size * 0.72), $sky);
    // Tail
    imageline($im, (int) $cx - (int) ($size * 0.10), (int) ($size * 0.72),
                   (int) $cx + (int) ($size * 0.10), (int) ($size * 0.72), $sky);

    imagepng($im, $outPath);
    imagedestroy($im);
    echo "  wrote $outPath ({$size}x{$size})\n";
}

$pubDir = __DIR__ . '/../public';
renderFavicon(32,  $pubDir . '/favicon-32.png');
renderFavicon(180, $pubDir . '/apple-touch-icon.png');
echo "done\n";
