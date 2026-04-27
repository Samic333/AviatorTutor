<?php
/**
 * Study Controller
 *
 * Handles detailed study and quick revision modes
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\DB;

class StudyController extends Controller
{
    /**
     * Show detailed study page for a system
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function detail(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $response->status(404);
            $response->html('<h1>System Not Found</h1>');
            return;
        }

        // Get all lessons with full content
        $lessons = $db->query(
            'SELECT l.id, l.title, l.slug, l.body, l.content_type,
                    l.summary, l.key_facts, l.must_know, l.exam_traps,
                    st.title as subtopic_title, st.id as subtopic_id,
                    up.status, up.time_spent_secs
             FROM lessons l
             LEFT JOIN subtopics st ON l.subtopic_id = st.id
             LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ?
             WHERE l.system_id = ? AND l.is_published = 1
             ORDER BY st.sort_order, l.sort_order',
            [$userId, $id]
        );

        // Get lesson sections for detailed content
        $sections = [];
        foreach ($lessons as $lesson) {
            $lessonSections = $db->query(
                'SELECT id, title, body, section_type, sort_order
                 FROM lesson_sections
                 WHERE lesson_id = ?
                 ORDER BY sort_order',
                [$lesson['id']]
            );
            $sections[$lesson['id']] = $lessonSections;
        }

        // Get study assets (diagrams, PDFs)
        $assets = $db->query(
            'SELECT id, title, description, file_type, file_path
             FROM study_assets
             WHERE system_id = ?
             ORDER BY file_type',
            [$id]
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
            ],
            'lessons' => $lessons,
            'sections' => $sections,
            'assets' => $assets,
            'mode' => 'detail',
        ];

        $html = $this->view('study/detail', $data);
        $response->html($html);
    }

    /**
     * Show quick revision mode for a system
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function revision(Request $request, Response $response): void
    {
        $this->requireAuth();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $response->status(404);
            $response->html('<h1>System Not Found</h1>');
            return;
        }

        // Get lessons with summary and key facts for quick revision
        $lessons = $db->query(
            'SELECT l.id, l.title, l.slug, l.summary, l.key_facts,
                    l.must_know, l.exam_traps,
                    st.title as subtopic_title
             FROM lessons l
             LEFT JOIN subtopics st ON l.subtopic_id = st.id
             WHERE l.system_id = ? AND l.is_published = 1 AND l.content_type IN ("revision", "overview")
             ORDER BY st.sort_order, l.sort_order
             LIMIT 20',
            [$id]
        );

        // Get all flashcards for this system for quick study
        $flashcards = $db->query(
            'SELECT id, front, back, difficulty
             FROM flashcards
             WHERE system_id = ? AND difficulty IN ("easy", "medium")
             LIMIT 10',
            [$id]
        );

        $data = [
            'title' => 'Quick Revision - ' . htmlspecialchars($system['name']),
            'system' => [
                'id' => $system['id'],
                'name' => $system['name'],
                'ata_code' => $system['ata_code'],
                'description' => $system['description'],
                'color' => $system['color_hex'] ?? '#34d399',
                'icon' => $system['icon'] ?? 'zap',
            ],
            'lessons' => $lessons,
            'flashcards' => $flashcards,
            'mode' => 'revision',
            'revision_modes' => [
                ['duration' => 3, 'label' => '3-Minute Review'],
                ['duration' => 5, 'label' => '5-Minute Review'],
                ['duration' => 10, 'label' => '10-Minute Review'],
            ],
        ];

        $html = $this->view('study/revision', $data);
        $response->html($html);
    }
}
