<?php
/**
 * Dashboard Controller
 *
 * Handles dashboard/home page requests
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();

        $user = $this->user();
        $userId = (int) $user['id'];

        // No active subscription? Render the paywall view instead of the full dashboard.
        // Admins skip the paywall entirely.
        $isAdmin = ($user['role'] ?? '') === 'admin';
        if (!$isAdmin && !\App\Services\SubscriptionService::hasActive($userId)) {
            $latest = \App\Services\SubscriptionService::latest($userId);
            $response->html($this->view('subscription/paywall', [
                'title'              => 'Subscribe to start studying',
                'description'        => 'AviatorTutor self-study requires an active monthly subscription.',
                'user'               => $user,
                'latestSubscription' => $latest,
            ], 'marketing'));
            return;
        }

        $db = \App\Core\DB::instance();

        // Count systems studied
        $systemsStudied = $db->queryOne(
            'SELECT COUNT(DISTINCT system_id) as count FROM user_progress
             WHERE user_id = ? AND status != "not_started"',
            [$userId]
        )['count'] ?? 0;

        // Count systems completed
        $systemsCompleted = $db->queryOne(
            'SELECT COUNT(DISTINCT system_id) as count FROM user_progress
             WHERE user_id = ? AND status = "completed"',
            [$userId]
        )['count'] ?? 0;

        // Count flashcards due today
        $flashcardsDue = $db->queryOne(
            'SELECT COUNT(*) as count FROM flashcard_reviews
             WHERE user_id = ? AND next_review_at <= NOW()',
            [$userId]
        )['count'] ?? 0;

        // Average quiz score
        $quizScoreResult = $db->queryOne(
            'SELECT AVG(score) as avg_score FROM quiz_attempts
             WHERE user_id = ? AND status = "completed"',
            [$userId]
        );
        $averageQuizScore = round($quizScoreResult['avg_score'] ?? 0);

        // Get study streak and last active
        $userStats = $db->queryOne(
            'SELECT study_streak, last_active FROM users WHERE id = ?',
            [$userId]
        );

        // Get in-progress systems with progress
        $inProgress = $db->query(
            'SELECT s.id, s.name, s.icon, s.color_hex,
                    COUNT(DISTINCT l.id) as total_lessons,
                    COUNT(DISTINCT CASE WHEN up.status = "completed" THEN l.id END) as completed_lessons
             FROM systems s
             LEFT JOIN lessons l ON s.id = l.system_id AND l.is_published = 1
             LEFT JOIN user_progress up ON s.id = up.system_id AND up.user_id = ? AND l.id = up.lesson_id
             WHERE s.id IN (SELECT DISTINCT system_id FROM user_progress WHERE user_id = ? AND status = "in_progress")
             GROUP BY s.id, s.name, s.icon, s.color_hex
             ORDER BY s.sort_order
             LIMIT 3',
            [$userId, $userId]
        );

        foreach ($inProgress as &$sys) {
            $sys['completion_percentage'] = $sys['total_lessons'] > 0
                ? round(($sys['completed_lessons'] / $sys['total_lessons']) * 100)
                : 0;
            $sys['current_topic'] = 'In Progress';
        }

        // Get recent study sessions
        $recentActivity = [];
        $sessions = $db->query(
            'SELECT ss.id, ss.session_type, ss.started_at, s.name as system_name
             FROM study_sessions ss
             LEFT JOIN systems s ON ss.system_id = s.id
             WHERE ss.user_id = ?
             ORDER BY ss.started_at DESC
             LIMIT 5',
            [$userId]
        );

        foreach ($sessions as $session) {
            $icon = match ($session['session_type']) {
                'detail' => 'book-open',
                'revision' => 'zap',
                'flashcard' => 'credit-card',
                'quiz' => 'check-square',
                'diagram' => 'activity',
                default => 'check-circle'
            };

            $recentActivity[] = [
                'icon' => $icon,
                'description' => ucfirst($session['session_type']) . ' session on ' . htmlspecialchars($session['system_name'] ?? 'General'),
                'time_ago' => $this->timeAgo($session['started_at']),
            ];
        }

        // Get flashcards and quizzes due for review
        $dueForReview = [];
        $dueFlashcards = $db->query(
            'SELECT fc.id, fc.front, s.name as system_name
             FROM flashcard_reviews fr
             JOIN flashcards fc ON fr.flashcard_id = fc.id
             JOIN systems s ON fc.system_id = s.id
             WHERE fr.user_id = ? AND fr.next_review_at <= NOW()
             LIMIT 5',
            [$userId]
        );

        foreach ($dueFlashcards as $fc) {
            $dueForReview[] = [
                'type' => 'Flashcard',
                'title' => substr($fc['front'], 0, 50),
                'system_name' => $fc['system_name'],
            ];
        }

        // Get systems not yet started for suggestion
        $notStarted = $db->queryOne(
            'SELECT s.id, s.name FROM systems s
             WHERE s.is_published = 1 AND s.id NOT IN (
                SELECT DISTINCT system_id FROM user_progress WHERE user_id = ?
             )
             ORDER BY s.sort_order
             LIMIT 1',
            [$userId]
        );

        $suggestedTopic = null;
        if ($notStarted) {
            $suggestedTopic = [
                'system_id' => $notStarted['id'],
                'topic_name' => $notStarted['name'],
                'reason' => 'Start learning a new system',
            ];
        }

        // Build quiz performance data for chart
        $quizPerformance = $db->query(
            'SELECT s.name, AVG(qa.score) as avg_score
             FROM quiz_attempts qa
             JOIN quizzes q ON qa.quiz_id = q.id
             JOIN systems s ON q.system_id = s.id
             WHERE qa.user_id = ? AND qa.status = "completed"
             GROUP BY s.id, s.name
             ORDER BY MAX(qa.completed_at) DESC
             LIMIT 10',
            [$userId]
        );

        $quizData = [
            'labels' => array_column($quizPerformance, 'name'),
            'scores' => array_map('round', array_column($quizPerformance, 'avg_score')),
        ];

        // Build streak calendar data (last 90 days)
        $streakData = [];
        $today = new \DateTime();
        for ($i = 89; $i >= 0; $i--) {
            $date = (clone $today)->modify("-$i days");
            $dateStr = $date->format('Y-m-d');

            $activity = $db->queryOne(
                'SELECT COUNT(*) as count FROM study_sessions
                 WHERE user_id = ? AND DATE(started_at) = ?',
                [$userId, $dateStr]
            );

            $intensity = $activity['count'] > 0 ? min($activity['count'] / 5, 1.0) : 0;

            $streakData[] = [
                'date' => $dateStr,
                'intensity' => $intensity,
            ];
        }

        // Aircraft scope: which aircraft is this user studying right now?
        // Brand-new users (no preference, no study activity, not admin) see a zero-state.
        $currentAircraft   = \App\Services\AircraftService::currentForUser((int) $userId);
        $studyableAircraft = \App\Services\AircraftService::studyable();
        $totalSessions = (int) (\App\Core\DB::instance()
            ->queryOne('SELECT COUNT(*) c FROM study_sessions WHERE user_id = ?', [$userId])['c'] ?? 0);
        // Read preferred_aircraft_id from DB (session may be stale).
        $userRow = \App\Core\DB::instance()
            ->queryOne('SELECT preferred_aircraft_id FROM users WHERE id = ?', [$userId]) ?: [];
        $hasPref = !empty($userRow['preferred_aircraft_id']);
        $isAdmin = ($user['role'] ?? '') === 'admin';
        $isFreshAccount = !$isAdmin
            && !$hasPref
            && $totalSessions === 0
            && $systemsStudied === 0
            && $flashcardsDue === 0;

        $data = [
            'user' => $user,
            'title' => 'Dashboard',
            'page_title' => 'Welcome to ' . ($currentAircraft['short_name'] ?? 'AviatorTutor'),
            'stats' => [
                'systems_studied' => $systemsStudied,
                'systems_completed' => $systemsCompleted,
                'flashcards_due' => $flashcardsDue,
                'average_quiz_score' => $averageQuizScore,
                'study_streak' => $userStats['study_streak'] ?? 0,
            ],
            'inProgress' => $inProgress,
            'recentActivity' => $recentActivity,
            'dueForReview' => $dueForReview,
            'suggestedTopic' => $suggestedTopic,
            'quizData' => $quizData,
            'streakData' => $streakData,
            'currentAircraft'   => $currentAircraft,
            'studyableAircraft' => $studyableAircraft,
            'isFreshAccount'    => $isFreshAccount,
            'csrf_token'        => \App\Core\CSRF::generate(),
        ];

        $html = $this->view('dashboard/index', $data);
        $response->html($html);
    }

    /**
     * Convert timestamp to "time ago" format
     */
    private function timeAgo(string $timestamp): string
    {
        $time = strtotime($timestamp);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }
}
