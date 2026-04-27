<?php
/**
 * User Model
 *
 * Manages user accounts, authentication, and progress tracking
 */

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password_hash', 'role', 'created_at', 'updated_at'];
    protected array $hidden = ['password_hash'];

    /**
     * Find user by email
     *
     * @param string $email User email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $result = self::db()->queryOne(
            "SELECT * FROM {$this->table} WHERE email = ?",
            [$email]
        );

        return $result ?: null;
    }

    /**
     * Validate password against hash
     *
     * @param string $password Plain text password
     * @param string $hash Password hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Update user study streak
     *
     * @param int $userId User ID
     * @return void
     */
    public function updateStreak(int $userId): void
    {
        $user = self::find($userId);
        if (!$user) {
            return;
        }

        $today = date('Y-m-d');
        $lastStreakDate = $user->last_streak_date ?? null;

        // Check if streak is still active (today or yesterday)
        if ($lastStreakDate) {
            $last = new \DateTime($lastStreakDate);
            $now = new \DateTime($today);
            $diff = $now->diff($last)->days;

            if ($diff === 0) {
                // Already studied today
                return;
            } elseif ($diff === 1) {
                // Streak continues
                self::update($userId, [
                    'study_streak' => ($user->study_streak ?? 0) + 1,
                    'last_streak_date' => $today,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                return;
            }
        }

        // Streak broken or new
        self::update($userId, [
            'study_streak' => 1,
            'last_streak_date' => $today,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get user progress statistics
     *
     * @param int $userId User ID
     * @return array Progress stats
     */
    public function getProgress(int $userId): array
    {
        $systems = self::db()->query(
            "SELECT DISTINCT s.id, s.name, s.slug,
                    COUNT(DISTINCT l.id) as total_lessons,
                    COUNT(DISTINCT CASE WHEN lp.completed = 1 THEN l.id END) as completed_lessons,
                    COUNT(DISTINCT f.id) as total_flashcards,
                    COUNT(DISTINCT CASE WHEN fr.user_id = ? AND fr.ease_factor > 0 THEN f.id END) as mastered_flashcards
             FROM systems s
             LEFT JOIN lessons l ON s.id = l.system_id
             LEFT JOIN user_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
             LEFT JOIN flashcards f ON s.id = f.system_id
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             GROUP BY s.id, s.name, s.slug",
            [$userId, $userId, $userId]
        );

        $stats = [
            'total_systems' => count($systems),
            'completed_lessons' => 0,
            'total_lessons' => 0,
            'mastered_flashcards' => 0,
            'total_flashcards' => 0,
            'systems' => [],
        ];

        foreach ($systems as $system) {
            $stats['completed_lessons'] += $system['completed_lessons'] ?? 0;
            $stats['total_lessons'] += $system['total_lessons'] ?? 0;
            $stats['mastered_flashcards'] += $system['mastered_flashcards'] ?? 0;
            $stats['total_flashcards'] += $system['total_flashcards'] ?? 0;

            $completed = $system['total_lessons'] > 0
                ? round(($system['completed_lessons'] / $system['total_lessons']) * 100)
                : 0;

            $stats['systems'][] = [
                'id' => $system['id'],
                'name' => $system['name'],
                'slug' => $system['slug'],
                'completion' => $completed,
                'lessons_completed' => $system['completed_lessons'],
                'total_lessons' => $system['total_lessons'],
            ];
        }

        return $stats;
    }

    /**
     * Get count of due flashcards for user
     *
     * @param int $userId User ID
     * @return int
     */
    public function getDueFlashcards(int $userId): int
    {
        $result = self::db()->queryOne(
            "SELECT COUNT(DISTINCT f.id) as count
             FROM flashcards f
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE fr.id IS NULL OR fr.next_review_at <= NOW()",
            [$userId]
        );

        return $result['count'] ?? 0;
    }

    /**
     * Get user study streak
     *
     * @param int $userId User ID
     * @return int
     */
    public function getStudyStreak(int $userId): int
    {
        $user = self::find($userId);
        return $user ? ($user->study_streak ?? 0) : 0;
    }
}
