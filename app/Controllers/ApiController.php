<?php
/**
 * API Controller
 *
 * Handles API endpoints for AJAX requests
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;

class ApiController extends Controller
{
    /**
     * Update user progress
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function updateProgress(Request $request, Response $response): void
    {
        $this->requireAuth();

        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return;
        }

        $userId = $this->user()['id'];
        $moduleId = $this->input('module_id');
        $progress = $this->input('progress');

        // TODO: Update progress in database

        $response->json([
            'success' => true,
            'user_id' => $userId,
            'module_id' => $moduleId,
            'progress' => $progress,
        ]);
    }

    /**
     * Record flashcard review
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function flashcardReview(Request $request, Response $response): void
    {
        $this->requireAuth();

        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return;
        }

        $userId = $this->user()['id'];
        $cardId = $this->input('card_id');
        $correct = $this->input('correct') === 'true';
        $elapsedTime = $this->input('elapsed_time');

        // TODO: Save review in database

        $response->json([
            'success' => true,
            'card_id' => $cardId,
            'correct' => $correct,
        ]);
    }

    /**
     * Search API
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function search(Request $request, Response $response): void
    {
        $this->requireAuth();

        $query = $this->query('q', '');
        $type = $this->query('type', 'all'); // all, systems, flashcards, quizzes

        if (strlen($query) < 2) {
            $response->json(['results' => []]);
            return;
        }

        // TODO: Search database
        $results = [];

        $response->json(['results' => $results, 'query' => $query]);
    }

    /**
     * Mark a lesson as complete
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function lessonComplete(Request $request, Response $response): void
    {
        $this->requireAuth();

        $userId = $this->user()['id'];
        $lessonId = $this->param('id');
        $db = \App\Core\DB::instance();

        // Get lesson's system_id
        $lesson = $db->queryOne(
            'SELECT id, system_id FROM lessons WHERE id = ? AND is_published = 1',
            [$lessonId]
        );

        if (!$lesson) {
            $response->status(404);
            $response->json(['error' => 'Lesson not found']);
            return;
        }

        // Upsert progress record
        $db->execute(
            'INSERT INTO user_progress (user_id, system_id, lesson_id, status, last_studied, confidence)
             VALUES (?, ?, ?, "completed", NOW(), 5)
             ON DUPLICATE KEY UPDATE status = "completed", last_studied = NOW()',
            [$userId, $lesson['system_id'], $lessonId]
        );

        $response->json(['success' => true, 'lesson_id' => $lessonId]);
    }

    /**
     * Save user notes for a system
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function saveNotes(Request $request, Response $response): void
    {
        $this->requireAuth();

        $userId = $this->user()['id'];
        $systemId = $this->input('system_id');
        $content = $this->input('content', '');
        $db = \App\Core\DB::instance();

        if (!$systemId) {
            $response->status(422);
            $response->json(['error' => 'system_id required']);
            return;
        }

        // Update existing note or insert new one
        $existing = $db->queryOne(
            'SELECT id FROM notes WHERE user_id = ? AND system_id = ? LIMIT 1',
            [$userId, $systemId]
        );

        if ($existing) {
            $db->execute(
                'UPDATE notes SET content = ?, updated_at = NOW() WHERE id = ?',
                [$content, $existing['id']]
            );
        } else {
            $db->insert(
                'INSERT INTO notes (user_id, system_id, content) VALUES (?, ?, ?)',
                [$userId, $systemId, $content]
            );
        }

        $response->json(['success' => true]);
    }

    /**
     * Mark all lessons in a system as complete
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function systemComplete(Request $request, Response $response): void
    {
        $this->requireAuth();

        $userId = $this->user()['id'];
        $systemId = $this->param('id');
        $db = \App\Core\DB::instance();

        // Get all published lessons for this system
        $lessons = $db->query(
            'SELECT id FROM lessons WHERE system_id = ? AND is_published = 1',
            [$systemId]
        );

        foreach ($lessons as $lesson) {
            $db->execute(
                'INSERT INTO user_progress (user_id, system_id, lesson_id, status, last_studied, confidence)
                 VALUES (?, ?, ?, "completed", NOW(), 5)
                 ON DUPLICATE KEY UPDATE status = "completed", last_studied = NOW()',
                [$userId, $systemId, $lesson['id']]
            );
        }

        $response->json(['success' => true, 'system_id' => $systemId, 'lessons_completed' => count($lessons)]);
    }
}
