<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

/**
 * Aircraft catalog + per-user aircraft scope.
 *
 * The catalog drives:
 *   - The 12 homepage tiles
 *   - /aircraft (catalog) and /aircraft/{slug} (detail)
 *   - The "Studying: {short_name} ▾" scope chip on the dashboard
 *   - The systems / flashcards / quizzes / progress route filters
 */
final class AircraftService
{
    /** All catalog rows, sorted by status (live first) then sort_order. */
    public static function all(): array
    {
        return DB::instance()->query(
            "SELECT * FROM aircrafts
             ORDER BY FIELD(status,'live','beta','coming_soon','archived'), sort_order ASC, short_name ASC"
        );
    }

    /** Only the rows a logged-in student can choose to study right now. */
    public static function studyable(): array
    {
        return DB::instance()->query(
            "SELECT * FROM aircrafts
             WHERE status IN ('live','beta')
             ORDER BY FIELD(status,'live','beta'), sort_order ASC"
        );
    }

    public static function byId(int $id): ?array
    {
        $row = DB::instance()->queryOne('SELECT * FROM aircrafts WHERE id = ?', [$id]);
        return is_array($row) ? $row : null;
    }

    public static function bySlug(string $slug): ?array
    {
        $row = DB::instance()->queryOne('SELECT * FROM aircrafts WHERE slug = ?', [$slug]);
        return is_array($row) ? $row : null;
    }

    /**
     * Return the user's currently-active aircraft (the one they last picked
     * via "Start studying" or the scope-chip dropdown). Falls back to Q400.
     */
    public static function currentForUser(?int $userId): ?array
    {
        $db = DB::instance();
        if ($userId !== null) {
            $row = $db->queryOne(
                'SELECT a.* FROM aircrafts a
                 JOIN users u ON u.preferred_aircraft_id = a.id
                 WHERE u.id = ?',
                [$userId]
            );
            if (is_array($row)) {
                return $row;
            }
        }
        return self::bySlug('q400');
    }

    /** Set a user's preferred aircraft. Returns true if updated. */
    public static function setPreferredForUser(int $userId, int $aircraftId): bool
    {
        $db = DB::instance();
        // Validate the aircraft exists and is studyable.
        $a = self::byId($aircraftId);
        if (!$a || !in_array($a['status'], ['live', 'beta'], true)) {
            return false;
        }
        $db->execute(
            'UPDATE users SET preferred_aircraft_id = ? WHERE id = ?',
            [$aircraftId, $userId]
        );
        return true;
    }

    /** Group catalog rows by category for the catalog page filter. */
    public static function groupedByCategory(): array
    {
        $rows = self::all();
        $groups = [];
        foreach ($rows as $r) {
            $groups[$r['category']][] = $r;
        }
        return $groups;
    }

    /** Recompute waitlist_count from lead_signups (used by /admin/aircrafts). */
    public static function refreshWaitlistCounts(): void
    {
        $db = DB::instance();
        $db->execute(
            "UPDATE aircrafts a
             SET waitlist_count = (
                SELECT COUNT(*) FROM lead_signups
                WHERE requested_module_slug = a.slug
             )"
        );
    }

    /** Friendly status label for badges. */
    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'live'        => 'LIVE',
            'beta'        => 'BETA',
            'coming_soon' => 'COMING SOON',
            'archived'    => 'ARCHIVED',
            default       => strtoupper($status),
        };
    }

    /** Tailwind-ish color hint for status badges (uses marketing.css tokens). */
    public static function statusToken(string $status): string
    {
        return match ($status) {
            'live'        => 'success',
            'beta'        => 'accent',
            'coming_soon' => 'warn',
            default       => 'soft',
        };
    }
}
