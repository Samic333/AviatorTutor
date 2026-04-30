<?php
/**
 * PDF Text Service — extract plain text from PDFs.
 *
 * Strategy (in priority order):
 *   1. Smalot/PdfParser (pure PHP, installed via Composer). Works on any
 *      shared host that has run `composer install`. This is the preferred
 *      path because it doesn't depend on system binaries.
 *   2. `pdftotext` shell command (poppler-utils). Used only when Smalot
 *      isn't available (e.g. composer hasn't been run yet).
 *
 * If neither is available the service surfaces a structured error so the
 * admin UI can fall back to the paste-text path.
 */

declare(strict_types=1);

namespace App\Services;

class PdfTextService
{
    /** @return bool whether Smalot/PdfParser is loadable */
    public static function smalotAvailable(): bool
    {
        return class_exists(\Smalot\PdfParser\Parser::class);
    }

    /**
     * Probe whether `pdftotext` is available and return the absolute path.
     * Returns null if not found.
     */
    public static function pdftotextPath(): ?string
    {
        $candidates = ['/usr/bin/pdftotext', '/usr/local/bin/pdftotext', '/bin/pdftotext'];
        foreach ($candidates as $p) {
            if (is_file($p) && is_executable($p)) {
                return $p;
            }
        }
        if (function_exists('shell_exec')) {
            $found = shell_exec('command -v pdftotext 2>/dev/null');
            if (is_string($found)) {
                $found = trim($found);
                if ($found !== '' && is_file($found)) {
                    return $found;
                }
            }
        }
        return null;
    }

    /**
     * Friendly summary of which extraction backends are available — used
     * by the admin UI to render configuration status.
     *
     * @return array{smalot:bool, pdftotext:?string, any:bool}
     */
    public static function backendStatus(): array
    {
        $bin = self::pdftotextPath();
        $smalot = self::smalotAvailable();
        return [
            'smalot'    => $smalot,
            'pdftotext' => $bin,
            'any'       => $smalot || $bin !== null,
        ];
    }

    /**
     * Extract plain text from a PDF on disk.
     *
     * @param string $pdfPath  Absolute path to a readable PDF file.
     * @param int    $maxBytes Truncate the extracted text to this many bytes.
     *                          0 = no limit.
     *
     * @return array{ok:bool, text?:string, bytes?:int, truncated?:bool, backend?:string, error?:string, error_detail?:string}
     */
    public static function extract(string $pdfPath, int $maxBytes = 0): array
    {
        if (!is_file($pdfPath) || !is_readable($pdfPath)) {
            return ['ok' => false, 'error' => 'file_not_readable', 'error_detail' => $pdfPath];
        }

        // 1. Try Smalot first (preferred, pure PHP).
        if (self::smalotAvailable()) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($pdfPath);
                $text   = (string) $pdf->getText();
                return self::wrap($text, $maxBytes, 'smalot');
            } catch (\Throwable $e) {
                // Fall through to pdftotext if Smalot can't parse this PDF
                // (encrypted, corrupted, exotic encoding, etc.)
                $smalotErr = $e->getMessage();
            }
        }

        // 2. Fall back to pdftotext if available.
        $bin = self::pdftotextPath();
        if ($bin !== null && function_exists('proc_open')) {
            $cmd = escapeshellarg($bin)
                 . ' -layout -enc UTF-8 '
                 . escapeshellarg($pdfPath)
                 . ' -';
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];
            $proc = proc_open($cmd, $descriptors, $pipes);
            if (is_resource($proc)) {
                fclose($pipes[0]);
                $stdout = (string) stream_get_contents($pipes[1]);
                $stderr = (string) stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                $exit = proc_close($proc);
                if ($exit === 0) {
                    return self::wrap($stdout, $maxBytes, 'pdftotext');
                }
                return [
                    'ok'           => false,
                    'error'        => 'pdftotext_failed',
                    'error_detail' => trim($stderr) !== '' ? $stderr : 'exit code ' . $exit,
                    'backend'      => 'pdftotext',
                ];
            }
        }

        return [
            'ok'           => false,
            'error'        => 'no_backend',
            'error_detail' => isset($smalotErr)
                ? 'Smalot failed (' . $smalotErr . ') and pdftotext is not installed.'
                : 'No PDF extraction backend available. Run `composer install` on the server, or install poppler-utils.',
        ];
    }

    /**
     * Common post-processing for both backends — UTF-8 sanitise, optional
     * truncation, byte counts.
     *
     * @return array{ok:bool, text:string, bytes:int, truncated:bool, backend:string}
     */
    private static function wrap(string $text, int $maxBytes, string $backend): array
    {
        $bytes = strlen($text);
        $truncated = false;
        if ($maxBytes > 0 && $bytes > $maxBytes) {
            $text = (string) substr($text, 0, $maxBytes);
            $lastSpace = strrpos($text, "\n");
            if ($lastSpace !== false) {
                $text = substr($text, 0, $lastSpace);
            }
            $truncated = true;
        }
        return [
            'ok'        => true,
            'text'      => $text,
            'bytes'     => $bytes,
            'truncated' => $truncated,
            'backend'   => $backend,
        ];
    }

    /**
     * Split a long text into roughly $maxChars chunks at paragraph
     * boundaries (so we don't slice mid-sentence). Used by the chunked
     * lesson-generation pass in Phase 3+.
     *
     * @return string[]
     */
    public static function chunk(string $text, int $maxChars = 80000): array
    {
        $text = trim($text);
        if ($text === '') return [];
        if (strlen($text) <= $maxChars) return [$text];

        $chunks = [];
        $remaining = $text;
        while (strlen($remaining) > $maxChars) {
            $slice = substr($remaining, 0, $maxChars);
            // Prefer a paragraph break, then a sentence end, then any newline.
            $cut = strrpos($slice, "\n\n");
            if ($cut === false || $cut < $maxChars * 0.5) {
                $cut = strrpos($slice, ". ");
                if ($cut !== false) $cut += 1; // keep the period
            }
            if ($cut === false || $cut < $maxChars * 0.4) {
                $cut = strrpos($slice, "\n");
            }
            if ($cut === false || $cut < $maxChars * 0.4) {
                $cut = $maxChars;
            }
            $chunks[]  = substr($remaining, 0, $cut);
            $remaining = ltrim((string) substr($remaining, $cut));
        }
        if ($remaining !== '') $chunks[] = $remaining;
        return $chunks;
    }
}
