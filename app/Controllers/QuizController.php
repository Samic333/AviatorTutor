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

        // Phase 8 follow-up (bug 27): include aircraft + subject context so the
        // index page can offer aircraft / subject / system dropdowns + keyword
        // search at the top, instead of only the quiz_type filter chips.
        $quizzes = $db->query(
            'SELECT q.id, q.title, q.description, q.quiz_type, q.time_limit_mins, q.pass_score,
                    s.name as system_name, s.id as system_id,
                    sub.id as subject_id, sub.name as subject_name, sub.category as subject_category,
                    a.id as aircraft_id, a.name as aircraft_name,
                    COUNT(DISTINCT qa.id) as attempt_count,
                    AVG(qa.score) as avg_score,
                    (SELECT COUNT(*) FROM quiz_questions qq
                       WHERE qq.quiz_id = q.id
                         AND (qq.status IS NULL OR qq.status = "published")
                    ) as question_count
             FROM quizzes q
             LEFT JOIN systems s ON q.system_id = s.id
             LEFT JOIN subjects sub ON s.subject_id = sub.id
             LEFT JOIN aircrafts a ON sub.aircraft_id = a.id
             LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id AND qa.user_id = ?
             WHERE q.is_published = 1
             GROUP BY q.id, q.title, q.description, q.quiz_type, q.time_limit_mins, q.pass_score,
                      s.name, s.id, sub.id, sub.name, sub.category, a.id, a.name
             ORDER BY q.created_at DESC',
            [$userId]
        );

        // Distinct lists for the dropdowns. Empty arrays are fine — the view
        // hides a dropdown that has only the "All" option.
        $aircrafts = [];
        $subjects  = [];
        $systems   = [];
        foreach ($quizzes as $q) {
            if (!empty($q['aircraft_id']) && !isset($aircrafts[$q['aircraft_id']])) {
                $aircrafts[$q['aircraft_id']] = $q['aircraft_name'];
            }
            if (!empty($q['subject_id']) && !isset($subjects[$q['subject_id']])) {
                $subjects[$q['subject_id']] = $q['subject_name'];
            }
            if (!empty($q['system_id']) && !isset($systems[$q['system_id']])) {
                $systems[$q['system_id']] = $q['system_name'];
            }
        }
        asort($aircrafts); asort($subjects); asort($systems);

        $data = [
            'title' => 'Quizzes',
            'quizzes' => $quizzes,
            'aircrafts' => $aircrafts,
            'subjects'  => $subjects,
            'systems'   => $systems,
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

        // Phase 3 fix: record this quiz attempt as a study session so the
        // dashboard's Recent Activity + Study Activity heatmap reflect it.
        $this->recordStudySession((int) $userId, (int) ($quiz['system_id'] ?? 0) ?: null, 'quiz');

        $questions = $db->query(
            'SELECT id, question_text, question_type, options, difficulty, sort_order
             FROM quiz_questions
             WHERE quiz_id = ?
               AND (status IS NULL OR status = "published")
             ORDER BY sort_order',
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

        // Phase 13 — wire Quiz into the unified study chrome when the flag
        // is on. Falls back to the legacy pilot layout otherwise.
        $layout = 'pilot';
        $cfg    = require BASE_PATH . '/config/app.php';
        if (!empty($cfg['features']['study_chrome_v2']) && (int)($quiz['system_id'] ?? 0) > 0) {
            $sysView = $this->loadSystemForChrome((int) $quiz['system_id']);
            if ($sysView) {
                $data   = array_merge($data, $this->buildStudyChromeData($sysView, 'quiz'));
                $layout = 'study';
            }
        }

        $html = $this->view('quiz/take', $data, $layout);
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

        // Parse answers from JSON submission. Shape:
        //   [{ question_id: 1, answer: "28 VDC" }, ...]
        // The frontend (views/quiz/take.php) currently sends a SINGLE STRING
        // per question (it only renders radio inputs). The DB stores
        // correct_answer as a JSON-encoded array, e.g. '["28 VDC"]'.
        //
        // Phase 8 bug (the "score is always 0" report): the previous
        // implementation compared json_encode($correctAnswer) === json_encode($answer),
        // which is '["28 VDC"]' vs '"28 VDC"' — never equal, so every
        // attempt scored 0. Below we normalise both sides into a canonical
        // sorted list of lowercased trimmed strings, so single-value and
        // multi-select questions both compare correctly.
        $answersJson  = $this->input('answers_json', '[]');
        $answersArray = json_decode($answersJson, true) ?? [];

        // Normaliser: array|scalar|null → sorted array of canonical strings.
        $normalise = static function ($v): array {
            if ($v === null) return [];
            if (is_string($v)) {
                // Some legacy rows store correct_answer as a bare string
                // ("28 VDC") not a JSON array. json_decode of a bare string
                // returns null (invalid JSON); we just use the string itself.
                $v = [$v];
            } elseif (is_scalar($v)) {
                $v = [(string) $v];
            } elseif (!is_array($v)) {
                $v = [(string) $v];
            }
            $v = array_map(static fn($x) => mb_strtolower(trim((string) $x)), $v);
            $v = array_values(array_filter($v, static fn($x) => $x !== ''));
            sort($v, SORT_STRING);
            return $v;
        };

        $correctCount = 0;

        // Use the actual quiz question count for the denominator, not just
        // submitted answers — otherwise a learner who skips half the quiz
        // gets the same score as one who answered every question correctly.
        $totalQuestions = (int) ($db->queryOne(
            'SELECT COUNT(*) c FROM quiz_questions
              WHERE quiz_id = ?
                AND (status IS NULL OR status = "published")',
            [$quizId]
        )['c'] ?? 0);
        if ($totalQuestions === 0) {
            // Defensive fallback — if the quiz has no published questions
            // for some reason, use whatever the user submitted so we don't
            // divide by zero.
            $totalQuestions = max(1, count($answersArray));
        }

        foreach ($answersArray as $entry) {
            $questionId = $entry['question_id'] ?? null;
            $answer     = $entry['answer'] ?? null;

            if (!$questionId) continue;

            $question = $db->queryOne(
                'SELECT correct_answer FROM quiz_questions WHERE id = ?',
                [$questionId]
            );

            $isCorrect = false;
            if ($question && $answer !== null) {
                $rawCorrect = $question['correct_answer'] ?? '';
                // Try JSON decode first; if it fails (legacy plain-string
                // storage), fall back to the raw value.
                $correctDecoded = json_decode((string) $rawCorrect, true);
                if ($correctDecoded === null && $rawCorrect !== '' && $rawCorrect !== 'null') {
                    $correctDecoded = $rawCorrect;
                }

                $expected  = $normalise($correctDecoded);
                $submitted = $normalise($answer);

                $isCorrect = !empty($expected) && $expected === $submitted;
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

        // Phase 13 — wire Quiz result into the unified study chrome too.
        $layout = 'pilot';
        $cfg    = require BASE_PATH . '/config/app.php';
        if (!empty($cfg['features']['study_chrome_v2']) && (int)($quiz['system_id'] ?? 0) > 0) {
            $sysView = $this->loadSystemForChrome((int) $quiz['system_id']);
            if ($sysView) {
                $data   = array_merge($data, $this->buildStudyChromeData($sysView, 'quiz'));
                $layout = 'study';
            }
        }

        $html = $this->view('quiz/result', $data, $layout);
        $response->html($html);
    }

    /**
     * Phase 13 — pull the columns the study chrome needs to render the
     * breadcrumb, mode switcher, and lesson drawer.
     */
    private function loadSystemForChrome(int $systemId): ?array
    {
        try {
            $row = DB::instance()->queryOne(
                'SELECT id, name, ata_code, color_hex FROM systems WHERE id = ? AND is_published = 1',
                [$systemId]
            );
        } catch (\Throwable $e) {
            return null;
        }
        if (!$row) return null;
        return [
            'id'       => (int) $row['id'],
            'name'     => (string) $row['name'],
            'ata_code' => (string) ($row['ata_code'] ?? ''),
            'color'    => (string) ($row['color_hex'] ?? '#38BDF8'),
        ];
    }

    /**
     * Phase 13 — same shape as FlashcardController::buildStudyChromeData
     * so the quiz pages render with the same breadcrumb / mode switcher /
     * drawer that Slides + Flashcards use.
     */
    private function buildStudyChromeData(array $system, string $modeKey): array
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

        $modeLabels = [
            'slides'     => 'Slides',
            'flashcards' => 'Flashcards',
            'quiz'       => 'Quiz',
            'mnemonics'  => 'Mnemonics',
            'mind_map'   => 'Mind Map',
            'deep_notes' => 'Deep Notes',
        ];

        return [
            'studyChromeV2'         => true,
            'studySystemColor'      => $accent,
            'studyBreadcrumb'       => [
                ['label' => 'Q400',  'href' => '/my-subjects'],
                ['label' => $sysName, 'href' => '/study/' . $sid],
                ['label' => $modeLabels[$modeKey] ?? ucfirst($modeKey), 'href' => ''],
            ],
            'studyModes' => [
                ['key' => 'slides',     'label' => 'Slides',     'href' => '/study/' . $sid, 'icon' => 'play-circle',
                 'active' => $modeKey === 'slides'],
                ['key' => 'flashcards', 'label' => 'Flashcards', 'href' => '/flashcards/' . $sid, 'icon' => 'rectangle-vertical',
                 'active' => $modeKey === 'flashcards'],
                ['key' => 'quiz',       'label' => 'Quiz',       'href' => '/quiz', 'icon' => 'check-circle-2',
                 'active' => $modeKey === 'quiz'],
                ['key' => 'mnemonics',  'label' => 'Mnemonics',  'href' => '/study/' . $sid . '/mnemonics',  'icon' => 'brain',
                 'active'   => $modeKey === 'mnemonics',
                 'disabled' => !$featOn('mnemonics_v2')],
                ['key' => 'mind_map',   'label' => 'Mind Map',   'href' => '/study/' . $sid . '/mind-map',   'icon' => 'git-branch',
                 'active'   => $modeKey === 'mind_map',
                 'disabled' => !$featOn('mind_map')],
                ['key' => 'deep_notes', 'label' => 'Deep Notes', 'href' => '/study/' . $sid . '/deep-notes', 'icon' => 'file-text',
                 'active'   => $modeKey === 'deep_notes',
                 'disabled' => !$featOn('deep_notes')],
            ],
            'drawerSystem'          => $system,
            'drawerLessons'         => $drawerLessons,
            'drawerCurrentLessonId' => 0,
        ];
    }
}
