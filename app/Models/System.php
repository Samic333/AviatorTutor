<?php
/**
 * System Model
 *
 * Represents Q400 aircraft systems (hydraulic, electrical, etc.)
 */

namespace App\Models;

use App\Core\Model;

class System extends Model
{
    protected string $table = 'systems';
    protected array $fillable = ['name', 'slug', 'description', 'icon', 'color', 'order', 'published', 'created_at', 'updated_at'];

    /**
     * Find system by slug
     *
     * @param string $slug System slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        $result = self::db()->queryOne(
            "SELECT * FROM {$this->table} WHERE slug = ?",
            [$slug]
        );

        return $result ?: null;
    }

    /**
     * Get all published systems
     *
     * @return array
     */
    public function getAllPublished(): array
    {
        return self::db()->query(
            "SELECT * FROM {$this->table} WHERE published = 1 ORDER BY `order` ASC"
        );
    }

    /**
     * Get systems with user progress percentage
     *
     * @param int $userId User ID
     * @return array
     */
    public function getWithProgress(int $userId): array
    {
        $systems = self::db()->query(
            "SELECT s.*,
                    COUNT(DISTINCT l.id) as total_lessons,
                    COUNT(DISTINCT CASE WHEN up.completed = 1 THEN l.id END) as completed_lessons,
                    COUNT(DISTINCT f.id) as total_flashcards,
                    COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.ease_factor > 0 THEN f.id END) as mastered_flashcards
             FROM {$this->table} s
             LEFT JOIN lessons l ON s.id = l.system_id
             LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ? AND up.completed = 1
             LEFT JOIN flashcards f ON s.id = f.system_id
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             WHERE s.published = 1
             GROUP BY s.id
             ORDER BY s.`order` ASC",
            [$userId, $userId]
        );

        foreach ($systems as &$system) {
            $total_lessons = (int)($system['total_lessons'] ?? 0);
            $completed_lessons = (int)($system['completed_lessons'] ?? 0);
            $system['progress'] = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;
        }

        return $systems;
    }

    /**
     * Get system progress for user
     *
     * @param int $userId User ID
     * @param int $systemId System ID
     * @return array
     */
    public function getSystemProgress(int $userId, int $systemId): array
    {
        $progress = self::db()->queryOne(
            "SELECT s.*,
                    COUNT(DISTINCT l.id) as total_lessons,
                    COUNT(DISTINCT CASE WHEN up.completed = 1 THEN l.id END) as completed_lessons,
                    COUNT(DISTINCT f.id) as total_flashcards,
                    COUNT(DISTINCT CASE WHEN fr.id IS NOT NULL AND fr.ease_factor > 0 THEN f.id END) as mastered_flashcards,
                    COUNT(DISTINCT q.id) as total_quizzes
             FROM {$this->table} s
             LEFT JOIN lessons l ON s.id = l.system_id
             LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ? AND up.completed = 1
             LEFT JOIN flashcards f ON s.id = f.system_id
             LEFT JOIN flashcard_reviews fr ON f.id = fr.flashcard_id AND fr.user_id = ?
             LEFT JOIN quizzes q ON s.id = q.system_id AND q.published = 1
             WHERE s.id = ?
             LIMIT 1",
            [$userId, $userId, $systemId]
        );

        if (!$progress) {
            return [];
        }

        $total_lessons = (int)($progress['total_lessons'] ?? 0);
        $completed_lessons = (int)($progress['completed_lessons'] ?? 0);
        $progress['progress'] = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

        return $progress;
    }
}
