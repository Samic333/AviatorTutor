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
        $moduleId = (int) $this->input('module_id');
        $progress = (int) $this->input('progress');
        $db = \App\Core\DB::instance();

        if (!$moduleId) {
            $response->status(422);
            $response->json(['error' => 'module_id required']);
            return;
        }

        $progress = max(0, min(100, $progress));
        if ($progress === 0) {
            $status = 'not_started';
        } elseif ($progress < 100) {
            $status = 'in_progress';
        } else {
            $status = 'completed';
        }
        $confidence = (int) round($progress / 20);

        $db->execute(
            'INSERT INTO user_progress (user_id, system_id, lesson_id, status, confidence, last_studied)
             VALUES (?, ?, NULL, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE status = ?, confidence = ?, last_studied = NOW()',
            [$userId, $moduleId, $status, $confidence, $status, $confidence]
        );

        $response->json([
            'success' => true,
            'user_id' => $userId,
            'module_id' => $moduleId,
            'progress' => $progress,
            'status' => $status,
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
        $cardId = (int) $this->input('card_id');
        $correct = $this->input('correct') === 'true';
        $db = \App\Core\DB::instance();

        if (!$cardId) {
            $response->status(422);
            $response->json(['error' => 'card_id required']);
            return;
        }

        $quality = $correct ? 5 : 1;

        $review = $db->queryOne(
            'SELECT id, ease_factor, interval_days, review_count FROM flashcard_reviews
             WHERE flashcard_id = ? AND user_id = ?',
            [$cardId, $userId]
        );

        if ($review) {
            $easeFactor = max(1.3, $review['ease_factor'] + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)));
            $interval = $review['review_count'] == 0 ? 1 : ($review['review_count'] == 1 ? 3 : (int) round($review['interval_days'] * $easeFactor));
            $nextReview = date('Y-m-d H:i:s', strtotime("+$interval days"));

            $db->execute(
                'UPDATE flashcard_reviews SET
                    rating = ?, ease_factor = ?, interval_days = ?,
                    review_count = review_count + 1, reviewed_at = NOW(),
                    next_review_at = ?
                 WHERE flashcard_id = ? AND user_id = ?',
                [$quality, $easeFactor, $interval, $nextReview, $cardId, $userId]
            );
        } else {
            $db->insert(
                'INSERT INTO flashcard_reviews
                 (flashcard_id, user_id, rating, ease_factor, interval_days, review_count, reviewed_at, next_review_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY))',
                [$cardId, $userId, $quality, 2.5, 1, 1]
            );
        }

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

        $query = trim($this->query('q', ''));
        $type = $this->query('type', 'all'); // all, systems, flashcards, quizzes

        if (strlen($query) < 2) {
            $response->json(['results' => [], 'query' => $query, 'total' => 0]);
            return;
        }

        $db = \App\Core\DB::instance();
        $searchTerm = "%{$query}%";
        $results = [];

        if ($type === 'all' || $type === 'systems') {
            $systems = $db->query(
                'SELECT id, name, description, ata_code FROM systems
                 WHERE (name LIKE ? OR description LIKE ? OR ata_code LIKE ?) AND is_published = 1
                 LIMIT 5',
                [$searchTerm, $searchTerm, $searchTerm]
            );
            foreach ($systems as $s) {
                $results[] = [
                    'type'        => 'system',
                    'id'          => $s['id'],
                    'title'       => $s['name'],
                    'system_name' => $s['name'],
                    'excerpt'     => substr(strip_tags($s['description'] ?? ''), 0, 150),
                    'url'         => '/systems/' . $s['id'],
                ];
            }
        }

        if ($type === 'all' || $type === 'lessons') {
            $lessons = $db->query(
                'SELECT l.id, l.title, l.body, s.name as system_name, s.id as system_id
                 FROM lessons l
                 JOIN systems s ON l.system_id = s.id
                 WHERE (l.title LIKE ? OR l.body LIKE ?) AND l.is_published = 1
                 LIMIT 10',
                [$searchTerm, $searchTerm]
            );
            foreach ($lessons as $l) {
                $results[] = [
                    'type'        => 'lesson',
                    'id'          => $l['id'],
                    'title'       => $l['title'],
                    'system_name' => $l['system_name'],
                    'excerpt'     => substr(strip_tags($l['body'] ?? ''), 0, 150),
                    'url'         => '/systems/' . $l['system_id'] . '#lesson-' . $l['id'],
                ];
            }
        }

        if ($type === 'all' || $type === 'flashcards') {
            $flashcards = $db->query(
                'SELECT f.id, f.front, f.back, s.name as system_name, s.id as system_id
                 FROM flashcards f
                 JOIN systems s ON f.system_id = s.id
                 WHERE (f.front LIKE ? OR f.back LIKE ?)
                 LIMIT 5',
                [$searchTerm, $searchTerm]
            );
            foreach ($flashcards as $f) {
                $results[] = [
                    'type'        => 'flashcard',
                    'id'          => $f['id'],
                    'title'       => $f['front'],
                    'system_name' => $f['system_name'],
                    'excerpt'     => substr(strip_tags($f['back'] ?? ''), 0, 150),
                    'url'         => '/flashcards/' . $f['system_id'],
                ];
            }
        }

        if ($type === 'quizzes') {
            $quizzes = $db->query(
                'SELECT q.id, q.title, q.description, s.name as system_name
                 FROM quizzes q
                 LEFT JOIN systems s ON q.system_id = s.id
                 WHERE q.title LIKE ? OR q.description LIKE ?
                 LIMIT 5',
                [$searchTerm, $searchTerm]
            );
            foreach ($quizzes as $q) {
                $results[] = [
                    'type'        => 'quiz',
                    'id'          => $q['id'],
                    'title'       => $q['title'],
                    'system_name' => $q['system_name'] ?? '',
                    'excerpt'     => substr(strip_tags($q['description'] ?? ''), 0, 150),
                    'url'         => '/quiz/' . $q['id'],
                ];
            }
        }

        $response->json(['results' => $results, 'query' => $query, 'total' => count($results)]);
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

        $MAX_ATTEMPTS = 3; // mirror slideAnswer

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

        // SECURITY GATE: every gated slide in this lesson must be settled —
        // either answered correctly OR exhausted to MAX_ATTEMPTS. Without
        // this check a learner could POST /complete directly and skip every
        // question gate.
        $unsettled = $db->queryOne(
            'SELECT COUNT(*) AS n
             FROM lesson_slides s
             LEFT JOIN user_slide_progress p
                 ON p.slide_id = s.id AND p.user_id = ?
             WHERE s.lesson_id = ?
               AND s.question IS NOT NULL
               AND JSON_EXTRACT(s.question, "$.correct_index") IS NOT NULL
               AND COALESCE(p.answered_correct, 0) = 0
               AND COALESCE(p.attempts, 0) < ?',
            [$userId, $lessonId, $MAX_ATTEMPTS]
        );

        if ((int) ($unsettled['n'] ?? 0) > 0) {
            $response->status(403);
            $response->json([
                'error' => 'Some question gates are still unanswered. Answer them (or use up your retries) before marking the lesson complete.',
                'unsettled_gate_count' => (int) $unsettled['n'],
            ]);
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
     * Record a learner's answer to a slide question gate.
     *
     * SECURITY: Correctness is computed SERVER-SIDE from the slide's stored
     * question JSON — we never trust a client-supplied `is_correct` flag.
     * After MAX_ATTEMPTS wrong tries the gate auto-unlocks (with the
     * explanation revealed) so the learner can keep moving; the failure is
     * still recorded for revision tracking.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function slideAnswer(Request $request, Response $response): void
    {
        $this->requireAuth();

        $MAX_ATTEMPTS  = 3; // after this many wrong tries the gate releases

        $userId        = $this->user()['id'];
        $lessonId      = (int) $this->param('id');
        $slideId       = (int) $this->input('slide_id');
        $selectedRaw   = $this->input('selected_index', null);
        $selectedIndex = ($selectedRaw === null || $selectedRaw === '') ? null : (int) $selectedRaw;
        $db            = \App\Core\DB::instance();

        if (!$slideId || $selectedIndex === null || $selectedIndex < 0) {
            $response->status(422);
            $response->json(['error' => 'slide_id and selected_index required']);
            return;
        }

        // Pull the slide's question JSON from the DB (single source of truth)
        $slide = $db->queryOne(
            'SELECT id, lesson_id, question
             FROM lesson_slides
             WHERE id = ? AND lesson_id = ?',
            [$slideId, $lessonId]
        );

        if (!$slide) {
            $response->status(404);
            $response->json(['error' => 'Slide not found']);
            return;
        }

        if (empty($slide['question'])) {
            $response->status(422);
            $response->json(['error' => 'Slide has no question gate']);
            return;
        }

        $question = is_string($slide['question'])
            ? json_decode($slide['question'], true)
            : $slide['question'];

        if (!is_array($question) || !isset($question['correct_index'])) {
            $response->status(500);
            $response->json(['error' => 'Slide question is malformed']);
            return;
        }

        $correctIndex = (int) $question['correct_index'];
        $isCorrect    = ($selectedIndex === $correctIndex) ? 1 : 0;
        $explanation  = isset($question['explanation']) ? (string) $question['explanation'] : '';

        // Upsert progress: increment attempts, OR-in answered_correct (so the
        // first correct answer sticks even if the user later picks wrong).
        $db->execute(
            'INSERT INTO user_slide_progress
                 (user_id, lesson_id, slide_id, answered_correct, attempts, viewed_at)
             VALUES (?, ?, ?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE
                 answered_correct = GREATEST(answered_correct, VALUES(answered_correct)),
                 attempts = attempts + 1,
                 viewed_at = NOW()',
            [$userId, $lessonId, $slideId, $isCorrect]
        );

        // Read back the post-update attempt count so the client knows where
        // it is in the retry budget.
        $row = $db->queryOne(
            'SELECT answered_correct, attempts
             FROM user_slide_progress
             WHERE user_id = ? AND slide_id = ?',
            [$userId, $slideId]
        );
        $attempts        = (int) ($row['attempts'] ?? 1);
        $alreadyCorrect  = (int) ($row['answered_correct'] ?? 0) === 1;
        $canProceed      = $alreadyCorrect || $attempts >= $MAX_ATTEMPTS;

        $response->json([
            'success'         => true,
            'is_correct'      => (bool) $isCorrect,
            'attempts'        => $attempts,
            'attempts_left'   => max(0, $MAX_ATTEMPTS - $attempts),
            'max_attempts'    => $MAX_ATTEMPTS,
            'can_proceed'     => (bool) $canProceed,
            // Reveal the explanation only when the gate is actually settled —
            // either correct, or burned through the retry budget.
            'explanation'     => $canProceed ? $explanation : '',
            'unlocked_after_failure' => !$alreadyCorrect && $canProceed,
        ]);
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

    /**
     * Delete a note belonging to the current user.
     * Works for both JSON (XHR) and form-POST (with redirect back to /notes).
     */
    public function deleteNote(Request $request, Response $response): void
    {
        $this->requireAuth();

        if (!\App\Core\CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return;
        }

        $userId = (int) ($this->user()['id'] ?? 0);
        $noteId = (int) $this->input('note_id', 0);
        if ($noteId <= 0) {
            $response->status(422);
            $response->json(['error' => 'note_id required']);
            return;
        }

        $rows = \App\Core\DB::instance()->execute(
            'DELETE FROM notes WHERE id = ? AND user_id = ?',
            [$noteId, $userId]
        );

        // If posted from a form, redirect back; otherwise return JSON.
        $accept = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');
        if (strpos($accept, 'application/json') === false) {
            $_SESSION['flash_ok'] = $rows > 0 ? 'Note deleted.' : 'Note not found.';
            $this->redirect('/notes');
            return;
        }
        $response->json(['success' => $rows > 0]);
    }

    /**
     * AI Q&A placeholder — returns 501 until an LLM provider is wired in.
     * The frontend study panel can call this and gracefully degrade.
     */
    public function aiAsk(Request $request, Response $response): void
    {
        $this->requireAuth();
        $response->status(501);
        $response->json([
            'error'   => 'not_implemented',
            'message' => 'AI Q&A is launching soon. Stay tuned!',
        ]);
    }
}
