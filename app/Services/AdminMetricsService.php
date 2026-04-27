<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

/**
 * Phase 10c — single-shot live metrics for /admin dashboard.
 *
 * Each method runs at most one query. The dashboard view calls dashboard()
 * which folds them all into one return.
 */
final class AdminMetricsService
{
    public static function dashboard(): array
    {
        $db = DB::instance();

        $userCount = (int) ($db->queryOne('SELECT COUNT(*) c FROM users')['c'] ?? 0);
        $admins = (int) ($db->queryOne("SELECT COUNT(*) c FROM users WHERE role='admin'")['c'] ?? 0);

        $subsActive  = (int) ($db->queryOne(
            "SELECT COUNT(*) c FROM subscriptions WHERE status='active' AND expires_at > NOW()"
        )['c'] ?? 0);
        $subsExpired = (int) ($db->queryOne(
            "SELECT COUNT(*) c FROM subscriptions WHERE status IN ('expired','active') AND expires_at <= NOW()"
        )['c'] ?? 0);

        $codesUnused = (int) ($db->queryOne("SELECT COUNT(*) c FROM activation_codes WHERE status='unused'")['c'] ?? 0);
        $codesRedeemed24h = (int) ($db->queryOne(
            "SELECT COUNT(*) c FROM activation_codes
             WHERE status='redeemed' AND redeemed_at IS NOT NULL AND redeemed_at >= NOW() - INTERVAL 24 HOUR"
        )['c'] ?? 0);

        $leadsTotal = (int) ($db->queryOne('SELECT COUNT(*) c FROM lead_signups')['c'] ?? 0);
        $leadsByModule = $db->query(
            'SELECT requested_module_slug AS slug, COUNT(*) AS n
             FROM lead_signups
             GROUP BY requested_module_slug
             ORDER BY n DESC LIMIT 10'
        );

        $sysCount   = (int) ($db->queryOne('SELECT COUNT(*) c FROM systems')['c'] ?? 0);
        $lessonCount= (int) ($db->queryOne('SELECT COUNT(*) c FROM lessons')['c'] ?? 0);
        $cardCount  = (int) ($db->queryOne('SELECT COUNT(*) c FROM flashcards')['c'] ?? 0);
        $quizCount  = (int) ($db->queryOne('SELECT COUNT(*) c FROM quizzes')['c'] ?? 0);

        $sessionsToday = (int) ($db->queryOne(
            'SELECT COUNT(*) c FROM study_sessions WHERE DATE(created_at) = CURDATE()'
        )['c'] ?? 0);

        // Last 14 days activity sparkline.
        $sparkline = $db->query(
            "SELECT DATE(created_at) d, COUNT(*) c
             FROM study_sessions
             WHERE created_at >= NOW() - INTERVAL 14 DAY
             GROUP BY DATE(created_at)
             ORDER BY d"
        );

        // Recent users (newest 10).
        $recentUsers = $db->query(
            "SELECT id, name, email, role, created_at
             FROM users
             ORDER BY id DESC
             LIMIT 10"
        );

        // Recent code redeems (newest 5).
        $recentRedeems = $db->query(
            "SELECT a.code, a.redeemed_at, u.name, u.email
             FROM activation_codes a
             LEFT JOIN users u ON u.id = a.redeemed_by_user_id
             WHERE a.status = 'redeemed'
             ORDER BY a.redeemed_at DESC
             LIMIT 5"
        );

        // Recent leads (newest 5).
        $recentLeads = $db->query(
            "SELECT email, requested_module_slug AS slug, created_at
             FROM lead_signups
             ORDER BY id DESC
             LIMIT 5"
        );

        // Aircraft catalog summary
        $aircraftStats = $db->query(
            "SELECT slug, short_name, status, waitlist_count
             FROM aircrafts ORDER BY sort_order"
        );

        return [
            'user_count'         => $userCount,
            'admin_count'        => $admins,
            'subs_active'        => $subsActive,
            'subs_expired'       => $subsExpired,
            'codes_unused'       => $codesUnused,
            'codes_redeemed_24h' => $codesRedeemed24h,
            'leads_total'        => $leadsTotal,
            'leads_by_module'    => $leadsByModule,
            'system_count'       => $sysCount,
            'lesson_count'       => $lessonCount,
            'flashcard_count'    => $cardCount,
            'quiz_count'         => $quizCount,
            'sessions_today'     => $sessionsToday,
            'sparkline'          => $sparkline,
            'recent_users'       => $recentUsers,
            'recent_redeems'     => $recentRedeems,
            'recent_leads'       => $recentLeads,
            'aircraft_stats'     => $aircraftStats,
        ];
    }
}
