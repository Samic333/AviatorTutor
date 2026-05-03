<?php
/**
 * Flashcard Controller
 *
 * Manages flashcard study sessions and reviews
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;

class FlashcardController extends Controller
{
    /**
     * List all flashcard sets
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

        $systems = $db->query(
            'SELECT s.id, s.name, s.icon, s.color_hex,
                    COUNT(f.id) as flashcard_count,
                    COUNT(CASE WHEN fr.next_review_at <= NOW() AND fr.user_id = ? THEN 1 END) as due_count
             FROM systems s
             LEFT JOIN flashcards f ON s.id = f.system_id
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id
             WHERE s.is_published = 1
             GROUP BY s.id, s.name, s.icon, s.color_hex
             ORDER BY s.sort_order',
            [$userId]
        );

        // Summary stats
        $totalFlashcards = $db->queryOne(
            'SELECT COUNT(*) as count FROM flashcards f
             JOIN systems s ON f.system_id = s.id
             WHERE s.is_published = 1
               AND (f.status IS NULL OR f.status = "published")'
        )['count'] ?? 0;

        $dueCount = $db->queryOne(
            'SELECT COUNT(*) as count FROM flashcard_reviews
             WHERE user_id = ? AND next_review_at <= NOW()',
            [$userId]
        )['count'] ?? 0;

        $masteredCount = $db->queryOne(
            'SELECT COUNT(*) as count FROM flashcard_reviews
             WHERE user_id = ? AND review_count >= 5',
            [$userId]
        )['count'] ?? 0;

        // Study streak: count consecutive days with at least one review
        $studyStreak = 0;
        $streakRows = $db->query(
            'SELECT DATE(reviewed_at) as review_date
             FROM flashcard_reviews WHERE user_id = ?
             GROUP BY DATE(reviewed_at)
             ORDER BY review_date DESC',
            [$userId]
        );
        $expected = new \DateTime('today');
        foreach ($streakRows as $row) {
            $d = new \DateTime($row['review_date']);
            if ($d->format('Y-m-d') === $expected->format('Y-m-d')) {
                $studyStreak++;
                $expected->modify('-1 day');
            } else {
                break;
            }
        }

        $data = [
            'title'           => 'Flashcards',
            'systems'         => $systems,
            'totalFlashcards' => $totalFlashcards,
            'dueCount'        => $dueCount,
            'masteredCount'   => $masteredCount,
            'studyStreak'     => $studyStreak,
        ];

        $html = $this->view('flashcards/index', $data, 'pilot');
        $response->html($html);
    }

    /**
     * Study a specific flashcard set
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function study(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $this->renderNotFound('System Not Found', 'That system has no flashcard deck yet, or the URL is off.', '/flashcards', 'Back to flashcards');
            return;
        }

        // Phase 3 v2: select category + why_it_matters when present (falls
        // back to defaults on legacy DBs missing those columns).
        try {
            $flashcards = $db->query(
                'SELECT f.id, f.front, f.back, f.expected_answer, f.hint, f.difficulty,
                        f.category, f.theme_color, f.why_it_matters, f.source_slide_id,
                        fr.next_review_at, fr.ease_factor
                 FROM flashcards f
                 LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
                 WHERE f.system_id = ?
                   AND (f.status IS NULL OR f.status = "published")
                 ORDER BY COALESCE(fr.next_review_at, NOW()) ASC',
                [$userId, $id]
            );
        } catch (\Throwable $e) {
            $flashcards = $db->query(
                'SELECT f.id, f.front, f.back, f.expected_answer, f.hint, f.difficulty,
                        fr.next_review_at, fr.ease_factor
                 FROM flashcards f
                 LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
                 WHERE f.system_id = ?
                   AND (f.status IS NULL OR f.status = "published")
                 ORDER BY COALESCE(fr.next_review_at, NOW()) ASC',
                [$userId, $id]
            );
        }

        // Phase 5: cards with an expected_answer are typeable and will
        // be AI-graded. Older manually-authored cards keep the legacy
        // flip-only flow. The view branches on this flag.
        foreach ($flashcards as &$c) {
            $c['typeable'] = !empty($c['expected_answer']);
            if (empty($c['category'])) $c['category'] = 'normal';
        }
        unset($c);

        $data = [
            'title' => 'Flashcards - ' . htmlspecialchars($system['name']),
            'system' => $system,
            'flashcards' => $flashcards,
            'csrf_token' => CSRF::generate(),
        ];

        // Phase 3: switch to the v2 view + study layout when the flag is on.
        $cfg = require BASE_PATH . '/config/app.php';
        if (!empty($cfg['features']['flashcards_v2'])) {
            $sysView = [
                'id'    => (int)$system['id'],
                'name'  => $system['name'],
                'ata_code' => $system['ata_code'] ?? '',
                'color' => '#38BDF8',
            ];
            // Reuse StudyController's chrome data — bring in via a tiny
            // local helper to avoid a coupling/inheritance dance.
            $cd  = $this->buildStudyChromeData($sysView);
            $data = array_merge($data, $cd);
            $template = 'flashcards/study_v2';
            $layout   = !empty($cfg['features']['study_chrome_v2']) ? 'study' : 'pilot';
        } else {
            $template = 'flashcards/study';
            $layout   = 'pilot';
        }

        $html = $this->view($template, $data, $layout);
        $response->html($html);
    }

    /**
     * Local helper: same shape as StudyController::studyChromeData but
     * without the slide-mode coupling. Builds breadcrumb + modes + drawer
     * data so the flashcards page integrates with the V2 chrome.
     *
     * @param array $system  must have id, name, ata_code, color
     * @return array<string,mixed>
     */
    private function buildStudyChromeData(array $system): array
    {
        $sid     = (int) $system['id'];
        $sysName = (string) ($system['name'] ?? '');
        $accent  = (string) ($system['color'] ?? '#38BDF8');

        $drawerLessons = [];
        try {
            $drawerLessons = DB::instance()->query(
                'SELECT id, title, slug FROM lessons
                  WHERE system_id = ? AND is_published = 1
                  ORDER BY sort_order, id',
                [$sid]
            );
        } catch (\Throwable $e) { /* ignore */ }

        $cfg = require BASE_PATH . '/config/app.php';
        $featOn = static fn(string $f): bool => !empty($cfg['features'][$f] ?? false);

        return [
            'studyChromeV2'         => true,
            'studySystemColor'      => $accent,
            'studyBreadcrumb'       => [
                ['label' => 'Q400',  'href' => '/my-subjects'],
                ['label' => $sysName, 'href' => '/study/' . $sid],
                ['label' => 'Flashcards', 'href' => ''],
            ],
            'studyModes' => [
                ['key' => 'slides',     'label' => 'Slides',     'href' => '/study/' . $sid, 'icon' => 'play-circle', 'active' => false],
                ['key' => 'flashcards', 'label' => 'Flashcards', 'href' => '/flashcards/' . $sid, 'icon' => 'rectangle-vertical', 'active' => true],
                ['key' => 'quiz',       'label' => 'Quiz',       'href' => '/quiz', 'icon' => 'check-circle-2'],
                ['key' => 'mnemonics',  'label' => 'Mnemonics',  'href' => '/study/' . $sid . '/mnemonics',  'icon' => 'brain',       'disabled' => !$featOn('mnemonics_v2')],
                ['key' => 'mind_map',   'label' => 'Mind Map',   'href' => '/study/' . $sid . '/mind-map',   'icon' => 'git-branch',  'disabled' => !$featOn('mind_map')],
                ['key' => 'deep_notes', 'label' => 'Deep Notes', 'href' => '/study/' . $sid . '/deep-notes', 'icon' => 'file-text',   'disabled' => !$featOn('deep_notes')],
            ],
            'drawerSystem'          => $system,
            'drawerLessons'         => $drawerLessons,
            'drawerCurrentLessonId' => 0,
        ];
    }

    /**
     * Record flashcard review result
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function review(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return;
        }

        $cardId = $this->input('card_id');
        $rating = $this->input('rating');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $review = $db->queryOne(
            'SELECT id, ease_factor, interval_days, review_count FROM flashcard_reviews
             WHERE flashcard_id = ? AND user_id = ?',
            [$cardId, $userId]
        );

        if ($review) {
            $quality = (int)$rating;
            $easyFactor = max(1.3, $review['ease_factor'] + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)));
            $interval = $review['review_count'] == 0 ? 1 : ($review['review_count'] == 1 ? 3 : round($review['interval_days'] * $easyFactor));

            $nextReview = date('Y-m-d H:i:s', strtotime("+$interval days"));

            $db->execute(
                'UPDATE flashcard_reviews SET
                    rating = ?, ease_factor = ?, interval_days = ?,
                    review_count = review_count + 1, reviewed_at = NOW(),
                    next_review_at = ?
                 WHERE flashcard_id = ? AND user_id = ?',
                [$quality, $easyFactor, $interval, $nextReview, $cardId, $userId]
            );
        } else {
            $db->insert(
                'INSERT INTO flashcard_reviews
                 (flashcard_id, user_id, rating, ease_factor, interval_days, review_count, reviewed_at, next_review_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY))',
                [$cardId, $userId, (int)$rating, 2.5, 1, 1]
            );
        }

        $response->json(['success' => true]);
    }
}
