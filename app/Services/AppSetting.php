<?php
/**
 * App Setting — runtime-editable config in the DB.
 *
 * Generic key/value store backed by the app_settings table. Used for
 * admin-editable config like AI provider API keys and default provider
 * choice, where editing config/app.local.php on the server would be
 * inconvenient.
 *
 * Lookup order in callers (e.g. AIContentService::config()):
 *   1. AppSetting::get($key) — DB value, if present
 *   2. require BASE_PATH/config/app.php — file value (which already
 *      merges config/app.local.php on top)
 *
 * is_secret flagged keys (API keys, etc.) are returned by getAll() with
 * a masked preview — the raw value is only emitted by get().
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class AppSetting
{
    /** Static cache so we don't hammer the DB inside one request. */
    private static ?array $cache = null;

    /**
     * Read one setting. Returns null if no row exists (so the caller can
     * fall back to a file-based default).
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::loadAll();
        if (!array_key_exists($key, $all)) return $default;
        $v = $all[$key]['value'];
        // Empty string in DB still counts as "set" — admin may have wanted
        // to explicitly blank a value. Return it; only return $default if
        // the row doesn't exist at all.
        return $v;
    }

    /**
     * Read all settings keyed by name. Each value is an array with
     * 'value' (string|null), 'is_secret' (bool), 'masked' (string), and
     * 'updated_at'. The 'masked' field is a UI-safe preview that only
     * reveals the last 4 chars of secret values.
     */
    public static function getAll(): array
    {
        $all = self::loadAll();
        $out = [];
        foreach ($all as $k => $row) {
            $out[$k] = [
                'value'      => $row['value'],
                'is_secret'  => $row['is_secret'],
                'masked'     => self::mask($row['value'], $row['is_secret']),
                'updated_at' => $row['updated_at'],
            ];
        }
        return $out;
    }

    /**
     * Set (insert or update) a setting. Pass an empty string to clear it
     * (or null to clear).
     *
     * @param string|null $value     The new value. Null/'' both clear the
     *                                row's value column but keep the row.
     * @param bool        $isSecret  Whether the key is a secret (gets
     *                                masked in UI listings).
     * @param int|null    $userId    Optional admin user id who made the change.
     */
    public static function set(string $key, ?string $value, bool $isSecret = false, ?int $userId = null): void
    {
        $db = DB::instance();
        $db->execute(
            'INSERT INTO app_settings (setting_key, setting_value, is_secret, updated_by, updated_at)
             VALUES (?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                 setting_value = VALUES(setting_value),
                 is_secret     = VALUES(is_secret),
                 updated_by    = VALUES(updated_by),
                 updated_at    = NOW()',
            [$key, $value, $isSecret ? 1 : 0, $userId]
        );
        self::$cache = null; // invalidate
    }

    /**
     * Convenience: only set if the new value is non-empty AND different
     * from the current value. Lets the admin form ignore "leave blank to
     * keep existing" without overwriting a real key with an empty string.
     */
    public static function setIfChanged(string $key, ?string $newValue, bool $isSecret = false, ?int $userId = null): bool
    {
        if ($newValue === null || $newValue === '') return false;
        $current = self::get($key);
        if ($current === $newValue) return false;
        self::set($key, $newValue, $isSecret, $userId);
        return true;
    }

    /** Clear a setting entirely (removes the row). */
    public static function delete(string $key): void
    {
        DB::instance()->execute('DELETE FROM app_settings WHERE setting_key = ?', [$key]);
        self::$cache = null;
    }

    /**
     * Render a UI-safe masked preview. For secrets shows '••••XXXX' where
     * XXXX is the last 4 chars. For non-secrets returns the raw value.
     * Returns empty string when the value is empty.
     */
    public static function mask(?string $value, bool $isSecret): string
    {
        if ($value === null || $value === '') return '';
        if (!$isSecret) return $value;
        $len = strlen($value);
        $tail = $len >= 4 ? substr($value, -4) : str_repeat('•', max(1, $len));
        return '••••' . $tail;
    }

    /**
     * Lower-level loader. Tolerates a missing table so the app still
     * boots on environments that haven't run the migration yet.
     *
     * @return array<string,array{value:?string,is_secret:bool,updated_at:?string}>
     */
    private static function loadAll(): array
    {
        if (self::$cache !== null) return self::$cache;
        try {
            $rows = DB::instance()->query(
                'SELECT setting_key, setting_value, is_secret, updated_at FROM app_settings'
            );
        } catch (\Throwable $e) {
            // Table missing / DB unreachable — return empty so callers
            // fall back to file-based config without crashing.
            self::$cache = [];
            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $out[(string) $r['setting_key']] = [
                'value'      => $r['setting_value'],
                'is_secret'  => (bool) ($r['is_secret'] ?? 0),
                'updated_at' => $r['updated_at'] ?? null,
            ];
        }
        self::$cache = $out;
        return $out;
    }
}
