<?php
/**
 * Activation Code Service
 *
 * Generate, list, redeem, and revoke single-use activation codes that
 * unlock a paid subscription window for the redeemer.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class ActivationCodeService
{
    /** Characters used in generated codes — A–Z 2–9 minus confusables (I, O, 0, 1). */
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /** Default code length (without hyphens). */
    private const CODE_LENGTH = 16;

    /**
     * Generate $count single-use codes.
     *
     * @return array<int, array{id:int, code:string}>
     */
    public static function generate(int $count, int $days = 30, string $plan = 'monthly', ?int $createdByAdminId = null, ?string $notes = null): array
    {
        $db = DB::instance();
        $created = [];
        for ($i = 0; $i < $count; $i++) {
            // Retry on the rare collision
            for ($try = 0; $try < 5; $try++) {
                $code = self::randomCode();
                try {
                    $id = $db->insert(
                        'INSERT INTO activation_codes (code, plan, days, status, created_by_admin_id, notes)
                         VALUES (?, ?, ?, "unused", ?, ?)',
                        [$code, $plan, $days, $createdByAdminId, $notes]
                    );
                    if ($id) {
                        $created[] = ['id' => (int) $id, 'code' => self::format($code)];
                        break;
                    }
                } catch (\PDOException $e) {
                    // Unique-key collision — try a fresh code
                    if ((int) ($e->errorInfo[1] ?? 0) !== 1062) {
                        throw $e;
                    }
                }
            }
        }
        return $created;
    }

    /**
     * Redeem a code for the given user. Returns the new subscription row id, or null on failure.
     * Atomic: the activation_codes row is locked + updated and a subscription is inserted in one tx.
     *
     * @return array{ok:bool, error?:string, subscription_id?:int, expires_at?:string}
     */
    public static function redeem(string $rawCode, int $userId, ?string $ip = null): array
    {
        $code = self::normalize($rawCode);
        if ($code === '') {
            return ['ok' => false, 'error' => 'Please enter your code.'];
        }

        $db = DB::instance();
        $db->beginTransaction();
        try {
            $row = $db->queryOne(
                'SELECT id, status, plan, days, expires_at FROM activation_codes
                 WHERE code = ? FOR UPDATE',
                [$code]
            );

            if (!$row) {
                $db->rollback();
                return ['ok' => false, 'error' => 'Invalid code. Check the spelling and try again.'];
            }
            if ($row['status'] === 'redeemed') {
                $db->rollback();
                return ['ok' => false, 'error' => 'This code has already been redeemed.'];
            }
            if ($row['status'] === 'revoked') {
                $db->rollback();
                return ['ok' => false, 'error' => 'This code has been revoked.'];
            }
            if (!empty($row['expires_at']) && strtotime($row['expires_at']) < time()) {
                $db->rollback();
                return ['ok' => false, 'error' => 'This code has expired.'];
            }

            $now = date('Y-m-d H:i:s');
            $expires = date('Y-m-d H:i:s', strtotime("+{$row['days']} days"));

            // Mark code redeemed
            $db->execute(
                'UPDATE activation_codes
                    SET status = "redeemed", redeemed_by_user_id = ?, redeemed_at = ?
                  WHERE id = ?',
                [$userId, $now, $row['id']]
            );

            // Insert new subscription
            $subId = (int) $db->insert(
                'INSERT INTO subscriptions
                    (user_id, plan, status, payment_provider, starts_at, expires_at, activation_code_id)
                 VALUES (?, ?, "active", "activation_code", ?, ?, ?)',
                [$userId, $row['plan'], $now, $expires, $row['id']]
            );

            // Audit
            $db->insert(
                'INSERT INTO subscription_events (user_id, subscription_id, event_type, payload_json, ip)
                 VALUES (?, ?, "activation_code_redeemed", ?, ?)',
                [
                    $userId,
                    $subId,
                    json_encode(['code_id' => (int) $row['id'], 'days' => (int) $row['days']]),
                    $ip,
                ]
            );

            $db->commit();
            return ['ok' => true, 'subscription_id' => $subId, 'expires_at' => $expires];
        } catch (\Throwable $e) {
            $db->rollback();
            return ['ok' => false, 'error' => 'Something went wrong. Please try again.'];
        }
    }

    /**
     * Revoke a code so it cannot be redeemed.
     */
    public static function revoke(int $codeId, ?int $byAdminId = null): bool
    {
        $db = DB::instance();
        $rows = $db->execute(
            'UPDATE activation_codes SET status = "revoked"
             WHERE id = ? AND status = "unused"',
            [$codeId]
        );
        if ($rows > 0) {
            $db->insert(
                'INSERT INTO audit_log (user_id, action, target_type, target_id)
                 VALUES (?, "code.revoke", "activation_code", ?)',
                [$byAdminId, $codeId]
            );
        }
        return $rows > 0;
    }

    /**
     * Recent codes for the admin listing (newest first).
     *
     * @return list<array<string,mixed>>
     */
    public static function recent(int $limit = 50): array
    {
        return DB::instance()->query(
            'SELECT ac.id, ac.code, ac.plan, ac.days, ac.status, ac.expires_at,
                    ac.redeemed_at, ac.created_at,
                    a.name AS admin_name,
                    u.name AS redeemed_by_name, u.email AS redeemed_by_email
               FROM activation_codes ac
               LEFT JOIN users a ON a.id = ac.created_by_admin_id
               LEFT JOIN users u ON u.id = ac.redeemed_by_user_id
              ORDER BY ac.created_at DESC
              LIMIT ?',
            [$limit]
        );
    }

    public static function summary(): array
    {
        $row = DB::instance()->queryOne(
            'SELECT
                SUM(status="unused")  AS unused,
                SUM(status="redeemed") AS redeemed,
                SUM(status="revoked")  AS revoked,
                COUNT(*)               AS total
             FROM activation_codes'
        );
        return [
            'unused'   => (int) ($row['unused']   ?? 0),
            'redeemed' => (int) ($row['redeemed'] ?? 0),
            'revoked'  => (int) ($row['revoked']  ?? 0),
            'total'    => (int) ($row['total']    ?? 0),
        ];
    }

    public static function normalize(string $code): string
    {
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $code) ?? '');
        return $cleaned;
    }

    /** Format storage-form (no hyphens) into XXXX-XXXX-XXXX-XXXX for display. */
    public static function format(string $code): string
    {
        $code = self::normalize($code);
        return implode('-', str_split($code, 4));
    }

    private static function randomCode(): string
    {
        $alphabetLen = strlen(self::ALPHABET);
        $code = '';
        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $alphabetLen - 1)];
        }
        return $code;
    }
}
