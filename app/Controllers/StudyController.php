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
        $this->requireActiveSubscription();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $this->renderNotFound('System Not Found', 'That aircraft system isn’t in the library yet, or the URL is off.', '/systems', 'Browse all systems');
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

        $html = $this->view('study/detail', $data, 'pilot');
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
        $this->requireActiveSubscription();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $this->renderNotFound('System Not Found', 'That aircraft system isn’t in the library yet, or the URL is off.', '/systems', 'Browse all systems');
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

        $html = $this->view('study/revision', $data, 'pilot');
        $response->html($html);
    }

    /**
     * Show interactive slide-based lesson player
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function lesson(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $systemId = (int)$this->param('id');
        $lessonId = (int)$this->param('lessonId');
        $userId   = $this->user()['id'];
        $db       = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$systemId]
        );

        if (!$system) {
            $this->renderNotFound('System Not Found', 'That aircraft system isn’t in the library yet, or the URL is off.', '/systems', 'Browse all systems');
            return;
        }

        $lesson = $db->queryOne(
            'SELECT id, system_id, title, slug, summary
             FROM lessons
             WHERE id = ? AND system_id = ? AND is_published = 1',
            [$lessonId, $systemId]
        );

        if (!$lesson) {
            $this->renderNotFound('Lesson Not Found', 'That lesson isn’t available — it may have been moved or unpublished.', '/study/' . $systemId, 'Back to system');
            return;
        }

        // Resolve the active difficulty: query string overrides session,
        // session falls back to default 'intermediate'. Persist updates so
        // the toggle is sticky across slide-deck navigation.
        $allowedDiff = ['beginner', 'intermediate', 'advanced'];
        $reqDiff = (string) $this->query('difficulty', '');
        if ($reqDiff !== '' && in_array($reqDiff, $allowedDiff, true)) {
            $_SESSION['study_difficulty'] = $reqDiff;
        }
        $difficulty = $_SESSION['study_difficulty'] ?? 'intermediate';
        if (!in_array($difficulty, $allowedDiff, true)) {
            $difficulty = 'intermediate';
        }
        $diffColumn = 'show_' . $difficulty;

        // Filter slides by the selected difficulty. Falls back to the
        // unfiltered query if the gating columns haven't been migrated yet.
        try {
            $slides = $db->query(
                "SELECT id, sort_order, slide_type, title, body,
                        media_type, media_url, media_alt,
                        key_point, ops_relevance, question
                 FROM lesson_slides
                 WHERE lesson_id = ? AND $diffColumn = 1
                 ORDER BY sort_order, id",
                [$lessonId]
            );
            // If the deck author hasn't tagged any slide for this level, fall
            // back to the full deck so the learner is never stuck on an empty
            // module.
            if (empty($slides)) {
                $slides = $db->query(
                    'SELECT id, sort_order, slide_type, title, body,
                            media_type, media_url, media_alt,
                            key_point, ops_relevance, question
                     FROM lesson_slides
                     WHERE lesson_id = ?
                     ORDER BY sort_order, id',
                    [$lessonId]
                );
            }
        } catch (\Throwable $e) {
            $slides = $db->query(
                'SELECT id, sort_order, slide_type, title, body,
                        media_type, media_url, media_alt,
                        key_point, ops_relevance, question
                 FROM lesson_slides
                 WHERE lesson_id = ?
                 ORDER BY sort_order, id',
                [$lessonId]
            );
        }

        $progressRows = $db->query(
            'SELECT slide_id, answered_correct, attempts
             FROM user_slide_progress
             WHERE user_id = ? AND lesson_id = ?',
            [$userId, $lessonId]
        );
        $progress = [];
        foreach ($progressRows as $row) {
            $progress[(int)$row['slide_id']] = [
                'answered_correct' => (int)$row['answered_correct'],
                'attempts'         => (int)$row['attempts'],
            ];
        }

        $data = [
            'title'   => htmlspecialchars($lesson['title']) . ' — ' . htmlspecialchars($system['name']),
            'system'  => [
                'id'          => (int)$system['id'],
                'name'        => $system['name'],
                'ata_code'    => $system['ata_code'],
                'description' => $system['description'],
                'color'       => $system['color_hex'] ?? '#34d399',
                'icon'        => $system['icon'] ?? 'zap',
            ],
            'lesson'     => $lesson,
            'slides'     => $slides,
            'progress'   => $progress,
            'difficulty' => $difficulty,
            'qrhLinks'   => $this->loadQrhLinksForLesson($lessonId),
        ];

        $html = $this->view('study/lesson_slides', $data, 'pilot');
        $response->html($html);
    }

    /**
     * Load QRH cross-references attached to a lesson, grouped by slide_id.
     * Returns an empty array if the table doesn't exist yet (Phase 4 not
     * migrated) so the slide player simply omits the QRH panel.
     */
    private function loadQrhLinksForLesson(int $lessonId): array
    {
        try {
            $rows = DB::instance()->query(
                'SELECT id, slide_id, qrh_section_title, qrh_excerpt,
                        memory_item, ops_meaning, recognition_cue, memory_trigger
                   FROM lesson_qrh_links
                  WHERE lesson_id = ?
                  ORDER BY sort_order, id',
                [$lessonId]
            );
        } catch (\Throwable $e) {
            return ['lessonWide' => [], 'bySlide' => []];
        }

        $bySlide     = [];
        $lessonWide  = [];
        foreach ($rows as $r) {
            $r['memory_item'] = (int) $r['memory_item'];
            if (!empty($r['slide_id'])) {
                $bySlide[(int) $r['slide_id']][] = $r;
            } else {
                $lessonWide[] = $r;
            }
        }
        return ['lessonWide' => $lessonWide, 'bySlide' => $bySlide];
    }
}
