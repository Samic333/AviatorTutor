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
     * Phase 2 (overhaul) — when study_chrome_v2 is on, study pages render
     * into views/layouts/study.php instead of pilot.php. Centralised so we
     * don't repeat the flag check in every action.
     */
    private function studyChromeV2(): bool
    {
        try {
            $cfg = require BASE_PATH . '/config/app.php';
            return !empty($cfg['features']['study_chrome_v2']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Build the data the new study layout needs: breadcrumb segments, mode
     * switcher entries, drawer lessons. Returned as a partial $data array
     * the action merges into its own.
     *
     * @param array $system  current system row (must have id, name, ata_code)
     * @param string $modeKey one of 'slides','detail','revision','flashcards','quiz','mnemonics','mind_map','deep_notes'
     * @param ?array $lesson  optional current lesson row (adds the lesson title to the crumb)
     * @return array<string,mixed>
     */
    private function studyChromeData(array $system, string $modeKey, ?array $lesson = null): array
    {
        $sid       = (int) ($system['id'] ?? 0);
        $sysName   = (string) ($system['name'] ?? '');
        $sysAccent = (string) ($system['color'] ?? $system['color_hex'] ?? '#34d399');

        // Sibling lessons for the drawer.
        $drawerLessons = [];
        try {
            $drawerLessons = DB::instance()->query(
                'SELECT id, title, slug, sort_order
                   FROM lessons
                  WHERE system_id = ? AND is_published = 1
                  ORDER BY sort_order, id',
                [$sid]
            );
        } catch (\Throwable $e) {
            // Lessons table missing — drawer simply renders empty.
        }

        // Breadcrumb: Q400 > <System> > <Mode> [> <Lesson>]
        $crumb = [
            ['label' => 'Q400',     'href' => '/my-subjects'],
            ['label' => $sysName,   'href' => '/study/' . $sid],
        ];
        $modeLabels = [
            'slides'      => 'Slides',
            'detail'      => 'Lessons',
            'revision'    => 'Revision',
            'flashcards'  => 'Flashcards',
            'quiz'        => 'Quiz',
            'mnemonics'   => 'Mnemonics',
            'mind_map'    => 'Mind Map',
            'deep_notes'  => 'Deep Notes',
        ];
        $crumb[] = ['label' => $modeLabels[$modeKey] ?? ucfirst($modeKey), 'href' => ''];
        if ($lesson && !empty($lesson['title'])) {
            $crumb[] = ['label' => (string) $lesson['title'], 'href' => ''];
        }

        // Mode switcher. Phase-3 modes (mnemonics, mind_map, deep_notes)
        // become live links once their feature flag is on; until then they
        // render as greyed buttons so learners can see what's coming.
        $cfg = [];
        try { $cfg = require BASE_PATH . '/config/app.php'; } catch (\Throwable $e) {}
        $featOn = static fn(string $f): bool => !empty($cfg['features'][$f] ?? false);

        $modes = [
            ['key' => 'slides',     'label' => 'Slides',
             'href' => $lesson ? ('/study/' . $sid . '/lesson/' . (int)$lesson['id'])
                              : ('/study/' . $sid),
             'icon' => 'play-circle',
             'active' => in_array($modeKey, ['slides','detail'], true)],
            ['key' => 'flashcards', 'label' => 'Flashcards',
             'href' => '/flashcards/' . $sid,
             'icon' => 'rectangle-vertical',
             'active' => $modeKey === 'flashcards'],
            ['key' => 'quiz',       'label' => 'Quiz',
             'href' => '/quiz',
             'icon' => 'check-circle-2',
             'active' => $modeKey === 'quiz'],
            ['key' => 'mnemonics',  'label' => 'Mnemonics',
             'href' => '/study/' . $sid . '/mnemonics',
             'icon' => 'brain',
             'active'   => $modeKey === 'mnemonics',
             'disabled' => !$featOn('mnemonics_v2')],
            ['key' => 'mind_map',   'label' => 'Mind Map',
             'href' => '/study/' . $sid . '/mind-map',
             'icon' => 'git-branch',
             'active'   => $modeKey === 'mind_map',
             'disabled' => !$featOn('mind_map')],
            ['key' => 'deep_notes', 'label' => 'Deep Notes',
             'href' => '/study/' . $sid . '/deep-notes',
             'icon' => 'file-text',
             'active'   => $modeKey === 'deep_notes',
             'disabled' => !$featOn('deep_notes')],
        ];

        return [
            'studyBreadcrumb'      => $crumb,
            'studyModes'           => $modes,
            'studySystemColor'     => $sysAccent,
            'drawerSystem'         => $system,
            'drawerLessons'        => $drawerLessons,
            'drawerCurrentLessonId' => $lesson ? (int)($lesson['id'] ?? 0) : 0,
            'studyChromeV2'        => true, // signals templates to suppress legacy chrome
        ];
    }

    /**
     * Phase 5 (overhaul): /study/{id} now resolves to "what the learner
     * actually wants" — the next lesson to study — rather than the old
     * long chapter-list page.
     *
     * Resolution order:
     *   1. The most-recently-touched in-progress lesson (last_studied DESC).
     *   2. The first published lesson by sort order.
     *   3. /systems/{id} (the system detail page) if the system has no
     *      lessons yet — preserves a useful destination for half-seeded data.
     *
     * This single redirect fixes every "Continue" / "Open" / "Back to
     * system" call site in the app at once (9+ view files were linking
     * here). The legacy view template lives at views/study/_legacy_detail.php
     * and is no longer rendered. Bookmarks and search-engine inbound
     * links continue to work because we 302, not 404.
     */
    public function detail(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $id     = (int) $this->param('id');
        $userId = (int) ($this->user()['id'] ?? 0);
        $db     = DB::instance();

        $system = $db->queryOne(
            'SELECT id FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $this->renderNotFound(
                'System Not Found',
                'That aircraft system isn’t in the library yet, or the URL is off.',
                '/systems',
                'Browse all systems'
            );
            return;
        }

        // Pick the "best" lesson to land on:
        //  a) most-recently-touched in_progress lesson (resume).
        //  b) earliest unfinished lesson by sort order (forward).
        //  c) first published lesson at all (fresh start).
        $resume = $db->queryOne(
            'SELECT l.id
               FROM lessons l
               JOIN user_progress up
                 ON up.lesson_id = l.id AND up.user_id = ?
              WHERE l.system_id = ?
                AND l.is_published = 1
                AND up.status = "in_progress"
              ORDER BY up.last_studied DESC
              LIMIT 1',
            [$userId, $id]
        );

        $lessonId = (int) ($resume['id'] ?? 0);

        if ($lessonId === 0) {
            $forward = $db->queryOne(
                'SELECT l.id
                   FROM lessons l
              LEFT JOIN user_progress up
                     ON up.lesson_id = l.id AND up.user_id = ?
                  WHERE l.system_id = ?
                    AND l.is_published = 1
                    AND (up.status IS NULL OR up.status != "completed")
               ORDER BY l.sort_order, l.id
                  LIMIT 1',
                [$userId, $id]
            );
            $lessonId = (int) ($forward['id'] ?? 0);
        }

        if ($lessonId === 0) {
            $first = $db->queryOne(
                'SELECT id FROM lessons
                  WHERE system_id = ? AND is_published = 1
               ORDER BY sort_order, id
                  LIMIT 1',
                [$id]
            );
            $lessonId = (int) ($first['id'] ?? 0);
        }

        if ($lessonId > 0) {
            $this->redirect('/study/' . $id . '/lesson/' . $lessonId);
            return;
        }

        // No lessons published for this system at all — fall back to the
        // system-detail page rather than 404, so the learner sees the
        // system's metadata + flashcard/quiz counts instead of a dead end.
        $this->redirect('/systems/' . $id);
    }

    /**
     * Phase 3 — Mnemonics mode. Renders the system's mnemonics with their
     * letter-by-letter breakdown, "why it works" explanation, and a worked
     * example. Falls back to an empty list if the table hasn't been
     * migrated yet (so the page never 500s on a partially-deployed env).
     */
    public function mnemonics(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();
        $cfg = require BASE_PATH . '/config/app.php';
        if (empty($cfg['features']['mnemonics_v2'])) {
            $this->renderNotFound('Mnemonics Coming Soon', 'Memory aids are being prepped — check back shortly.', '/systems', 'Browse all systems');
            return;
        }

        $systemId = (int) $this->param('id');
        $db       = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$systemId]
        );
        if (!$system) {
            $this->renderNotFound('System Not Found', 'That system isn\'t in the library yet.', '/systems', 'Browse all systems');
            return;
        }

        // Phase 11 follow-up (bug 31): support optional lesson-scope filter so
        // the slide player can link "Mnemonic for this concept" → only the
        // mnemonics tied to a specific lesson_id surface, with system-level
        // mnemonics (lesson_id IS NULL) acting as a fallback. Without a
        // lesson param, all system mnemonics show as before.
        $lessonId = (int) ($_GET['lesson'] ?? 0);

        $mnemonics = [];
        $allSystems = [];
        try {
            if ($lessonId > 0) {
                $mnemonics = $db->query(
                    'SELECT id, phrase, breakdown_json, why_it_works, worked_example, audio_url, lesson_id
                       FROM mnemonics
                      WHERE system_id = ?
                        AND is_published = 1
                        AND (lesson_id IS NULL OR lesson_id = ?)
                      ORDER BY (lesson_id IS NULL), sort_order, id',
                    [$systemId, $lessonId]
                );
            } else {
                $mnemonics = $db->query(
                    'SELECT id, phrase, breakdown_json, why_it_works, worked_example, audio_url, lesson_id
                       FROM mnemonics
                      WHERE system_id = ? AND is_published = 1
                      ORDER BY sort_order, id',
                    [$systemId]
                );
            }

            // Phase 11 — list every system that has at least one published
            // mnemonic so the top-of-page system-jumper can cross-link
            // straight into the matching mnemonics page.
            $allSystems = $db->query(
                'SELECT s.id, s.name, s.ata_code,
                        COUNT(m.id) AS mnemonic_count
                   FROM systems s
                   JOIN mnemonics m ON m.system_id = s.id AND m.is_published = 1
                  WHERE s.is_published = 1
                  GROUP BY s.id, s.name, s.ata_code, s.sort_order
                  ORDER BY s.sort_order, s.name'
            );
        } catch (\Throwable $e) {
            // mnemonics table not migrated yet — render empty state.
        }

        $systemView = [
            'id'       => (int)$system['id'],
            'name'     => $system['name'],
            'ata_code' => $system['ata_code'],
            'color'    => $system['color_hex'] ?? '#34d399',
            'icon'     => $system['icon'] ?? 'zap',
        ];

        $data = [
            'title'      => 'Mnemonics — ' . htmlspecialchars($system['name']),
            'system'     => $systemView,
            'mnemonics'  => $mnemonics,
            'allSystems' => $allSystems,
        ];

        $layout = 'pilot';
        if ($this->studyChromeV2()) {
            $data   = array_merge($data, $this->studyChromeData($systemView, 'mnemonics'));
            $layout = 'study';
        }

        $response->html($this->view('study/mnemonics', $data, $layout));
    }

    /**
     * Phase 3 — Mind Map mode. Renders a vanilla-SVG hierarchical mind
     * map of the system: subtopics → sections → key facts. Falls back to
     * an empty state if no subtopics/sections are seeded.
     */
    public function mindMap(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();
        $cfg = require BASE_PATH . '/config/app.php';
        if (empty($cfg['features']['mind_map'])) {
            $this->renderNotFound('Mind Map Coming Soon', 'The mind map view is being prepped — check back shortly.', '/systems', 'Browse all systems');
            return;
        }

        $systemId = (int) $this->param('id');
        $db       = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$systemId]
        );
        if (!$system) {
            $this->renderNotFound('System Not Found', 'That system isn\'t in the library yet.', '/systems', 'Browse all systems');
            return;
        }

        // Build hierarchy: system → lessons → sections + JSON facts. Pulls
        // from BOTH lesson_sections (typed structured chunks) and the JSON
        // columns on lessons so the map captures the full content depth.
        $lessons = $db->query(
            'SELECT id, title, slug, key_facts, must_know, exam_traps
               FROM lessons
              WHERE system_id = ? AND is_published = 1
              ORDER BY sort_order, id',
            [$systemId]
        );

        $tree = [
            'id'    => 'sys-' . $systemId,
            'label' => (string) $system['name'],
            'kind'  => 'system',
            'children' => [],
        ];
        foreach ($lessons as $l) {
            $lid  = (int) $l['id'];
            $node = [
                'id'    => 'lsn-' . $lid,
                'label' => (string) $l['title'],
                'kind'  => 'lesson',
                'href'  => '/study/' . $systemId . '/lesson/' . $lid,
                'children' => [],
            ];

            // Pull lesson_sections so the mind map shows real structure
            // (overview, components, operation, abnormal, etc.) — not just
            // a flat list of facts. Wrapped in try/catch so legacy DBs
            // without the table still produce a usable tree.
            try {
                $sections = $db->query(
                    'SELECT id, title, body, section_type
                       FROM lesson_sections
                      WHERE lesson_id = ?
                      ORDER BY sort_order, id',
                    [$lid]
                );
                if (!empty($sections)) {
                    $sBucket = ['id' => 'sec-' . $lid, 'label' => 'Sections', 'kind' => 'bucket', 'children' => []];
                    foreach ($sections as $sec) {
                        $bodyTxt = trim(strip_tags((string)($sec['body'] ?? '')));
                        $secNode = [
                            'id'     => 'sec-' . (int)$sec['id'],
                            'label'  => mb_strimwidth((string)$sec['title'], 0, 60, '…'),
                            'kind'   => 'leaf',
                            'href'   => '/study/' . $systemId . '/deep-notes#dn-section-' . (int)$sec['id'],
                            'detail' => mb_strimwidth($bodyTxt, 0, 600, '…'),
                        ];
                        $sBucket['children'][] = $secNode;
                    }
                    $node['children'][] = $sBucket;
                }
            } catch (\Throwable $e) { /* skip section bucket */ }

            foreach (['key_facts' => 'Key facts', 'must_know' => 'Must know', 'exam_traps' => 'Exam traps'] as $col => $label) {
                $arr = json_decode((string)($l[$col] ?? '[]'), true);
                if (is_array($arr) && !empty($arr)) {
                    $bucket = ['id' => $col . '-' . $lid, 'label' => $label, 'kind' => 'bucket', 'children' => []];
                    foreach ($arr as $i => $item) {
                        $text = is_string($item) ? $item : (string)($item['text'] ?? json_encode($item));
                        $bucket['children'][] = [
                            'id'     => $col . '-' . $lid . '-' . $i,
                            'label'  => mb_strimwidth($text, 0, 80, '…'),
                            'kind'   => 'leaf',
                            'detail' => $text,
                        ];
                    }
                    $node['children'][] = $bucket;
                }
            }
            $tree['children'][] = $node;
        }

        $systemView = [
            'id'       => (int)$system['id'],
            'name'     => $system['name'],
            'ata_code' => $system['ata_code'],
            'color'    => $system['color_hex'] ?? '#34d399',
            'icon'     => $system['icon'] ?? 'zap',
        ];

        $data = [
            'title'  => 'Mind Map — ' . htmlspecialchars($system['name']),
            'system' => $systemView,
            'tree'   => $tree,
        ];

        $layout = 'pilot';
        if ($this->studyChromeV2()) {
            $data   = array_merge($data, $this->studyChromeData($systemView, 'mind_map'));
            $layout = 'study';
        }

        $response->html($this->view('study/mind_map', $data, $layout));
    }

    /**
     * Phase 3 — Deep Notes mode. Renders every published lesson + its
     * lesson_sections as one long, anchored, searchable document.
     *
     * Gated by the `deep_notes` feature flag (off by default in production
     * until the content is reviewed).
     */
    public function deepNotes(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();
        $cfg = require BASE_PATH . '/config/app.php';
        if (empty($cfg['features']['deep_notes'])) {
            $this->renderNotFound('Deep Notes Coming Soon', 'This mode is being prepped and will be available shortly.', '/systems', 'Browse all systems');
            return;
        }

        $systemId = (int) $this->param('id');
        $db       = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code, description, color_hex, icon
             FROM systems WHERE id = ? AND is_published = 1',
            [$systemId]
        );
        if (!$system) {
            $this->renderNotFound('System Not Found', 'That aircraft system isn\'t in the library yet.', '/systems', 'Browse all systems');
            return;
        }

        $lessons = $db->query(
            'SELECT id, title, slug, body, summary, sort_order
               FROM lessons
              WHERE system_id = ? AND is_published = 1
              ORDER BY sort_order, id',
            [$systemId]
        );
        $sectionsByLesson = [];
        foreach ($lessons as $l) {
            try {
                $sectionsByLesson[(int)$l['id']] = $db->query(
                    'SELECT id, title, body, section_type, sort_order
                       FROM lesson_sections
                      WHERE lesson_id = ?
                      ORDER BY sort_order, id',
                    [(int)$l['id']]
                );
            } catch (\Throwable $e) {
                $sectionsByLesson[(int)$l['id']] = [];
            }
        }

        $systemView = [
            'id'       => (int)$system['id'],
            'name'     => $system['name'],
            'ata_code' => $system['ata_code'],
            'color'    => $system['color_hex'] ?? '#34d399',
            'icon'     => $system['icon'] ?? 'zap',
        ];

        $data = [
            'title'            => 'Deep Notes — ' . htmlspecialchars($system['name']),
            'system'           => $systemView,
            'lessons'          => $lessons,
            'sectionsByLesson' => $sectionsByLesson,
        ];

        $layout = 'pilot';
        if ($this->studyChromeV2()) {
            $data   = array_merge($data, $this->studyChromeData($systemView, 'deep_notes'));
            $layout = 'study';
        }

        $response->html($this->view('study/deep_notes', $data, $layout));
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
               AND (status IS NULL OR status = "published")
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

        // Phase 3 fix: record this view as a study session so the dashboard's
        // Continue Studying / Recent Activity / Study Activity heatmap pick
        // it up. De-duped at the helper level so refreshes don't spam.
        $this->recordStudySession((int) $userId, $systemId, 'detail');

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
        // status = 'published' filter prevents draft slides (Phase 3+
        // AI-generated drafts) from leaking before the admin reviews and
        // publishes them. The legacy fallback query below applies the
        // same filter — old environments without the status column will
        // throw and fall through to the catch.
        try {
            $slides = $db->query(
                "SELECT id, sort_order, slide_type, title, body,
                        media_type, media_url, media_alt,
                        key_point, ops_relevance, question
                 FROM lesson_slides
                 WHERE lesson_id = ?
                   AND status = 'published'
                   AND $diffColumn = 1
                 ORDER BY sort_order, id",
                [$lessonId]
            );
            // If the deck author hasn't tagged any slide for this level, fall
            // back to the full published deck so the learner is never stuck
            // on an empty module.
            if (empty($slides)) {
                $slides = $db->query(
                    "SELECT id, sort_order, slide_type, title, body,
                            media_type, media_url, media_alt,
                            key_point, ops_relevance, question
                     FROM lesson_slides
                     WHERE lesson_id = ?
                       AND status = 'published'
                     ORDER BY sort_order, id",
                    [$lessonId]
                );
            }
        } catch (\Throwable $e) {
            // Legacy DB without status column — safe because every row in
            // such a DB is by definition pre-Phase-3 and considered published.
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

        // user_slide_progress is added by the 2026_04_29_lesson_slides
        // migration. Wrap defensively so an environment that hasn't run the
        // migration yet still serves the lesson — empty progress just means
        // every slide renders as "unseen", which is the correct fallback
        // for a fresh learner anyway. (Was the cause of HTTP 500 on first
        // study slide, May 2026.)
        $progress = [];
        try {
            $progressRows = $db->query(
                'SELECT slide_id, answered_correct, attempts
                 FROM user_slide_progress
                 WHERE user_id = ? AND lesson_id = ?',
                [$userId, $lessonId]
            );
            foreach ($progressRows as $row) {
                $progress[(int)$row['slide_id']] = [
                    'answered_correct' => (int)$row['answered_correct'],
                    'attempts'         => (int)$row['attempts'],
                ];
            }
        } catch (\Throwable $e) {
            // Table missing or schema drift — proceed with empty progress.
        }

        $systemView = [
            'id'          => (int)$system['id'],
            'name'        => $system['name'],
            'ata_code'    => $system['ata_code'],
            'description' => $system['description'],
            'color'       => $system['color_hex'] ?? '#34d399',
            'icon'        => $system['icon'] ?? 'zap',
        ];

        $data = [
            'title'   => htmlspecialchars($lesson['title']) . ' — ' . htmlspecialchars($system['name']),
            'system'  => $systemView,
            'lesson'     => $lesson,
            'slides'     => $slides,
            'progress'   => $progress,
            'difficulty' => $difficulty,
            'qrhLinks'   => $this->loadQrhLinksForLesson($lessonId),
        ];

        $layout = 'pilot';
        if ($this->studyChromeV2()) {
            $data   = array_merge($data, $this->studyChromeData($systemView, 'slides', $lesson));
            // Hand the slide list to the drawer + picker.
            $data['drawerSlides']            = $slides;
            $data['drawerCurrentSlideIndex'] = 0;
            $data['studyTotalSlides']        = count($slides);
            $data['studyCurrentSlideIndex']  = 0;
            $layout = 'study';
        }

        $html = $this->view('study/lesson_slides', $data, $layout);
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
