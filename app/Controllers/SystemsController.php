<?php
/**
 * Systems Controller
 *
 * Handles aircraft systems listing and detail views
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;

class SystemsController extends Controller
{
    /**
     * Show systems list
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $userId = $this->user()['id'];
        $db = \App\Core\DB::instance();

        $systems = $db->query(
            'SELECT s.id, s.name, s.ata_code, s.description, s.color_hex, s.icon,
                    COUNT(DISTINCT l.id) as total_lessons,
                    COUNT(DISTINCT CASE WHEN up.status = "completed" THEN l.id END) as completed_lessons
             FROM systems s
             LEFT JOIN lessons l ON s.id = l.system_id AND l.is_published = 1
             LEFT JOIN user_progress up ON s.id = up.system_id AND up.user_id = ? AND l.id = up.lesson_id
             WHERE s.is_published = 1
             GROUP BY s.id, s.name, s.ata_code, s.description, s.color_hex, s.icon
             ORDER BY s.sort_order',
            [$userId]
        );

        foreach ($systems as &$sys) {
            $sys['color'] = $sys['color_hex'] ?? '#34d399';
            $sys['topic_count'] = $sys['total_lessons'] ?? 0;
            $sys['completion_percentage'] = $sys['total_lessons'] > 0
                ? round(($sys['completed_lessons'] / $sys['total_lessons']) * 100)
                : 0;
            $sys['difficulty'] = 'basic';
        }

        $data = [
            'title' => 'Aircraft Systems',
            'systems' => $systems,
        ];

        $html = $this->view('systems/index', $data);
        $response->html($html);
    }

    /**
     * Show system details
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function show(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = \App\Core\DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon, sort_order
             FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $response->status(404);
            $response->html('<h1>System Not Found</h1>');
            return;
        }

        // Get lessons grouped by subtopic (no completion_percentage column in user_progress)
        $lessons = $db->query(
            'SELECT l.id, l.title, l.slug, l.content_type, l.summary,
                    st.title as subtopic_title, st.id as subtopic_id,
                    up.status, up.confidence
             FROM lessons l
             LEFT JOIN subtopics st ON l.subtopic_id = st.id
             LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ?
             WHERE l.system_id = ? AND l.is_published = 1
             ORDER BY st.sort_order, l.sort_order',
            [$userId, $id]
        );

        // Get flashcard count
        $flashcardCount = $db->queryOne(
            'SELECT COUNT(*) as count FROM flashcards WHERE system_id = ?',
            [$id]
        )['count'] ?? 0;

        // Get quiz count
        $quizCount = $db->queryOne(
            'SELECT COUNT(*) as count FROM quizzes WHERE system_id = ? AND is_published = 1',
            [$id]
        )['count'] ?? 0;

        // Calculate overall progress
        $totalLessons = $db->queryOne(
            'SELECT COUNT(*) as count FROM lessons WHERE system_id = ? AND is_published = 1',
            [$id]
        )['count'] ?? 0;

        $completedLessons = $db->queryOne(
            'SELECT COUNT(*) as count FROM lessons l
             LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ?
             WHERE l.system_id = ? AND l.is_published = 1 AND up.status = "completed"',
            [$userId, $id]
        )['count'] ?? 0;

        $completionPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        // Get user notes
        $userNotes = $db->queryOne(
            'SELECT content FROM notes WHERE user_id = ? AND system_id = ? LIMIT 1',
            [$userId, $id]
        );

        // Get user study sessions for this system
        $sessions = $db->query(
            'SELECT COUNT(*) as session_count FROM study_sessions
             WHERE user_id = ? AND system_id = ?',
            [$userId, $id]
        );

        $data = [
            'title' => htmlspecialchars($system['name']),
            'system' => [
                'id' => $system['id'],
                'name' => $system['name'],
                'ata_code' => $system['ata_code'],
                'description' => $system['description'],
                'color' => $system['color_hex'] ?? '#34d399',
                'icon' => $system['icon'] ?? 'zap',
                'completion_percentage' => $completionPercentage,
                'topic_count' => $totalLessons,
                'estimated_hours' => 5,
                'difficulty' => 'basic',
                'topics' => [],
                'overview' => $system['description'],
                'must_know' => 'Key system characteristics and operations',
                'exam_traps' => 'Common exam question pitfalls',
                'components' => [],
                'operation' => 'System operation details',
                'abnormal' => 'Abnormal procedures and troubleshooting',
            ],
            'lessons' => $lessons,
            'flashcardCount' => $flashcardCount,
            'quizCount' => $quizCount,
            'userNotes' => $userNotes['content'] ?? '',
            'csrf_token' => CSRF::generate(),
            'currentTopic' => null,
            'relatedSystems' => [],
        ];

        $html = $this->view('systems/show', $data);
        $response->html($html);
    }
}
