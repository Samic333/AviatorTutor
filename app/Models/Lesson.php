<?php
/**
 * Lesson Model
 *
 * Represents study lessons for each system
 */

namespace App\Models;

use App\Core\Model;

class Lesson extends Model
{
    protected string $table = 'lessons';
    protected array $fillable = ['system_id', 'title', 'slug', 'content', 'order', 'published', 'created_at', 'updated_at'];

    /**
     * Get lessons by system
     *
     * @param int $systemId System ID
     * @return array
     */
    public function getBySystem(int $systemId): array
    {
        return self::db()->query(
            "SELECT * FROM {$this->table} WHERE system_id = ? AND published = 1 ORDER BY `order` ASC",
            [$systemId]
        );
    }

    /**
     * Get lesson by system slug and lesson slug
     *
     * @param string $systemSlug System slug
     * @param string $lessonSlug Lesson slug
     * @return array|null
     */
    public function getBySlug(string $systemSlug, string $lessonSlug): ?array
    {
        $result = self::db()->queryOne(
            "SELECT l.* FROM {$this->table} l
             JOIN systems s ON l.system_id = s.id
             WHERE s.slug = ? AND l.slug = ? AND l.published = 1
             LIMIT 1",
            [$systemSlug, $lessonSlug]
        );

        return $result ?: null;
    }

    /**
     * Get sections for a lesson
     *
     * @param int $lessonId Lesson ID
     * @return array
     */
    public function getSections(int $lessonId): array
    {
        return self::db()->query(
            "SELECT * FROM lesson_sections WHERE lesson_id = ? ORDER BY `order` ASC",
            [$lessonId]
        );
    }

    /**
     * Get lesson with all sections
     *
     * @param int $lessonId Lesson ID
     * @return array
     */
    public function getWithSections(int $lessonId): array
    {
        $lesson = self::find($lessonId);

        if (!$lesson) {
            return [];
        }

        $sections = $this->getSections($lessonId);

        return array_merge(
            $lesson->toArray(),
            ['sections' => $sections]
        );
    }

    /**
     * Mark lesson as complete for user
     *
     * @param int $userId User ID
     * @param int $lessonId Lesson ID
     * @return void
     */
    public function markComplete(int $userId, int $lessonId): void
    {
        $lesson = self::find($lessonId);
        if (!$lesson) {
            return;
        }

        // Check if progress record exists
        $existing = self::db()->queryOne(
            "SELECT id FROM user_progress WHERE user_id = ? AND lesson_id = ?",
            [$userId, $lessonId]
        );

        if ($existing) {
            self::db()->execute(
                "UPDATE user_progress SET completed = 1, completed_at = NOW() WHERE user_id = ? AND lesson_id = ?",
                [$userId, $lessonId]
            );
        } else {
            self::db()->insert(
                "INSERT INTO user_progress (user_id, lesson_id, system_id, completed, completed_at) VALUES (?, ?, ?, 1, NOW())",
                [$userId, $lessonId, $lesson->system_id]
            );
        }
    }

    /**
     * Get user progress for lesson
     *
     * @param int $userId User ID
     * @param int $lessonId Lesson ID
     * @return array|null
     */
    public function getUserProgress(int $userId, int $lessonId): ?array
    {
        return self::db()->queryOne(
            "SELECT * FROM user_progress WHERE user_id = ? AND lesson_id = ? LIMIT 1",
            [$userId, $lessonId]
        );
    }
}
