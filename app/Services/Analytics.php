<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

/**
 * Lightweight, fire-and-forget event tracker. Inserts one row per call;
 * never throws (so a hot path doesn't blow up if the table is missing).
 *
 * Usage:
 *   Analytics::track('mode_open', ['mode' => 'mind_map', 'system_id' => 7]);
 *
 * The Phase-5 admin dashboard at /admin/analytics aggregates these rows.
 */
class Analytics
{
    public static function track(string $event, array $props = [], ?int $userId = null): void
    {
        if ($event === '') return;
        try {
            $uid = $userId !== null ? $userId : (int) (\App\Core\Auth::user()['id'] ?? 0);
            $sid = (string) ($_SESSION['analytics_session_id'] ?? '');
            if ($sid === '') {
                $sid = bin2hex(random_bytes(8));
                $_SESSION['analytics_session_id'] = $sid;
            }
            DB::instance()->execute(
                'INSERT INTO analytics_events (user_id, event, props_json, session_id)
                 VALUES (?, ?, ?, ?)',
                [
                    $uid > 0 ? $uid : null,
                    $event,
                    !empty($props) ? json_encode($props, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                    $sid,
                ]
            );
        } catch (\Throwable $e) {
            // Tracking must never affect the user — silently swallow.
        }
    }
}
