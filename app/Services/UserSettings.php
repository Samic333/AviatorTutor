<?php
/**
 * Per-user reading preferences (theme, font size/family, line spacing, etc.)
 *
 * Reads + writes are defensive: every method falls back to the default
 * profile if the user_settings table hasn't been migrated yet, so the
 * site doesn't 500 on a partially-deployed environment.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class UserSettings
{
    /** Default profile applied when a user has no row yet, or the table is missing. */
    public const DEFAULTS = [
        'theme'          => 'dark',
        'font_size'      => 'm',     // xs|s|m|l|xl
        'font_family'    => 'system',
        'line_spacing'   => 'normal',
        'reading_width'  => 'medium',
        'reduced_motion' => 0,
        'audio_accent'   => 'us',
    ];

    public const ALLOWED = [
        'theme'          => ['light','dark','sepia','high-contrast','blue-light','solarized'],
        'font_size'      => ['xs','s','m','l','xl'],
        'font_family'    => ['system','serif','dyslexic'],
        'line_spacing'   => ['tight','normal','loose'],
        'reading_width'  => ['narrow','medium','wide'],
        'audio_accent'   => ['us','uk'],
    ];

    /**
     * Read a user's settings. Returns DEFAULTS if no row / table missing.
     * @return array<string,mixed>
     */
    public static function get(int $userId): array
    {
        if ($userId <= 0) return self::DEFAULTS;
        try {
            $row = DB::instance()->queryOne(
                'SELECT theme, font_size, font_family, line_spacing,
                        reading_width, reduced_motion, audio_accent
                   FROM user_settings WHERE user_id = ?',
                [$userId]
            );
        } catch (\Throwable $e) {
            return self::DEFAULTS;
        }
        if (!$row) return self::DEFAULTS;
        $row['reduced_motion'] = (int) $row['reduced_motion'];
        return array_merge(self::DEFAULTS, $row);
    }

    /**
     * Write a partial update. Unknown keys and out-of-range values are dropped.
     * Idempotent — performs an UPSERT. Silently no-ops if the table is missing.
     */
    public static function set(int $userId, array $partial): bool
    {
        if ($userId <= 0) return false;
        $clean = self::sanitize($partial);
        if (empty($clean)) return true; // nothing to do

        try {
            $existing = DB::instance()->queryOne(
                'SELECT user_id FROM user_settings WHERE user_id = ?',
                [$userId]
            );
        } catch (\Throwable $e) {
            return false; // table missing — caller's localStorage mirror still wins
        }

        $merged = array_merge(self::DEFAULTS, self::get($userId), $clean);

        try {
            if ($existing) {
                $sets   = [];
                $params = [];
                foreach ($clean as $k => $v) {
                    $sets[]   = "$k = ?";
                    $params[] = $v;
                }
                $params[] = $userId;
                DB::instance()->execute(
                    'UPDATE user_settings SET ' . implode(', ', $sets) . ' WHERE user_id = ?',
                    $params
                );
            } else {
                DB::instance()->execute(
                    'INSERT INTO user_settings
                        (user_id, theme, font_size, font_family, line_spacing,
                         reading_width, reduced_motion, audio_accent)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $userId,
                        $merged['theme'],
                        $merged['font_size'],
                        $merged['font_family'],
                        $merged['line_spacing'],
                        $merged['reading_width'],
                        (int) $merged['reduced_motion'],
                        $merged['audio_accent'],
                    ]
                );
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Filter the input array down to known-good keys + values. */
    private static function sanitize(array $partial): array
    {
        $clean = [];
        foreach ($partial as $k => $v) {
            if ($k === 'reduced_motion') {
                $clean[$k] = !empty($v) ? 1 : 0;
                continue;
            }
            if (!array_key_exists($k, self::ALLOWED)) continue;
            $sv = (string) $v;
            if (in_array($sv, self::ALLOWED[$k], true)) {
                $clean[$k] = $sv;
            }
        }
        return $clean;
    }
}
