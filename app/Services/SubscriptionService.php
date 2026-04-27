<?php
/**
 * Subscription Service
 *
 * Read the active subscription state for a user. The application does not
 * run a cron — every request that needs gating calls hasActive() which
 * verifies status='active' AND expires_at > NOW(). Expired subs are
 * implicitly inactive; status is flipped to 'expired' lazily by lazyExpire().
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class SubscriptionService
{
    /**
     * Is this user currently allowed to study (active sub)?
     */
    public static function hasActive(int $userId): bool
    {
        return self::current($userId) !== null;
    }

    /**
     * Returns the user's active subscription row, or null.
     *
     * @return array<string,mixed>|null
     */
    public static function current(int $userId): ?array
    {
        $row = DB::instance()->queryOne(
            'SELECT id, plan, status, payment_provider, starts_at, expires_at,
                    activation_code_id, stripe_customer_id, stripe_subscription_id,
                    created_at, updated_at
               FROM subscriptions
              WHERE user_id = ?
                AND status = "active"
                AND expires_at > NOW()
              ORDER BY expires_at DESC
              LIMIT 1',
            [$userId]
        );
        return $row ?: null;
    }

    /**
     * The latest subscription row regardless of status — used for /account.
     */
    public static function latest(int $userId): ?array
    {
        $row = DB::instance()->queryOne(
            'SELECT id, plan, status, payment_provider, starts_at, expires_at,
                    activation_code_id, stripe_customer_id, stripe_subscription_id,
                    created_at, updated_at
               FROM subscriptions
              WHERE user_id = ?
              ORDER BY id DESC
              LIMIT 1',
            [$userId]
        );
        return $row ?: null;
    }

    /**
     * Flip rows whose expires_at has passed from active → expired.
     * Called from CLI or admin tools; not on every request (expiry is checked
     * implicitly via the WHERE clause in current()).
     */
    public static function lazyExpire(): int
    {
        return DB::instance()->execute(
            'UPDATE subscriptions
                SET status = "expired"
              WHERE status = "active"
                AND expires_at <= NOW()'
        );
    }

    /**
     * Cancel an active subscription (admin or user-initiated). Does NOT refund or
     * change expires_at — the user retains access until expires_at unless
     * end_immediately is true.
     */
    public static function cancel(int $subscriptionId, bool $endImmediately = false): bool
    {
        $db = DB::instance();
        if ($endImmediately) {
            $rows = $db->execute(
                'UPDATE subscriptions
                    SET status = "cancelled", expires_at = NOW()
                  WHERE id = ?',
                [$subscriptionId]
            );
        } else {
            $rows = $db->execute(
                'UPDATE subscriptions
                    SET status = "cancelled"
                  WHERE id = ?',
                [$subscriptionId]
            );
        }
        if ($rows > 0) {
            $sub = $db->queryOne('SELECT user_id FROM subscriptions WHERE id = ?', [$subscriptionId]);
            $db->insert(
                'INSERT INTO subscription_events (user_id, subscription_id, event_type, payload_json)
                 VALUES (?, ?, "cancelled", ?)',
                [$sub['user_id'] ?? null, $subscriptionId, json_encode(['end_immediately' => $endImmediately])]
            );
        }
        return $rows > 0;
    }

    /**
     * Stats for the admin dashboard.
     */
    public static function summary(): array
    {
        $row = DB::instance()->queryOne(
            'SELECT
                SUM(status = "active"    AND expires_at > NOW()) AS active,
                SUM(status = "active"    AND expires_at <= NOW()) AS active_expired,
                SUM(status = "expired")                          AS expired,
                SUM(status = "cancelled")                         AS cancelled,
                COUNT(*)                                          AS total
             FROM subscriptions'
        );
        return [
            'active'    => (int) ($row['active']         ?? 0),
            'expired'   => (int) ($row['expired']        ?? 0)
                          + (int) ($row['active_expired'] ?? 0),
            'cancelled' => (int) ($row['cancelled']      ?? 0),
            'total'     => (int) ($row['total']          ?? 0),
        ];
    }
}
