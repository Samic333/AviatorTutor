<?php
/**
 * Quiz Controller
 *
 * Manages quiz creation and quiz-taking functionality
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;

class QuizController extends Controller
{
    /**
     * List available quizzes
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

        $quizzes = $db->query(
            'SELECT q.id, q.title, q.description, q.quiz_type, q.time_limit_mins, q.pass_score,
                    s.name as system_name, s.id as system_id,
                    COUNT(DISTINCT qa.id) as attempt_count,
                    AVG(qa.score) as avg_score,
                    (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.id) as question_count
             FROM quizzes q
             LEFT JOIN systems s ON q.system_id = s.id
             LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id AND qa.user_id = ?
             WHERE q.is_published = 1
             GROUP BY q.id, q.title, q.description, q.quiz_type, q.time_limit_mins, q.pass_score, s.name, s.id
             ORDER BY q.created_at DESC',
            [$userId]
        );

        $data = [
            'title' => 'Quizzes',
            'quizzes' => $quizzes,
        ];

        $html = $this->view('quiz/index', $data, 'pilot');
        $response->html($html);
    }

    /**
     * Take a quiz
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function take(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $id = $this->param('id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        $quiz = $db->queryOne(
            'SELECT id, title, description, time_limit_mins, pass_score, system_id
             FROM quizzes WHERE id = ? AND is_published = 1',
            [$id]
        );

        if (!$quiz) {
            $this->renderNotFound('Quiz Not Found', 'That quiz isn’t available — it may have been removed or unpublished.', '/quiz', 'Back to quizzes');
            return;
        }

        $questions = $db->query(
            'SELECT id, question_text, question_type, options, difficulty, sort_order
             FROM quiz_questions WHERE quiz_id = ? ORDER BY sort_order',
            [$id]
        );

        $attempt = $db->insert(
            'INSERT INTO quiz_attempts (quiz_id, user_id, status, started_at)
             VALUES (?, ?, "in_progress", NOW())',
            [$id, $userId]
        );

        $data = [
            'title' => htmlspecialchars($quiz['title']),
            'quiz' => $quiz,
            'questions' => $questions,
            'attempt_id' => $attempt,
            'csrf_token' => CSRF::generate(),
        ];

        $html = $this->view('quiz/take', $data, 'pilot');
        $response->html($html);
    }

    /**
     * Submit quiz answers
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function submit(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        if (!CSRF::check($request)) {
            $response->status(419);
            $response->html('<h1>Invalid CSRF token</h1>');
            return;
        }

        $quizId = $this->param('id');
        $attemptId = $this->input('attempt_id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        // Verify attempt belongs to this user
        $attempt = $db->queryOne(
            'SELECT id FROM quiz_attempts WHERE id = ? AND user_id = ?',
            [$attemptId, $userId]
        );

        if (!$attempt) {
            $response->status(403);
            $response->html('<h1>Attempt not found</h1>');
            return;
        }

        // Parse answers from JSON submission
        $answersJson = $this->input('answers_json', '[]');
        $answersArray = json_decode($answersJson, true) ?? [];

        $correctCount = 0;
        $totalQuestions = count($answersArray);

        foreach ($answersArray as $entry) {
            $questionId = $entry['question_id'] ?? null;
            $answer = $entry['answer'] ?? null;

            if (!$questionId) continue;

            $question = $db->queryOne(
                'SELECT correct_answer FROM quiz_questions WHERE id = ?',
                [$questionId]
            );

            $isCorrect = false;
            if ($question && $answer !== null) {
                $correctAnswer = json_decode($question['correct_answer'], true);
                $isCorrect = json_encode($correctAnswer) === json_encode($answer);
            }

            $db->insert(
                'INSERT INTO quiz_answers (attempt_id, question_id, user_answer, is_correct, time_taken_secs)
                 VALUES (?, ?, ?, ?, ?)',
                [$attemptId, $questionId, json_encode($answer), $isCorrect ? 1 : 0, 0]
            );

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $db->execute(
            'UPDATE quiz_attempts SET status = "completed", score = ?,
             completed_at = NOW(),
             time_taken_secs = TIMESTAMPDIFF(SECOND, started_at, NOW())
             WHERE id = ?',
            [$score, $attemptId]
        );

        // Redirect to result page
        header("Location: /quiz/{$quizId}/result/{$attemptId}");
        exit;
    }

    /**
     * Display quiz result
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function result(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $quizId = $this->param('id');
        $attemptId = $this->param('attempt_id');
        $userId = $this->user()['id'];
        $db = DB::instance();

        // Get quiz
        $quiz = $db->queryOne(
            'SELECT q.*, s.name as system_name FROM quizzes q LEFT JOIN systems s ON q.system_id = s.id WHERE q.id = ?',
            [$quizId]
        );

        if (!$quiz) {
            $this->renderNotFound('Quiz Result Not Found', 'We couldn’t find that quiz attempt — it may have been removed.', '/quiz', 'Back to quizzes');
            return;
        }

        // Get attempt
        $attempt = $db->queryOne(
            'SELECT * FROM quiz_attempts WHERE id = ? AND user_id = ?',
            [$attemptId, $userId]
        );

        if (!$attempt) {
            $response->status(404);
            $response->html('<h1>Attempt not found</h1>');
            return;
        }

        // Get answers with question details
        $answers = $db->query(
            'SELECT qa.*, qq.question_text, qq.correct_answer, qq.explanation, qq.options
             FROM quiz_answers qa
             JOIN quiz_questions qq ON qa.question_id = qq.id
             WHERE qa.attempt_id = ?
             ORDER BY qq.sort_order',
            [$attemptId]
        );

        $passed = ($attempt['score'] ?? 0) >= ($quiz['pass_score'] ?? 70);

        $data = [
            'title' => 'Quiz Result',
            'quiz' => $quiz,
            'attempt' => $attempt,
            'answers' => $answers,
            'passed' => $passed,
        ];

        $html = $this->view('quiz/result', $data, 'pilot');
        $response->html($html);
    }
}
