<?php
/**
 * Progress Controller
 *
 * Displays user learning progress and statistics
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\DB;

class ProgressController extends Controller
{
    /**
     * Show progress dashboard
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $userId = $this->user()['id'];
        $db = DB::instance();

        // Get system completion stats. last_studied falls back to the most
        // recent study_sessions.started_at when no user_progress row exists
        // yet for that system — otherwise the user studies a system but the
        // /progress page keeps showing "Last studied: Never" until they
        // formally complete a lesson, which feels broken.
        $systemProgress = $db->query(
            "SELECT s.id, s.name, s.color_hex, s.icon,
                    COUNT(DISTINCT CASE WHEN up.status = 'completed' THEN up.lesson_id END) as lessons_done,
                    COUNT(DISTINCT l.id) as lessons_total,
                    ROUND(COUNT(DISTINCT CASE WHEN up.status = 'completed' THEN up.lesson_id END) * 100.0 / NULLIF(COUNT(DISTINCT l.id), 0), 0) as completion_percentage,
                    COALESCE(MAX(up.last_studied), MAX(ss.started_at)) as last_studied
            FROM systems s
            LEFT JOIN lessons l ON s.id = l.system_id AND l.is_published = 1
            LEFT JOIN user_progress up ON s.id = up.system_id AND up.user_id = ? AND up.lesson_id = l.id
            LEFT JOIN study_sessions ss ON s.id = ss.system_id AND ss.user_id = ?
            GROUP BY s.id, s.name, s.color_hex, s.icon
            ORDER BY s.sort_order ASC",
            [$userId, $userId]
        );

        // Get overall stats
        $overallStats = $db->queryOne(
            "SELECT (SELECT COUNT(DISTINCT id) FROM systems) as total_systems,
                    COUNT(DISTINCT CASE WHEN up.status = 'completed' THEN up.system_id END) as completed_systems,
                    COUNT(DISTINCT CASE WHEN up.status = 'in_progress' THEN up.system_id END) as in_progress_systems
            FROM user_progress up
            WHERE up.user_id = ?",
            [$userId]
        );

        // Phase 14: don't hardcode the system count — Q400 has 22 today but
        // adding a B737 system would silently desync this number from the
        // dashboard's (which reads from `systems`).
        $totalSystems = (int) ($db->queryOne(
            'SELECT COUNT(*) c FROM systems WHERE is_published = 1'
        )['c'] ?? 0);
        if ($totalSystems === 0) $totalSystems = 22; // safety net
        $completedSystems = (int)($overallStats['completed_systems'] ?? 0);
        $inProgressSystems = (int)($overallStats['in_progress_systems'] ?? 0);
        $notStartedSystems = max(0, $totalSystems - $completedSystems - $inProgressSystems);
        $overallPercentage = $totalSystems > 0 ? round(($completedSystems / $totalSystems) * 100) : 0;

        // Get user stats
        $user = $db->queryOne("SELECT study_streak FROM users WHERE id = ?", [$userId]);
        $studyStreak = (int)($user['study_streak'] ?? 0);

        // Get total study time
        $studyTimeResult = $db->queryOne(
            "SELECT COALESCE(SUM(duration_secs), 0) as total_secs FROM study_sessions WHERE user_id = ?",
            [$userId]
        );
        $totalStudyTimeMins = (int)(($studyTimeResult['total_secs'] ?? 0) / 60);

        // Get average quiz score
        $quizStats = $db->queryOne(
            "SELECT COALESCE(ROUND(AVG(score)), 0) as avg_score FROM quiz_attempts WHERE user_id = ? AND status = 'completed'",
            [$userId]
        );
        $averageQuizScore = (int)($quizStats['avg_score'] ?? 0);

        // Get weak topics
        $weakTopics = $db->query(
            "SELECT s.name as system_name, uts.strength_score, s.id as system_id
            FROM user_topic_strength uts
            JOIN systems s ON uts.system_id = s.id
            WHERE uts.user_id = ?
            ORDER BY uts.strength_score ASC
            LIMIT 5",
            [$userId]
        );

        // Get study history (last 30 days)
        $studyHistory = $db->query(
            "SELECT DATE(started_at) as date, COUNT(*) as session_count
            FROM study_sessions
            WHERE user_id = ? AND started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(started_at)
            ORDER BY date ASC",
            [$userId]
        );

        $data = [
            'title' => 'My Progress',
            'stats' => [
                'total_systems' => $totalSystems,
                'completed_systems' => $completedSystems,
                'in_progress_systems' => $inProgressSystems,
                'not_started_systems' => $notStartedSystems,
                'overall_percentage' => $overallPercentage,
                'study_streak' => $studyStreak,
                'total_study_time_mins' => $totalStudyTimeMins,
                'average_quiz_score' => $averageQuizScore,
            ],
            'systemProgress' => $systemProgress,
            'weakTopics' => $weakTopics,
            'studyHistory' => $studyHistory,
        ];

        $html = $this->view('progress/index', $data, 'pilot');
        $response->html($html);
    }
}
