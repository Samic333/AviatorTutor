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
        $this->requireAuth();

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
             JOIN systems s ON f.system_id = s.id WHERE s.is_published = 1'
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

        $html = $this->view('flashcards/index', $data);
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
        $this->requireAuth();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $system = $db->queryOne(
            'SELECT id, name, ata_code FROM systems WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$system) {
            $response->status(404);
            $response->html('<h1>System Not Found</h1>');
            return;
        }

        $flashcards = $db->query(
            'SELECT f.id, f.front, f.back, f.difficulty, fr.next_review_at, fr.ease_factor
             FROM flashcards f
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE f.system_id = ?
             ORDER BY COALESCE(fr.next_review_at, NOW()) ASC',
            [$userId, $id]
        );

        $data = [
            'title' => 'Flashcards - ' . htmlspecialchars($system['name']),
            'system' => $system,
            'flashcards' => $flashcards,
            'csrf_token' => CSRF::generate(),
        ];

        $html = $this->view('flashcards/study', $data);
        $response->html($html);
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
        $this->requireAuth();

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
