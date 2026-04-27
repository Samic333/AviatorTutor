<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Phase 10e — process raw cockpit images into clean WebPs.
 *
 *   1. Auto-detect whitespace borders (luminance-scan from each edge).
 *   2. Optionally mask out logo / watermark regions with a sampled background fill.
 *   3. Crop, downscale to 2x retina + a smaller poster, write WebPs to disk.
 *   4. Return a manifest the controller can persist to aircrafts.cockpit_*_path
 *      and asset_imports.
 *
 * Pure GD — works on any Namecheap shared host where PHP 8.2 is compiled with
 * `--with-webp`. No ImageMagick dependency.
 */
final class CockpitImageService
{
    /** Pixels above this 0..1 luminance count as "whitespace" during auto-crop. */
    private const WHITE_THRESHOLD = 0.96;

    /** A row/column is "white" if at least this fraction of its pixels exceed the threshold. */
    private const WHITE_RATIO = 0.985;

    /** Margin to leave around the auto-crop in pixels. */
    private const MARGIN_PX = 16;

    /**
     * Process a single source image.
     *
     * @param string $sourcePath Absolute path to the input PNG/JPG/WebP.
     * @param string $outDir     Absolute directory to write the WebPs into. Created if missing.
     * @param array{0:int,1:int,2:int,3:int}|null $cropOverride [x,y,w,h] in source-pixel coords; null = auto.
     * @param array<int,array{0:int,1:int,2:int,3:int}> $maskRects Logo / watermark regions in SOURCE-pixel coords.
     *                                                              Each is [x,y,w,h]. Filled with sampled background.
     * @param int $maxWidth Output image max width in pixels (default 2400 for retina).
     * @param int $posterWidth Smaller poster width (default 1280).
     * @return array{
     *     out: string, out_2x: string, out_poster: string,
     *     crop: array{x:int,y:int,w:int,h:int},
     *     masks: array<int,array{x:int,y:int,w:int,h:int}>,
     *     bytes: int, poster_bytes: int
     * }
     */
    public function process(
        string $sourcePath,
        string $outDir,
        ?array $cropOverride = null,
        array $maskRects = [],
        int $maxWidth = 2400,
        int $posterWidth = 1280
    ): array {
        if (!is_file($sourcePath)) {
            throw new \RuntimeException("Source not found: $sourcePath");
        }
        if (!is_dir($outDir) && !mkdir($outDir, 0755, true) && !is_dir($outDir)) {
            throw new \RuntimeException("Could not create out dir: $outDir");
        }

        $info = getimagesize($sourcePath) ?: throw new \RuntimeException("Not an image: $sourcePath");
        $img  = $this->load($sourcePath, $info[2]);

        // Apply masks to the SOURCE image (before crop) so coords stay simple.
        foreach ($maskRects as $rect) {
            $this->applyMask($img, $rect);
        }

        // Determine crop rect.
        if ($cropOverride !== null) {
            [$cx, $cy, $cw, $ch] = $cropOverride;
        } else {
            [$cx, $cy, $cw, $ch] = $this->autoCrop($img);
        }
        $cw = max(1, $cw);
        $ch = max(1, $ch);

        // Crop.
        $cropped = imagecreatetruecolor($cw, $ch);
        imagecopy($cropped, $img, 0, 0, $cx, $cy, $cw, $ch);
        imagedestroy($img);

        // Downscale main output (preserve aspect, max width = $maxWidth).
        $main = $this->resizeToWidth($cropped, $maxWidth);

        // Make a smaller poster too.
        $poster = $this->resizeToWidth($cropped, $posterWidth);
        imagedestroy($cropped);

        // Write both.
        $outMain   = rtrim($outDir, '/') . '/cockpit.webp';
        $outPoster = rtrim($outDir, '/') . '/cockpit-poster.webp';
        $out2x     = rtrim($outDir, '/') . '/cockpit-2x.webp'; // alias of main; same content
        imagewebp($main, $outMain, 88);
        imagewebp($main, $out2x,   88);
        imagewebp($poster, $outPoster, 78);
        imagedestroy($main);
        imagedestroy($poster);

        return [
            'out'         => $outMain,
            'out_2x'      => $out2x,
            'out_poster'  => $outPoster,
            'crop'        => ['x' => $cx, 'y' => $cy, 'w' => $cw, 'h' => $ch],
            'masks'       => array_map(static fn($r) => ['x' => $r[0], 'y' => $r[1], 'w' => $r[2], 'h' => $r[3]], $maskRects),
            'bytes'       => (int) filesize($outMain),
            'poster_bytes'=> (int) filesize($outPoster),
        ];
    }

    /** Detect whitespace borders by scanning rows/columns for luminance > threshold. */
    public function autoCrop(\GdImage $img): array
    {
        $w = imagesx($img);
        $h = imagesy($img);

        $isWhiteRow = function (int $y) use ($img, $w): bool {
            $whitePx = 0;
            // Sample every 4th pixel for speed
            $samples = 0;
            for ($x = 0; $x < $w; $x += 4) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8)  & 0xFF;
                $b = $rgb         & 0xFF;
                $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255.0;
                if ($lum > self::WHITE_THRESHOLD) $whitePx++;
                $samples++;
            }
            return $samples > 0 && ($whitePx / $samples) >= self::WHITE_RATIO;
        };
        $isWhiteCol = function (int $x) use ($img, $h): bool {
            $whitePx = 0;
            $samples = 0;
            for ($y = 0; $y < $h; $y += 4) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8)  & 0xFF;
                $b = $rgb         & 0xFF;
                $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255.0;
                if ($lum > self::WHITE_THRESHOLD) $whitePx++;
                $samples++;
            }
            return $samples > 0 && ($whitePx / $samples) >= self::WHITE_RATIO;
        };

        // Walk in from each edge.
        $top = 0;     while ($top < $h - 1 && $isWhiteRow($top))               { $top++; }
        $bottom = $h - 1; while ($bottom > $top && $isWhiteRow($bottom))      { $bottom--; }
        $left = 0;    while ($left < $w - 1 && $isWhiteCol($left))             { $left++; }
        $right = $w - 1; while ($right > $left && $isWhiteCol($right))         { $right--; }

        // Margin (don't exceed image bounds).
        $top    = max(0, $top - self::MARGIN_PX);
        $bottom = min($h - 1, $bottom + self::MARGIN_PX);
        $left   = max(0, $left - self::MARGIN_PX);
        $right  = min($w - 1, $right + self::MARGIN_PX);

        $cw = $right - $left + 1;
        $ch = $bottom - $top + 1;

        // Sanity: if the heuristic crashed (image is all white), don't crop at all.
        if ($cw < $w * 0.2 || $ch < $h * 0.2) {
            return [0, 0, $w, $h];
        }
        return [$left, $top, $cw, $ch];
    }

    /** Fill a [x,y,w,h] rect with a colour sampled from the immediate area outside the rect. */
    private function applyMask(\GdImage $img, array $rect): void
    {
        [$x, $y, $w, $h] = $rect;
        $imgW = imagesx($img);
        $imgH = imagesy($img);

        // Clamp.
        $x = max(0, min($imgW - 1, $x));
        $y = max(0, min($imgH - 1, $y));
        $w = max(1, min($imgW - $x, $w));
        $h = max(1, min($imgH - $y, $h));

        // Sample a 16x16 patch immediately to the right of the mask if possible,
        // otherwise above/below.
        $samples = [];
        $patchSize = 16;
        $candidates = [
            [min($x + $w + 4, $imgW - $patchSize - 1), $y],                // right of mask
            [$x, max($y - $patchSize - 4, 0)],                              // above mask
            [$x, min($y + $h + 4, $imgH - $patchSize - 1)],                 // below mask
        ];
        foreach ($candidates as [$sx, $sy]) {
            if ($sx < 0 || $sy < 0 || $sx + $patchSize > $imgW || $sy + $patchSize > $imgH) continue;
            for ($py = 0; $py < $patchSize; $py++) {
                for ($px = 0; $px < $patchSize; $px++) {
                    $rgb = imagecolorat($img, $sx + $px, $sy + $py);
                    $samples[] = $rgb;
                }
            }
            break;
        }

        if (empty($samples)) {
            // Fallback to pure white if nothing sampled (e.g. tiny image).
            $fill = imagecolorallocate($img, 250, 250, 250);
        } else {
            // Average the samples.
            $rs = $gs = $bs = 0;
            foreach ($samples as $rgb) {
                $rs += ($rgb >> 16) & 0xFF;
                $gs += ($rgb >> 8)  & 0xFF;
                $bs += $rgb         & 0xFF;
            }
            $n = count($samples);
            $fill = imagecolorallocate($img, intdiv($rs, $n), intdiv($gs, $n), intdiv($bs, $n));
        }
        imagefilledrectangle($img, $x, $y, $x + $w - 1, $y + $h - 1, $fill);
    }

    /** Load an image as GD resource. Supports PNG/JPG/WebP. */
    private function load(string $path, int $type): \GdImage
    {
        $img = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default        => throw new \RuntimeException("Unsupported image type: $type"),
        };
        if ($img === false) {
            throw new \RuntimeException("Failed to decode image: $path");
        }
        // Convert palette PNGs to truecolor so colour math + WebP encode work.
        if (!imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }
        return $img;
    }

    /** Resize preserving aspect ratio. */
    private function resizeToWidth(\GdImage $src, int $newW): \GdImage
    {
        $sw = imagesx($src);
        $sh = imagesy($src);
        if ($sw <= $newW) {
            // Just clone.
            $out = imagecreatetruecolor($sw, $sh);
            imagecopy($out, $src, 0, 0, 0, 0, $sw, $sh);
            return $out;
        }
        $newH = (int) round($sh * ($newW / $sw));
        $out = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($out, $src, 0, 0, 0, 0, $newW, $newH, $sw, $sh);
        return $out;
    }
}
