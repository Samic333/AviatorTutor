<?php
/**
 * PDF Text Service — extract plain text from PDFs.
 *
 * Phase 2 strategy: shell out to `pdftotext` (poppler-utils) when available
 * on the host. Most cPanel/Namecheap shared servers have it preinstalled.
 * If it's missing, we surface a clear error message and the admin can
 * paste text directly in the smoke-test page instead.
 *
 * Phase 3 will add a pure-PHP fallback (Smalot/PdfParser via Composer) so
 * extraction never depends on a system binary.
 */

declare(strict_types=1);

namespace App\Services;

class PdfTextService
{
    /**
     * Probe whether `pdftotext` is available and return the absolute path.
     * Returns null if not found.
     */
    public static function pdftotextPath(): ?string
    {
        // Common paths on cPanel/Linux hosts; check $PATH last.
        $candidates = ['/usr/bin/pdftotext', '/usr/local/bin/pdftotext', '/bin/pdftotext'];
        foreach ($candidates as $p) {
            if (is_file($p) && is_executable($p)) {
                return $p;
            }
        }
        // Fall back to `which` if shell exec is allowed
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
     * Extract plain text from a PDF on disk.
     *
     * @param string $pdfPath Absolute path to a readable PDF file.
     * @param int    $maxBytes Truncate the extracted text to this many bytes
     *                          (utf-8 safe-ish; rounded down to a char boundary).
     *                          0 = no limit.
     *
     * @return array{ok:bool, text?:string, bytes?:int, truncated?:bool, error?:string, error_detail?:string}
     */
    public static function extract(string $pdfPath, int $maxBytes = 0): array
    {
        if (!is_file($pdfPath) || !is_readable($pdfPath)) {
            return ['ok' => false, 'error' => 'file_not_readable', 'error_detail' => $pdfPath];
        }

        $bin = self::pdftotextPath();
        if ($bin === null) {
            return [
                'ok'           => false,
                'error'        => 'pdftotext_missing',
                'error_detail' => 'pdftotext (poppler-utils) is not available on this host. Paste the text manually for now, or install poppler-utils.',
            ];
        }

        if (!function_exists('proc_open')) {
            return ['ok' => false, 'error' => 'proc_open_disabled'];
        }

        // -layout preserves the column structure better than the default
        // flow mode; -enc UTF-8 forces utf-8 output; "-" writes to stdout.
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
        if (!is_resource($proc)) {
            return ['ok' => false, 'error' => 'proc_open_failed'];
        }
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($proc);

        if ($exit !== 0) {
            return [
                'ok'           => false,
                'error'        => 'pdftotext_failed',
                'error_detail' => trim($stderr) !== '' ? $stderr : 'exit code ' . $exit,
            ];
        }

        $bytes = strlen($stdout);
        $truncated = false;
        if ($maxBytes > 0 && $bytes > $maxBytes) {
            $stdout = (string) substr($stdout, 0, $maxBytes);
            // Round down to last whitespace so we don't split a word
            $lastSpace = strrpos($stdout, "\n");
            if ($lastSpace !== false) {
                $stdout = substr($stdout, 0, $lastSpace);
            }
            $truncated = true;
        }

        return [
            'ok'        => true,
            'text'      => $stdout,
            'bytes'     => $bytes,
            'truncated' => $truncated,
        ];
    }
}
