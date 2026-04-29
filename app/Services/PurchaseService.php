<?php
/**
 * Purchase Service
 *
 * Per-subject access model. Replaces the flat-rate SubscriptionService for new
 * content while staying backward-compatible: a user with a legacy active
 * subscription still gets access to the q400-pack subject.
 *
 * Access check order in hasAccess():
 *   1. Admin → always granted
 *   2. purchases row with status='active' and (expires_at NULL or > NOW()) → granted
 *   3. Legacy fallback: if subject is q400-pack and SubscriptionService::hasActive() → granted
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class PurchaseService
{
    /**
     * Does this user have access to the given subject?
     */
    public static function hasAccess(int $userId, string $subjectSlug): bool
    {
        if ($userId <= 0) return false;

        $row = DB::instance()->queryOne(
            'SELECT p.id
               FROM purchases p
               JOIN subjects s ON s.id = p.subject_id
              WHERE p.user_id = ?
                AND s.slug = ?
                AND p.status = "active"
                AND (p.expires_at IS NULL OR p.expires_at > NOW())
              LIMIT 1',
            [$userId, $subjectSlug]
        );
        if ($row) return true;

        // Legacy fallback — flat-rate subscribers retain Q400 access.
        if ($subjectSlug === 'q400-pack' && SubscriptionService::hasActive($userId)) {
            return true;
        }

        return false;
    }

    /**
     * Grant a subject to a user. Returns the new purchase id, or 0 on failure
     * (e.g. duplicate user/subject).
     *
     * @param array<string,mixed> $meta
     */
    public static function grant(int $userId, int $subjectId, string $provider, array $meta = []): int
    {
        $db = DB::instance();

        // Idempotent: if a row exists, return it.
        $existing = $db->queryOne(
            'SELECT id, status FROM purchases WHERE user_id = ? AND subject_id = ? LIMIT 1',
            [$userId, $subjectId]
        );

        if ($existing) {
            // Reactivate refunded/disputed if explicitly granting again.
            if ($existing['status'] !== 'active') {
                $db->execute(
                    'UPDATE purchases SET status = "active", updated_at = NOW() WHERE id = ?',
                    [$existing['id']]
                );
            }
            return (int)$existing['id'];
        }

        $id = $db->insert(
            'INSERT INTO purchases
               (user_id, subject_id, payment_provider, stripe_payment_intent_id,
                activation_code_id, amount_paid_usd, status, granted_at, expires_at, notes)
             VALUES (?, ?, ?, ?, ?, ?, "active", NOW(), ?, ?)',
            [
                $userId,
                $subjectId,
                $provider,
                $meta['stripe_payment_intent_id'] ?? null,
                $meta['activation_code_id']       ?? null,
                $meta['amount_paid_usd']          ?? 0.00,
                $meta['expires_at']               ?? null,
                $meta['notes']                    ?? null,
            ]
        );

        // Audit trail.
        $db->insert(
            'INSERT INTO subscription_events (user_id, subscription_id, event_type, payload_json)
             VALUES (?, NULL, ?, ?)',
            [
                $userId,
                'subject_granted',
                json_encode([
                    'subject_id' => $subjectId,
                    'provider'   => $provider,
                    'meta'       => $meta,
                ]),
            ]
        );

        return (int)$id;
    }

    /**
     * Admin-initiated grant. Wraps grant() with provider='admin_grant'.
     */
    public static function adminGrant(int $userId, int $subjectId, ?string $note = null): int
    {
        return self::grant($userId, $subjectId, 'admin_grant', [
            'notes'           => $note,
            'amount_paid_usd' => 0.00,
        ]);
    }

    /**
     * Refund / revoke access without deleting the row.
     */
    public static function refund(int $purchaseId): bool
    {
        return DB::instance()->execute(
            'UPDATE purchases SET status = "refunded", updated_at = NOW() WHERE id = ?',
            [$purchaseId]
        ) > 0;
    }

    /**
     * All active purchases for a user, joined with subject info.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function userPurchases(int $userId): array
    {
        return DB::instance()->query(
            'SELECT p.id, p.status, p.granted_at, p.expires_at, p.amount_paid_usd,
                    p.payment_provider,
                    s.id AS subject_id, s.slug, s.name, s.category, s.short_blurb,
                    s.color_hex, s.icon_slug, s.is_coming_soon
               FROM purchases p
               JOIN subjects s ON s.id = p.subject_id
              WHERE p.user_id = ?
                AND p.status = "active"
                AND (p.expires_at IS NULL OR p.expires_at > NOW())
              ORDER BY p.granted_at DESC',
            [$userId]
        );
    }

    /**
     * Catalog of all published subjects, optionally filtered by category.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function catalog(?string $category = null): array
    {
        if ($category !== null) {
            return DB::instance()->query(
                'SELECT id, slug, name, category, short_blurb, price_usd,
                        is_coming_soon, color_hex, icon_slug, sort_order
                   FROM subjects
                  WHERE is_published = 1 AND category = ?
                  ORDER BY sort_order ASC',
                [$category]
            );
        }

        return DB::instance()->query(
            'SELECT id, slug, name, category, short_blurb, price_usd,
                    is_coming_soon, color_hex, icon_slug, sort_order
               FROM subjects
              WHERE is_published = 1
              ORDER BY sort_order ASC'
        );
    }

    /**
     * Find a subject row by slug.
     *
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        $row = DB::instance()->queryOne(
            'SELECT * FROM subjects WHERE slug = ? LIMIT 1',
            [$slug]
        );
        return $row ?: null;
    }

    /**
     * Stats for the admin dashboard.
     */
    public static function summary(): array
    {
        $row = DB::instance()->queryOne(
            'SELECT
                COUNT(*)                                                 AS total_purchases,
                SUM(status = "active")                                   AS active,
                SUM(status = "refunded")                                  AS refunded,
                SUM(status = "disputed")                                  AS disputed,
                COALESCE(SUM(CASE WHEN status = "active" THEN amount_paid_usd END), 0) AS total_revenue
             FROM purchases'
        );
        return [
            'total_purchases' => (int)   ($row['total_purchases'] ?? 0),
            'active'          => (int)   ($row['active']          ?? 0),
            'refunded'        => (int)   ($row['refunded']        ?? 0),
            'disputed'        => (int)   ($row['disputed']        ?? 0),
            'total_revenue'   => (float) ($row['total_revenue']   ?? 0),
        ];
    }
}
