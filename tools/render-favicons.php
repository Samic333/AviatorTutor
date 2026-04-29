<?php
/**
 * Render PNG favicon variants from the AviatorTutor mark.
 *
 *   php tools/render-favicons.php
 *
 * Outputs:
 *   public/favicon-32.png         (32x32, navy bg + sky A glyph + gold pip)
 *   public/apple-touch-icon.png   (180x180, same design)
 *
 * Geometry matches public/favicon.svg so the SVG and PNG fallbacks render
 * the same brand mark across browsers.
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

    $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefilledrectangle($im, 0, 0, $size, $size, $transparent);
    imagealphablending($im, true);

    $navy   = imagecolorallocate($im, 0x0F, 0x17, 0x2A);
    $skyBg  = imagecolorallocate($im, 0x1E, 0x29, 0x3B);
    $sky    = imagecolorallocate($im, 0x38, 0xBD, 0xF8);
    $gold   = imagecolorallocate($im, 0xF2, 0xC9, 0x4C);

    // Rounded-rect navy background
    $r = (int) round($size * 0.22);
    imagefilledrectangle($im, $r, 0, $size - $r, $size, $navy);
    imagefilledrectangle($im, 0, $r, $size, $size - $r, $navy);
    imagefilledellipse($im, $r,         $r,         $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $size - $r, $r,         $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $r,         $size - $r, $r * 2, $r * 2, $navy);
    imagefilledellipse($im, $size - $r, $size - $r, $r * 2, $r * 2, $navy);

    $cx = (int) ($size / 2);
    $cy = (int) ($size / 2);
    $ringR = (int) ($size * 0.42);
    imageellipse($im, $cx, $cy, $ringR * 2, $ringR * 2, $skyBg);

    $stroke = max(2, (int) round($size * 0.075));
    imagesetthickness($im, $stroke);

    // A-glyph: two diagonals from base to apex (in viewBox 0..64, apex at (32,12), base at (14,50)..(50,50))
    $apexX = $cx;
    $apexY = (int) ($size * 12 / 64);
    $leftBaseX  = (int) ($size * 14 / 64);
    $rightBaseX = (int) ($size * 50 / 64);
    $baseY      = (int) ($size * 50 / 64);

    imageline($im, $leftBaseX,  $baseY, $apexX, $apexY, $sky);
    imageline($im, $rightBaseX, $baseY, $apexX, $apexY, $sky);

    // Horizon bar
    $hY  = (int) ($size * 40 / 64);
    $hX1 = (int) ($size * 22 / 64);
    $hX2 = (int) ($size * 42 / 64);
    imageline($im, $hX1, $hY, $hX2, $hY, $sky);

    // Gold altitude pip at apex
    $pipR = max(2, (int) round($size * 0.05));
    imagefilledellipse($im, $apexX, $apexY + (int)($size * 0.015), $pipR * 2, $pipR * 2, $gold);

    imagepng($im, $outPath);
    imagedestroy($im);
    echo "  wrote $outPath ({$size}x{$size})\n";
}

$pubDir = __DIR__ . '/../public';
renderFavicon(32,  $pubDir . '/favicon-32.png');
renderFavicon(180, $pubDir . '/apple-touch-icon.png');
echo "done\n";
