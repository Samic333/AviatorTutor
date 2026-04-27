<?php
/**
 * Flashcard Model
 *
 * Manages flashcards with SM-2 spaced repetition algorithm
 */

namespace App\Models;

use App\Core\Model;

class Flashcard extends Model
{
    protected string $table = 'flashcards';
    protected array $fillable = ['system_id', 'front', 'back', 'difficulty', 'published', 'created_at', 'updated_at'];

    /**
     * Get flashcards by system
     *
     * @param int $systemId System ID
     * @return array
     */
    public function getBySystem(int $systemId): array
    {
        return self::db()->query(
            "SELECT * FROM {$this->table} WHERE system_id = ? AND published = 1 ORDER BY id ASC",
            [$systemId]
        );
    }

    /**
     * Get due flashcards for user using SM-2 algorithm
     *
     * Due cards are those:
     * - Never reviewed (no entry in flashcard_reviews)
     * - Next review date is today or earlier
     *
     * @param int $userId User ID
     * @param int $limit Max cards to return
     * @return array
     */
    public function getDueCards(int $userId, int $limit = 20): array
    {
        return self::db()->query(
            "SELECT f.*, fr.id as review_id, fr.ease_factor, fr.interval, fr.repetitions, fr.next_review_at
             FROM {$this->table} f
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE f.published = 1 AND (
                fr.id IS NULL
                OR fr.next_review_at <= NOW()
             )
             ORDER BY
                CASE WHEN fr.id IS NULL THEN 0 ELSE 1 END,
                fr.next_review_at ASC,
                f.id ASC
             LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Get review data for a flashcard
     *
     * @param int $userId User ID
     * @param int $flashcardId Flashcard ID
     * @return array|null
     */
    public function getReviewData(int $userId, int $flashcardId): ?array
    {
        return self::db()->queryOne(
            "SELECT fr.* FROM flashcard_reviews fr
             WHERE fr.user_id = ? AND fr.flashcard_id = ?
             LIMIT 1",
            [$userId, $flashcardId]
        );
    }

    /**
     * Save review using SM-2 algorithm
     *
     * SM-2 Algorithm:
     * - Rating scale: 1 (Again), 2 (Hard), 3 (Good), 4 (Easy)
     * - EF (Ease Factor) adjusts based on performance
     * - Interval increases with successful reviews
     *
     * @param int $userId User ID
     * @param int $flashcardId Flashcard ID
     * @param int $rating Rating (1-4)
     * @return array Next review date info
     */
    public function saveReview(int $userId, int $flashcardId, int $rating): array
    {
        // Validate rating
        if ($rating < 1 || $rating > 4) {
            $rating = 3;
        }

        // Get existing review data
        $review = $this->getReviewData($userId, $flashcardId);

        // Initialize SM-2 values
        $ease = $review['ease_factor'] ?? 2.5;
        $interval = $review['interval'] ?? 0;
        $repetitions = $review['repetitions'] ?? 0;

        // SM-2 calculation
        if ($review === null) {
            // First review
            if ($rating >= 3) {
                $interval = 1;
                $repetitions = 1;
                $ease = 2.5;
            } else {
                $interval = 0;
                $repetitions = 0;
                $ease = 2.5;
            }
        } else {
            // Subsequent reviews
            $repetitions++;

            // Adjust ease factor
            $ease = max(1.3, $ease + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02)));

            if ($rating >= 3) {
                // Correct answer - increase interval
                if ($repetitions === 1) {
                    $interval = 1;
                } elseif ($repetitions === 2) {
                    $interval = 3;
                } else {
                    $interval = ceil($interval * $ease);
                }
            } else {
                // Incorrect answer - reset
                $interval = 1;
                $repetitions = 1;
            }
        }

        // Calculate next review date
        $nextReview = date('Y-m-d H:i:s', strtotime("+{$interval} days"));

        // Save/update review
        if ($review === null) {
            self::db()->insert(
                "INSERT INTO flashcard_reviews (user_id, flashcard_id, ease_factor, interval, repetitions, next_review_at, last_reviewed_at, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [$userId, $flashcardId, $ease, $interval, $repetitions, $nextReview]
            );
        } else {
            self::db()->execute(
                "UPDATE flashcard_reviews SET ease_factor = ?, interval = ?, repetitions = ?, next_review_at = ?, last_reviewed_at = NOW()
                 WHERE user_id = ? AND flashcard_id = ?",
                [$ease, $interval, $repetitions, $nextReview, $userId, $flashcardId]
            );
        }

        return [
            'ease_factor' => $ease,
            'interval' => $interval,
            'repetitions' => $repetitions,
            'next_review_at' => $nextReview,
            'rating' => $rating,
        ];
    }

    /**
     * Get flashcard statistics for user
     *
     * @param int $userId User ID
     * @return array
     */
    public function getStats(int $userId): array
    {
        $result = self::db()->queryOne(
            "SELECT
                COUNT(DISTINCT f.id) as total,
                COUNT(DISTINCT CASE WHEN fr.id IS NULL THEN f.id END) as new,
                COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.next_review_at <= NOW() THEN f.id END) as due,
                COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.ease_factor >= 2.5 THEN f.id END) as mastered
             FROM {$this->table} f
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE f.published = 1",
            [$userId]
        );

        return [
            'total' => (int)($result['total'] ?? 0),
            'new' => (int)($result['new'] ?? 0),
            'due' => (int)($result['due'] ?? 0),
            'mastered' => (int)($result['mastered'] ?? 0),
        ];
    }

    /**
     * Get flashcard statistics per system
     *
     * @param int $userId User ID
     * @param int $systemId System ID
     * @return array
     */
    public function getSystemStats(int $userId, int $systemId): array
    {
        $result = self::db()->queryOne(
            "SELECT
                COUNT(DISTINCT f.id) as total,
                COUNT(DISTINCT CASE WHEN fr.id IS NULL THEN f.id END) as new,
                COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.next_review_at <= NOW() THEN f.id END) as due,
                COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.ease_factor >= 2.5 THEN f.id END) as mastered
             FROM {$this->table} f
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE f.system_id = ? AND f.published = 1",
            [$userId, $systemId]
        );

        return [
            'total' => (int)($result['total'] ?? 0),
            'new' => (int)($result['new'] ?? 0),
            'due' => (int)($result['due'] ?? 0),
            'mastered' => (int)($result['mastered'] ?? 0),
        ];
    }
}
