<?php
/**
 * Admin Controller — Platform admin dashboard + ops pages.
 * All views use the 'admin' layout (gold sidebar, near-black theme).
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Services\ActivationCodeService;
use App\Services\AdminMetricsService;
use App\Services\AircraftService;
use App\Services\AIContentService;
use App\Services\AIJobService;
use App\Services\AIPromptBuilder;
use App\Services\PdfTextService;
use App\Services\SubscriptionService;

class AdminController extends Controller
{
    public function dashboard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $metrics = AdminMetricsService::dashboard();
        $response->html($this->view('admin/dashboard', [
            'title'   => 'Overview',
            'metrics' => $metrics,
        ], 'admin'));
    }

    /* ---------------- Activation codes ---------------- */

    public function codes(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $statusFilter = (string) $this->input('status', 'all');
        $where = '';
        $params = [];
        if (in_array($statusFilter, ['unused','redeemed','revoked'], true)) {
            $where = 'WHERE status = ?';
            $params = [$statusFilter];
        }
        $codes = DB::instance()->query(
            "SELECT a.*,
                    u.email AS redeemed_email,
                    u.name  AS redeemed_name
             FROM activation_codes a
             LEFT JOIN users u ON u.id = a.redeemed_by_user_id
             {$where}
             ORDER BY a.id DESC
             LIMIT 200",
            $params
        );
        $summary = ActivationCodeService::summary();
        $response->html($this->view('admin/codes', [
            'title'        => 'Activation Codes',
            'codes'        => $codes,
            'summary'      => $summary,
            'statusFilter' => $statusFilter,
            'csrf_token'   => CSRF::generate(),
            'flashOk'      => $this->popFlash('flash_ok'),
            'flashError'   => $this->popFlash('flash_error'),
        ], 'admin'));
    }

    public function codesGenerate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/codes');
            return;
        }
        $count = max(1, min(100, (int) $this->input('count', 5)));
        $days  = max(1, min(3650, (int) $this->input('days',  30)));
        $plan  = (string) $this->input('plan', 'monthly');
        $admin = $this->user();
        $codes = ActivationCodeService::generate($count, $days, $plan, (int) ($admin['id'] ?? 0));
        $_SESSION['flash_ok'] = "Generated {$count} codes ({$days}-day {$plan}).";
        $this->redirect('/admin/codes');
    }

    public function codesRevoke(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/codes');
            return;
        }
        $code = (string) $this->input('code', '');
        if ($code === '') {
            $_SESSION['flash_error'] = 'No code given.';
            $this->redirect('/admin/codes');
            return;
        }
        $ok = ActivationCodeService::revoke($code);
        $_SESSION[$ok ? 'flash_ok' : 'flash_error'] = $ok ? "Code {$code} revoked." : "Could not revoke {$code}.";
        $this->redirect('/admin/codes');
    }

    /* ---------------- Users ---------------- */

    public function users(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $users = DB::instance()->query(
            'SELECT u.id, u.name, u.email, u.role, u.last_active, u.created_at, u.preferred_aircraft_id,
                    u.email_verified_at,
                    a.short_name AS preferred_aircraft,
                    (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id AND s.status="active" AND s.expires_at > NOW()) AS sub_active
             FROM users u
             LEFT JOIN aircrafts a ON a.id = u.preferred_aircraft_id
             ORDER BY u.id DESC
             LIMIT 200'
        );
        $response->html($this->view('admin/users', [
            'title'      => 'Users',
            'users'      => $users,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'admin'));
    }

    public function usersUpdate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/users');
            return;
        }
        $userId = (int) $this->input('user_id', 0);
        $role   = (string) $this->input('role', 'learner');
        if ($userId <= 0 || !in_array($role, ['admin','learner'], true)) {
            $_SESSION['flash_error'] = 'Invalid input.';
            $this->redirect('/admin/users');
            return;
        }
        $self = $this->user();
        if ((int) $self['id'] === $userId && $role !== 'admin') {
            $_SESSION['flash_error'] = 'You cannot remove your own admin role.';
            $this->redirect('/admin/users');
            return;
        }
        DB::instance()->execute('UPDATE users SET role = ? WHERE id = ?', [$role, $userId]);
        $_SESSION['flash_ok'] = "User #{$userId} role set to {$role}.";
        $this->redirect('/admin/users');
    }

    /**
     * Manually mark a user's email as verified.
     * Useful when SMTP is failing and a real user can't receive the verify email.
     */
    public function userVerify(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/users');
            return;
        }
        $userId = (int) $this->input('user_id', 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user.';
            $this->redirect('/admin/users');
            return;
        }
        $db = DB::instance();
        $row = $db->queryOne('SELECT id, email, email_verified_at FROM users WHERE id = ?', [$userId]);
        if (!$row) {
            $_SESSION['flash_error'] = 'User not found.';
            $this->redirect('/admin/users');
            return;
        }
        if (!empty($row['email_verified_at'])) {
            $_SESSION['flash_notice'] = "User #{$userId} ({$row['email']}) was already verified.";
            $this->redirect('/admin/users');
            return;
        }
        $db->execute('UPDATE users SET email_verified_at = NOW() WHERE id = ?', [$userId]);
        // Burn any outstanding verification tokens so they can't be reused.
        $db->execute('UPDATE email_verifications SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL', [$userId]);
        $_SESSION['flash_ok'] = "User #{$userId} ({$row['email']}) marked as verified.";
        $this->redirect('/admin/users');
    }

    /**
     * Resend the email-verification link to a specific user. Mirrors
     * AuthController::resendVerification but is admin-initiated, so it
     * always reports the outcome (vs. the privacy-preserving generic
     * flash on the public flow).
     */
    public function userResendVerify(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/users');
            return;
        }
        $userId = (int) $this->input('user_id', 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user.';
            $this->redirect('/admin/users');
            return;
        }
        $db = DB::instance();
        $user = $db->queryOne('SELECT id, name, email, email_verified_at FROM users WHERE id = ?', [$userId]);
        if (!$user) {
            $_SESSION['flash_error'] = 'User not found.';
            $this->redirect('/admin/users');
            return;
        }
        if (!empty($user['email_verified_at'])) {
            $_SESSION['flash_notice'] = "User #{$userId} ({$user['email']}) is already verified.";
            $this->redirect('/admin/users');
            return;
        }

        $cfg      = require BASE_PATH . '/config/app.php';
        $rawToken = \App\Services\EmailService::generateToken();
        $hash     = \App\Services\EmailService::hashToken($rawToken);
        $expires  = date('Y-m-d H:i:s', time() + ((int) ($cfg['verification_token_ttl_hours'] ?? 48)) * 3600);

        $db->insert(
            'INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (?, ?, ?)',
            [$user['id'], $hash, $expires]
        );
        $sent = \App\Services\EmailService::sendVerification($user, $rawToken);

        if ($sent) {
            $_SESSION['flash_ok'] = "Fresh verification link sent to {$user['email']}.";
        } else {
            $_SESSION['flash_notice'] = "Verification link generated for {$user['email']} but mail() returned failure — see storage/logs/mail.log for the raw link.";
        }
        $this->redirect('/admin/users');
    }

    /* ---------------- Subscriptions ---------------- */

    public function subscriptions(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $subs = DB::instance()->query(
            'SELECT s.*, u.name, u.email
             FROM subscriptions s
             JOIN users u ON u.id = s.user_id
             ORDER BY s.id DESC
             LIMIT 200'
        );
        $summary = SubscriptionService::summary();
        $response->html($this->view('admin/subscriptions', [
            'title'         => 'Subscriptions',
            'subscriptions' => $subs,
            'summary'       => $summary,
            'csrf_token'    => CSRF::generate(),
        ], 'admin'));
    }

    public function subscriptionCancel(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/subscriptions');
            return;
        }
        $subId = (int) $this->input('subscription_id', 0);
        if ($subId <= 0) {
            $_SESSION['flash_error'] = 'Invalid subscription.';
            $this->redirect('/admin/subscriptions');
            return;
        }
        SubscriptionService::cancel($subId, true);
        $_SESSION['flash_ok'] = "Subscription #{$subId} cancelled.";
        $this->redirect('/admin/subscriptions');
    }

    /* ---------------- Leads ---------------- */

    public function leads(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $leads = DB::instance()->query(
            'SELECT * FROM lead_signups ORDER BY id DESC LIMIT 500'
        );
        $byModule = DB::instance()->query(
            'SELECT requested_module_slug AS slug, COUNT(*) AS n
             FROM lead_signups
             GROUP BY requested_module_slug
             ORDER BY n DESC'
        );
        $response->html($this->view('admin/leads', [
            'title'    => 'Leads',
            'leads'    => $leads,
            'byModule' => $byModule,
        ], 'admin'));
    }

    /* ---------------- Aircrafts ---------------- */

    public function aircrafts(Request $request, Response $response): void
    {
        $this->requireAdmin();
        AircraftService::refreshWaitlistCounts();
        $rows = AircraftService::all();
        $response->html($this->view('admin/aircrafts', [
            'title'      => 'Aircraft',
            'aircrafts'  => $rows,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'admin'));
    }

    public function aircraftsUpdate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/aircrafts');
            return;
        }
        $id     = (int) $this->input('id', 0);
        $status = (string) $this->input('status', 'coming_soon');
        $sort   = (int) $this->input('sort_order', 100);
        if ($id <= 0 || !in_array($status, ['live','beta','coming_soon','archived'], true)) {
            $_SESSION['flash_error'] = 'Invalid input.';
            $this->redirect('/admin/aircrafts');
            return;
        }
        DB::instance()->execute(
            'UPDATE aircrafts SET status = ?, sort_order = ? WHERE id = ?',
            [$status, $sort, $id]
        );
        $_SESSION['flash_ok'] = 'Aircraft updated.';
        $this->redirect('/admin/aircrafts');
    }

    /* ---------------- Content ---------------- */

    public function content(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $aircraftId = (int) $this->input('aircraft_id', 0);
        $where = '';
        $params = [];
        if ($aircraftId > 0) {
            $where = 'WHERE s.aircraft_id = ?';
            $params = [$aircraftId];
        }
        $systems = DB::instance()->query(
            "SELECT s.id, s.name, s.slug, s.ata_code, s.is_published, a.short_name AS aircraft,
                    (SELECT COUNT(*) FROM lessons WHERE system_id = s.id) AS lesson_count,
                    (SELECT COUNT(*) FROM flashcards WHERE system_id = s.id) AS card_count,
                    (SELECT COUNT(*) FROM quizzes WHERE system_id = s.id) AS quiz_count
             FROM systems s
             LEFT JOIN aircrafts a ON a.id = s.aircraft_id
             {$where}
             ORDER BY s.aircraft_id, s.sort_order, s.id
             LIMIT 200",
            $params
        );
        $aircrafts = AircraftService::all();
        $response->html($this->view('admin/content', [
            'title'      => 'Content',
            'systems'    => $systems,
            'aircrafts'  => $aircrafts,
            'aircraftId' => $aircraftId,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function createContent(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
    }

    /* ---------------- Import ---------------- */

    public function import(Request $request, Response $response): void
    {
        $this->requireAdmin();

        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $recentJobs = DB::instance()->query(
            'SELECT id, source_label, original_filename, mode, analysis_depth,
                    status, progress_pct, progress_message, created_at
             FROM ai_generation_jobs
             ORDER BY id DESC LIMIT 10'
        );

        $cfg = AIContentService::config();

        $response->html($this->view('admin/import', [
            'title'           => 'Import',
            'csrf_token'      => CSRF::generate(),
            'systems'         => $systems,
            'api_configured'  => $cfg['configured'],
            'extract_status'  => PdfTextService::backendStatus(),
            'recent_jobs'     => $recentJobs,
        ], 'admin'));
    }

    /**
     * Enqueue an AI generation job. Replaces the previous stub.
     */
    public function processImport(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF']);
            return;
        }

        $mode  = (string) $this->input('mode', 'full');
        $depth = (string) $this->input('analysis_depth', 'standard');
        if (!in_array($mode,  ['manual','assisted','full'],  true)) $mode  = 'full';
        if (!in_array($depth, ['standard','detail'],         true)) $depth = 'standard';

        $sourceLabel = trim((string) $this->input('source_label', ''));
        $pasted      = (string) $this->input('pasted_text', '');
        $targetSysId = (int) $this->input('target_system_id', 0);
        if ($targetSysId <= 0) {
            $response->status(422);
            $response->json(['error' => 'target_system_id is required']);
            return;
        }

        // Save uploaded PDF (if any) to a stable location.
        $pdfPath          = null;
        $originalFilename = null;
        if ($this->hasFile('pdf_file')) {
            $f = $_FILES['pdf_file'] ?? null;
            if (is_array($f) && ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $tmp = (string) ($f['tmp_name'] ?? '');
                if (is_uploaded_file($tmp)) {
                    $uploadDir = BASE_PATH . '/storage/uploads/ai';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0775, true);
                    }
                    $safe = preg_replace('/[^A-Za-z0-9._-]+/', '_', (string) $f['name']);
                    $pdfPath = $uploadDir . '/' . date('Ymd-His') . '-' . $safe;
                    if (!@move_uploaded_file($tmp, $pdfPath)) {
                        $pdfPath = null;
                    } else {
                        $originalFilename = (string) $f['name'];
                    }
                }
            }
        }

        if ($pdfPath === null && trim($pasted) === '') {
            $response->status(422);
            $response->json(['error' => 'Either upload a PDF or paste text.']);
            return;
        }

        $jobId = AIJobService::enqueue([
            'admin_user_id'     => (int) ($this->user()['id'] ?? 0),
            'target_system_id'  => $targetSysId,
            'pdf_path'          => $pdfPath,
            'original_filename' => $originalFilename,
            'source_label'      => $sourceLabel,
            'pasted_text'       => $pasted !== '' ? $pasted : null,
            'mode'              => $mode,
            'analysis_depth'    => $depth,
        ]);

        // Manual mode skips Claude entirely — mark the job as 'review'
        // immediately so the admin sees an empty drafts shell to fill in.
        if ($mode === 'manual') {
            DB::instance()->execute(
                'UPDATE ai_generation_jobs
                 SET status = "review",
                     progress_pct = 100,
                     progress_message = "Manual mode — no AI call",
                     finished_at = NOW()
                 WHERE id = ?',
                [$jobId]
            );
        }

        // Redirect to the job status page where the admin can watch
        // progress (auto-refreshes while running).
        $response->redirect('/admin/ai-jobs/' . $jobId);
    }

    public function aiJobsList(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $jobs = DB::instance()->query(
            'SELECT * FROM ai_generation_jobs ORDER BY id DESC LIMIT 100'
        );
        $response->html($this->view('admin/ai_jobs', [
            'title' => 'AI Jobs',
            'jobs'  => $jobs,
        ], 'admin'));
    }

    /**
     * Publish all drafts produced by one AI generation job — single
     * transaction that flips lessons / slides / flashcards / quizzes /
     * quiz_questions tied to this ai_job_id from draft to published, and
     * marks the job itself published.
     */
    public function aiJobPublish(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF']);
            return;
        }

        $id = (int) $this->param('id');
        $db = DB::instance();
        $job = $db->queryOne('SELECT * FROM ai_generation_jobs WHERE id = ?', [$id]);
        if (!$job) {
            $response->status(404);
            $response->html('Job not found.');
            return;
        }
        if (!in_array((string) $job['status'], ['review', 'failed'], true)) {
            // Anything else (queued/running/published/cancelled) is invalid.
            $response->redirect('/admin/ai-jobs/' . $id);
            return;
        }

        $db->execute('START TRANSACTION');
        try {
            // Flip the lesson row, if there is one
            if (!empty($job['lesson_id'])) {
                $db->execute(
                    'UPDATE lessons
                     SET draft_status = "published", is_published = 1
                     WHERE id = ?',
                    [(int) $job['lesson_id']]
                );
            }

            // Flip slides
            $db->execute(
                'UPDATE lesson_slides SET status = "published" WHERE ai_job_id = ?',
                [$id]
            );

            // Flip flashcards
            $db->execute(
                'UPDATE flashcards SET status = "published" WHERE ai_job_id = ?',
                [$id]
            );

            // Flip quiz questions and the parent quiz row
            $db->execute(
                'UPDATE quiz_questions SET status = "published" WHERE ai_job_id = ?',
                [$id]
            );
            $db->execute(
                'UPDATE quizzes
                 SET is_published = 1
                 WHERE id IN (
                    SELECT * FROM (
                        SELECT DISTINCT quiz_id FROM quiz_questions WHERE ai_job_id = ?
                    ) AS sub
                 )',
                [$id]
            );

            // Mark the job itself
            $db->execute(
                'UPDATE ai_generation_jobs
                 SET status = "published",
                     progress_message = "Published"
                 WHERE id = ?',
                [$id]
            );

            $db->execute('COMMIT');
        } catch (\Throwable $e) {
            $db->execute('ROLLBACK');
            $response->status(500);
            $response->html('Publish failed: ' . htmlspecialchars($e->getMessage()));
            return;
        }

        $response->redirect('/admin/ai-jobs/' . $id);
    }

    /**
     * Discard a job — deletes the lesson and any draft rows linked to it.
     * Only available when the job is in 'review' or 'failed' state.
     */
    public function aiJobDiscard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF']);
            return;
        }

        $id = (int) $this->param('id');
        $db = DB::instance();
        $job = $db->queryOne('SELECT * FROM ai_generation_jobs WHERE id = ?', [$id]);
        if (!$job) {
            $response->status(404);
            $response->html('Job not found.');
            return;
        }
        if (!in_array((string) $job['status'], ['review', 'failed'], true)) {
            $response->redirect('/admin/ai-jobs/' . $id);
            return;
        }

        $db->execute('START TRANSACTION');
        try {
            // Lesson cascade-deletes its slides via FK; we still need to
            // clean up flashcards + quiz_questions + quizzes that share
            // the ai_job_id but were created at the system level.
            if (!empty($job['lesson_id'])) {
                $db->execute('DELETE FROM lessons WHERE id = ?', [(int) $job['lesson_id']]);
            }
            $db->execute('DELETE FROM flashcards WHERE ai_job_id = ?', [$id]);
            // Find quizzes whose only questions are from this job and delete them
            $orphanQuizIds = $db->query(
                'SELECT q.id
                 FROM quizzes q
                 JOIN quiz_questions qq ON qq.quiz_id = q.id
                 WHERE qq.ai_job_id = ?
                 GROUP BY q.id
                 HAVING COUNT(*) = SUM(qq.ai_job_id = ?)',
                [$id, $id]
            );
            $db->execute('DELETE FROM quiz_questions WHERE ai_job_id = ?', [$id]);
            foreach ($orphanQuizIds as $row) {
                $db->execute('DELETE FROM quizzes WHERE id = ?', [(int) $row['id']]);
            }

            $db->execute(
                'UPDATE ai_generation_jobs
                 SET status = "cancelled",
                     progress_message = "Discarded"
                 WHERE id = ?',
                [$id]
            );

            $db->execute('COMMIT');
        } catch (\Throwable $e) {
            $db->execute('ROLLBACK');
            $response->status(500);
            $response->html('Discard failed: ' . htmlspecialchars($e->getMessage()));
            return;
        }

        $response->redirect('/admin/ai-jobs');
    }

    public function aiJobShow(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int) $this->param('id');
        $job = DB::instance()->queryOne(
            'SELECT * FROM ai_generation_jobs WHERE id = ?',
            [$id]
        );
        if (!$job) {
            $response->status(404);
            $response->html('Job not found.');
            return;
        }

        $lesson = null;
        $slides = [];
        $flashcards = [];
        $quizQs = [];
        if (!empty($job['lesson_id'])) {
            $lesson = DB::instance()->queryOne(
                'SELECT * FROM lessons WHERE id = ?',
                [(int) $job['lesson_id']]
            );
            $slides = DB::instance()->query(
                'SELECT id, slide_type, title, body, key_point, ops_relevance,
                        question, source_quote, sort_order, status
                 FROM lesson_slides WHERE ai_job_id = ?
                 ORDER BY sort_order, id',
                [$id]
            );
            $flashcards = DB::instance()->query(
                'SELECT id, front, back, expected_answer, grading_rubric,
                        hint, difficulty, status
                 FROM flashcards WHERE ai_job_id = ? ORDER BY id',
                [$id]
            );
            $quizQs = DB::instance()->query(
                'SELECT id, question_text, options, correct_answer, explanation,
                        difficulty, status
                 FROM quiz_questions WHERE ai_job_id = ? ORDER BY id',
                [$id]
            );
        }

        $targetSystem = null;
        if (!empty($job['target_system_id'])) {
            $targetSystem = DB::instance()->queryOne(
                'SELECT id, name, ata_code FROM systems WHERE id = ?',
                [(int) $job['target_system_id']]
            );
        }

        $response->html($this->view('admin/ai_job_show', [
            'title'          => 'Job #' . $id,
            'job'            => $job,
            'lesson'         => $lesson,
            'slides'         => $slides,
            'flashcards'     => $flashcards,
            'quiz_questions' => $quizQs,
            'target_system'  => $targetSystem,
            'csrf_token'     => CSRF::generate(),
        ], 'admin'));
    }

    /* ---------------- AI Smoke Test (Phase 2) ----------------
     *
     * /admin/ai-test: a one-shot harness that proves we can call Claude
     * end-to-end. The admin pastes (or uploads as PDF) a chunk of a Q400
     * system, and we ask Claude to return one sample slide as structured
     * JSON. No DB writes — the result is rendered straight back to the
     * page so we can eyeball the contract.
     */

    public function aiTest(Request $request, Response $response): void
    {
        $this->requireAdmin();

        $cfg          = AIContentService::config();
        $pdfTextBin   = PdfTextService::pdftotextPath();

        $response->html($this->view('admin/ai_test', [
            'title'           => 'AI Smoke Test',
            'csrf_token'      => CSRF::generate(),
            'configured'      => $cfg['configured'],
            'model_chunk'     => $cfg['model_chunk'],
            'model_outline'   => $cfg['model_outline'],
            'pdftotext_path'  => $pdfTextBin,
            'result'          => null,
            'submitted'       => false,
            'source_label'    => '',
            'pasted_text'     => '',
        ], 'admin'));
    }

    public function aiTestRun(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $response->status(419);
            $response->html('CSRF token mismatch — refresh the page and try again.');
            return;
        }

        $cfg = AIContentService::config();

        $sourceLabel = trim((string) $this->input('source_label', ''));
        $pasted      = (string) $this->input('pasted_text', '');
        $extracted   = '';
        $extractInfo = null;

        // 1. If a PDF was uploaded, try pdftotext first.
        if ($this->hasFile('pdf_file')) {
            $file = $_FILES['pdf_file'] ?? null;
            if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $tmp = (string) ($file['tmp_name'] ?? '');
                if (is_uploaded_file($tmp)) {
                    // Cap at ~120 KB of extracted text for a smoke test
                    $extractInfo = PdfTextService::extract($tmp, 120000);
                    if (($extractInfo['ok'] ?? false) === true) {
                        $extracted = (string) ($extractInfo['text'] ?? '');
                        if ($sourceLabel === '') {
                            $sourceLabel = (string) ($file['name'] ?? 'uploaded PDF');
                        }
                    }
                }
            }
        }

        $userText = $extracted !== '' ? $extracted : trim($pasted);
        if ($userText === '') {
            $response->html($this->view('admin/ai_test', [
                'title'           => 'AI Smoke Test',
                'csrf_token'      => CSRF::generate(),
                'configured'      => $cfg['configured'],
                'model_chunk'     => $cfg['model_chunk'],
                'model_outline'   => $cfg['model_outline'],
                'pdftotext_path'  => PdfTextService::pdftotextPath(),
                'result'          => [
                    'ok'    => false,
                    'error' => 'no_input',
                    'error_detail' => 'Either upload a PDF (and pdftotext must be available) or paste a text excerpt.',
                    'extract_info' => $extractInfo,
                ],
                'submitted'       => true,
                'source_label'    => $sourceLabel,
                'pasted_text'     => $pasted,
            ], 'admin'));
            return;
        }

        $systemPrompt = AIPromptBuilder::smokeTestSystem();
        $userPrompt   = AIPromptBuilder::smokeTestUser(
            $sourceLabel !== '' ? $sourceLabel : 'pasted excerpt',
            $userText
        );

        $apiResponse = AIContentService::generate($systemPrompt, $userPrompt);

        $parsedJson = null;
        if (($apiResponse['ok'] ?? false) === true && !empty($apiResponse['text'])) {
            $parsedJson = AIContentService::extractJson((string) $apiResponse['text']);
        }

        $response->html($this->view('admin/ai_test', [
            'title'           => 'AI Smoke Test',
            'csrf_token'      => CSRF::generate(),
            'configured'      => $cfg['configured'],
            'model_chunk'     => $cfg['model_chunk'],
            'model_outline'   => $cfg['model_outline'],
            'pdftotext_path'  => PdfTextService::pdftotextPath(),
            'result'          => [
                'ok'              => (bool) ($apiResponse['ok'] ?? false),
                'api'             => $apiResponse,
                'extract_info'    => $extractInfo,
                'input_chars'     => strlen($userText),
                'parsed_json'     => $parsedJson,
                'parsed_json_ok'  => is_array($parsedJson),
            ],
            'submitted'       => true,
            'source_label'    => $sourceLabel,
            'pasted_text'     => $extracted !== '' ? '' : $pasted,
        ], 'admin'));
    }

    /* ---------------- Flashcards ---------------- */

    /** List flashcards (newest 100) with the system name for context. */
    public function flashcards(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $cards = DB::instance()->query(
            'SELECT f.id, f.front, f.back, f.hint, f.difficulty, f.system_id,
                    s.name AS system_name
             FROM flashcards f
             LEFT JOIN systems s ON s.id = f.system_id
             ORDER BY f.id DESC LIMIT 100'
        );
        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $response->html($this->view('admin/flashcards', [
            'title'      => 'Flashcards',
            'cards'      => $cards,
            'systems'    => $systems,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Form to create a new flashcard. */
    public function flashcardNew(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $response->html($this->view('admin/flashcard-edit', [
            'title'      => 'New Flashcard',
            'card'       => null,
            'systems'    => $systems,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Persist a new flashcard. */
    public function createFlashcard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/flashcards'); return;
        }
        [$payload, $err] = $this->validateFlashcard($request);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            $this->redirect('/admin/flashcards/new'); return;
        }
        DB::instance()->execute(
            'INSERT INTO flashcards (system_id, front, back, hint, difficulty, base_difficulty, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                $payload['system_id'], $payload['front'], $payload['back'],
                $payload['hint'], $payload['difficulty'],
                $payload['difficulty'] === 'easy' ? 1 : ($payload['difficulty'] === 'hard' ? 3 : 2),
            ]
        );
        $_SESSION['flash_ok'] = 'Flashcard created.';
        $this->redirect('/admin/flashcards');
    }

    /** Form to edit an existing flashcard. */
    public function flashcardEdit(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        $card = DB::instance()->queryOne('SELECT * FROM flashcards WHERE id = ?', [$id]);
        if (!$card) {
            $_SESSION['flash_error'] = 'Flashcard not found.';
            $this->redirect('/admin/flashcards'); return;
        }
        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $response->html($this->view('admin/flashcard-edit', [
            'title'      => 'Edit Flashcard #' . $id,
            'card'       => $card,
            'systems'    => $systems,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Persist edits to an existing flashcard. */
    public function updateFlashcard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/flashcards/' . $id . '/edit'); return;
        }
        [$payload, $err] = $this->validateFlashcard($request);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            $this->redirect('/admin/flashcards/' . $id . '/edit'); return;
        }
        DB::instance()->execute(
            'UPDATE flashcards
                SET system_id = ?, front = ?, back = ?, hint = ?,
                    difficulty = ?, base_difficulty = ?
              WHERE id = ?',
            [
                $payload['system_id'], $payload['front'], $payload['back'],
                $payload['hint'], $payload['difficulty'],
                $payload['difficulty'] === 'easy' ? 1 : ($payload['difficulty'] === 'hard' ? 3 : 2),
                $id,
            ]
        );
        $_SESSION['flash_ok'] = 'Flashcard updated.';
        $this->redirect('/admin/flashcards');
    }

    /** Delete a flashcard. */
    public function deleteFlashcard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/flashcards'); return;
        }
        DB::instance()->execute('DELETE FROM flashcards WHERE id = ?', [$id]);
        $_SESSION['flash_ok'] = 'Flashcard deleted.';
        $this->redirect('/admin/flashcards');
    }

    /**
     * Whitelist + sanitize POSTed flashcard fields.
     * Returns [payload, errorMessageOrNull].
     */
    private function validateFlashcard(Request $request): array
    {
        $front = trim((string)$this->input('front', ''));
        $back  = trim((string)$this->input('back', ''));
        $hint  = trim((string)$this->input('hint', ''));
        $sysId = (int)$this->input('system_id', 0);
        $diff  = (string)$this->input('difficulty', 'medium');

        if ($front === '' || mb_strlen($front) > 2000) {
            return [[], 'Front side must be 1–2000 characters.'];
        }
        if ($back === '' || mb_strlen($back) > 2000) {
            return [[], 'Back side must be 1–2000 characters.'];
        }
        if (mb_strlen($hint) > 500) {
            return [[], 'Hint must be under 500 characters.'];
        }
        if (!in_array($diff, ['easy','medium','hard'], true)) {
            $diff = 'medium';
        }
        if ($sysId <= 0) {
            return [[], 'Pick a system.'];
        }
        $exists = DB::instance()->queryOne('SELECT id FROM systems WHERE id = ?', [$sysId]);
        if (!$exists) {
            return [[], 'Invalid system.'];
        }
        return [[
            'system_id'  => $sysId,
            'front'      => $front,
            'back'       => $back,
            'hint'       => $hint !== '' ? $hint : null,
            'difficulty' => $diff,
        ], null];
    }

    /* ---------------- Quizzes ---------------- */

    /** List quizzes (newest 100) with question counts. */
    public function quizzes(Request $request, Response $response): void
    {
        $this->requireAdmin();
        // quiz_questions is wired against the legacy `modules` table; for the
        // quiz container itself we just show the basic metadata + timing.
        // Schema drift: schema.sql named the column `time_limit_mins`,
        // some installs renamed it to `time_limit_minutes`. Pick whichever
        // exists so prod and dev both work.
        $cols = array_column(
            DB::instance()->query('SHOW COLUMNS FROM quizzes'),
            'Field'
        );
        $timeCol = in_array('time_limit_minutes', $cols, true)
            ? 'q.time_limit_minutes'
            : (in_array('time_limit_mins', $cols, true) ? 'q.time_limit_mins' : 'NULL');

        $quizzes = DB::instance()->query(
            "SELECT q.id, q.title, q.quiz_type, $timeCol AS time_limit_minutes,
                    q.pass_score, q.is_published, q.system_id,
                    s.name AS system_name
             FROM quizzes q
             LEFT JOIN systems s ON s.id = q.system_id
             ORDER BY q.id DESC LIMIT 100"
        );
        $response->html($this->view('admin/quizzes', [
            'title'      => 'Quizzes',
            'quizzes'    => $quizzes,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Form to create a new quiz. */
    public function quizNew(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $response->html($this->view('admin/quiz-edit', [
            'title'      => 'New Quiz',
            'quiz'       => null,
            'systems'    => $systems,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Persist a new quiz container. */
    public function createQuiz(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/quizzes'); return;
        }
        [$payload, $err] = $this->validateQuiz($request);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            $this->redirect('/admin/quizzes/new'); return;
        }
        DB::instance()->execute(
            'INSERT INTO quizzes (system_id, title, quiz_type, time_limit_minutes,
                                  pass_score, is_published, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, 0)',
            [
                $payload['system_id'], $payload['title'], $payload['quiz_type'],
                $payload['time_limit_minutes'], $payload['pass_score'],
                $payload['is_published'],
            ]
        );
        $_SESSION['flash_ok'] = 'Quiz created. Questions are managed via the legacy module pipeline.';
        $this->redirect('/admin/quizzes');
    }

    /** Form to edit an existing quiz. */
    public function quizEdit(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        $quiz = DB::instance()->queryOne('SELECT * FROM quizzes WHERE id = ?', [$id]);
        if (!$quiz) {
            $_SESSION['flash_error'] = 'Quiz not found.';
            $this->redirect('/admin/quizzes'); return;
        }
        $systems = DB::instance()->query(
            'SELECT id, name, ata_code FROM systems ORDER BY sort_order, id'
        );
        $response->html($this->view('admin/quiz-edit', [
            'title'      => 'Edit Quiz #' . $id,
            'quiz'       => $quiz,
            'systems'    => $systems,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /** Persist edits to an existing quiz. */
    public function updateQuiz(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/quizzes/' . $id . '/edit'); return;
        }
        [$payload, $err] = $this->validateQuiz($request);
        if ($err !== null) {
            $_SESSION['flash_error'] = $err;
            $this->redirect('/admin/quizzes/' . $id . '/edit'); return;
        }
        DB::instance()->execute(
            'UPDATE quizzes
                SET system_id = ?, title = ?, quiz_type = ?,
                    time_limit_minutes = ?, pass_score = ?, is_published = ?
              WHERE id = ?',
            [
                $payload['system_id'], $payload['title'], $payload['quiz_type'],
                $payload['time_limit_minutes'], $payload['pass_score'],
                $payload['is_published'], $id,
            ]
        );
        $_SESSION['flash_ok'] = 'Quiz updated.';
        $this->redirect('/admin/quizzes');
    }

    /** Delete a quiz. */
    public function deleteQuiz(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int)$this->param('id', 0);
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please reload and try again.';
            $this->redirect('/admin/quizzes'); return;
        }
        DB::instance()->execute('DELETE FROM quizzes WHERE id = ?', [$id]);
        $_SESSION['flash_ok'] = 'Quiz deleted.';
        $this->redirect('/admin/quizzes');
    }

    /** Whitelist + sanitize POSTed quiz fields. */
    private function validateQuiz(Request $request): array
    {
        $title  = trim((string)$this->input('title', ''));
        $sysId  = (int)$this->input('system_id', 0);
        $qtype  = (string)$this->input('quiz_type', 'standard');
        $tlim   = (int)$this->input('time_limit_minutes', 0);
        $pass   = (int)$this->input('pass_score', 70);
        $pub    = (int)$this->input('is_published', 1);

        if ($title === '' || mb_strlen($title) > 255) {
            return [[], 'Title must be 1–255 characters.'];
        }
        if (!in_array($qtype, ['standard','exam_prep','rapid_fire'], true)) {
            $qtype = 'standard';
        }
        if ($tlim < 0 || $tlim > 240) {
            return [[], 'Time limit must be 0–240 minutes (0 = untimed).'];
        }
        if ($pass < 0 || $pass > 100) {
            return [[], 'Pass score must be 0–100.'];
        }
        if ($sysId <= 0) {
            return [[], 'Pick a system.'];
        }
        $exists = DB::instance()->queryOne('SELECT id FROM systems WHERE id = ?', [$sysId]);
        if (!$exists) {
            return [[], 'Invalid system.'];
        }
        return [[
            'system_id'          => $sysId,
            'title'              => $title,
            'quiz_type'          => $qtype,
            'time_limit_minutes' => $tlim > 0 ? $tlim : null,
            'pass_score'         => $pass,
            'is_published'       => $pub ? 1 : 0,
        ], null];
    }

    /* ---------------- Slides (Phase 3 admin editor) ---------------- */

    /**
     * Browse lessons with slide counts. Pick a lesson to edit its slides.
     */
    public function slidesIndex(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $rows = DB::instance()->query(
            "SELECT s.id AS system_id, s.name AS system_name, s.ata_code,
                    l.id AS lesson_id, l.title AS lesson_title, l.slug AS lesson_slug,
                    (SELECT COUNT(*) FROM lesson_slides ls WHERE ls.lesson_id = l.id) AS slide_count
             FROM systems s
             INNER JOIN lessons l ON l.system_id = s.id
             WHERE s.is_published = 1 AND l.is_published = 1
             ORDER BY s.sort_order, s.id, l.sort_order, l.id"
        );

        $response->html($this->view('admin/slides_index', [
            'title'      => 'Slide Lessons',
            'rows'       => $rows,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /**
     * Edit slides for one lesson. Renders the full per-slide form list.
     */
    public function slidesEdit(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $lessonId = (int) $this->param('lessonId');
        $db = DB::instance();

        $lesson = $db->queryOne(
            'SELECT l.id, l.title, l.slug, l.summary, l.system_id,
                    s.name AS system_name, s.ata_code
             FROM lessons l
             INNER JOIN systems s ON s.id = l.system_id
             WHERE l.id = ?',
            [$lessonId]
        );
        if (!$lesson) {
            $this->renderNotFound('Lesson Not Found', 'That lesson doesn’t exist or has been removed. Pick a lesson from the slide lessons index.', '/admin/slides', 'Back to slide lessons');
            return;
        }

        $slides = $db->query(
            'SELECT * FROM lesson_slides WHERE lesson_id = ? ORDER BY sort_order, id',
            [$lessonId]
        );

        $response->html($this->view('admin/slides_edit', [
            'title'      => 'Edit Slides — ' . $lesson['title'],
            'lesson'     => $lesson,
            'slides'     => $slides,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    /**
     * Save (create or update) a single slide for the given lesson.
     */
    public function slidesSave(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Try again.';
            $this->redirect('/admin/slides/lesson/' . (int)$this->param('lessonId'));
            return;
        }

        $lessonId = (int) $this->param('lessonId');
        $db = DB::instance();

        // Verify the lesson exists.
        $lesson = $db->queryOne('SELECT id FROM lessons WHERE id = ?', [$lessonId]);
        if (!$lesson) {
            $_SESSION['flash_error'] = 'Lesson not found.';
            $this->redirect('/admin/slides');
            return;
        }

        $slideId    = (int) $this->input('slide_id', 0);
        $sortOrder  = (int) $this->input('sort_order', 0);
        $allowedTypes = ['intro','concept','system','normal_op','abnormal','operational','qrh','scenario','revision','quiz'];
        $allowedMedia = ['none','image','diagram','video','animation','model3d'];
        $slideType  = (string) $this->input('slide_type', 'concept');
        if (!in_array($slideType, $allowedTypes, true)) $slideType = 'concept';
        $mediaType  = (string) $this->input('media_type', 'none');
        if (!in_array($mediaType, $allowedMedia, true)) $mediaType = 'none';

        $title         = trim((string) $this->input('title', ''));
        $body          = trim((string) $this->input('body', ''));
        $mediaUrl      = trim((string) $this->input('media_url', ''));
        $mediaAlt      = trim((string) $this->input('media_alt', ''));
        $keyPoint      = trim((string) $this->input('key_point', ''));
        $opsRelevance  = trim((string) $this->input('ops_relevance', ''));

        if ($title === '') {
            $_SESSION['flash_error'] = 'Slide title is required.';
            $this->redirect('/admin/slides/lesson/' . $lessonId);
            return;
        }

        // Question gate (optional). Built from named POST fields.
        $qPrompt        = trim((string) $this->input('question_prompt', ''));
        $qOptionsRaw    = (array)  ($_POST['question_options'] ?? []);
        $qCorrectIndex  = $this->input('question_correct_index', null);
        $qExplanation   = trim((string) $this->input('question_explanation', ''));

        $qOptions = [];
        foreach ($qOptionsRaw as $opt) {
            $opt = trim((string) $opt);
            if ($opt !== '') $qOptions[] = $opt;
        }

        $questionJson = null;
        if ($qPrompt !== '' && count($qOptions) >= 2 && $qCorrectIndex !== null && $qCorrectIndex !== '') {
            $ci = (int) $qCorrectIndex;
            if ($ci < 0 || $ci >= count($qOptions)) $ci = 0;
            $questionJson = json_encode([
                'prompt'        => $qPrompt,
                'options'       => array_values($qOptions),
                'correct_index' => $ci,
                'explanation'   => $qExplanation,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($slideId > 0) {
            // Update — make sure slide belongs to this lesson.
            $existing = $db->queryOne(
                'SELECT id FROM lesson_slides WHERE id = ? AND lesson_id = ?',
                [$slideId, $lessonId]
            );
            if (!$existing) {
                $_SESSION['flash_error'] = 'Slide not found in this lesson.';
                $this->redirect('/admin/slides/lesson/' . $lessonId);
                return;
            }
            $db->execute(
                'UPDATE lesson_slides
                 SET sort_order = ?, slide_type = ?, title = ?, body = ?,
                     media_type = ?, media_url = ?, media_alt = ?,
                     key_point = ?, ops_relevance = ?, question = ?
                 WHERE id = ? AND lesson_id = ?',
                [
                    $sortOrder, $slideType, $title, $body !== '' ? $body : null,
                    $mediaType, $mediaUrl !== '' ? $mediaUrl : null, $mediaAlt !== '' ? $mediaAlt : null,
                    $keyPoint !== '' ? $keyPoint : null, $opsRelevance !== '' ? $opsRelevance : null,
                    $questionJson,
                    $slideId, $lessonId,
                ]
            );
            $_SESSION['flash_ok'] = 'Slide saved.';
        } else {
            // Create — auto-pick sort_order if not supplied.
            if ($sortOrder <= 0) {
                $maxRow = $db->queryOne(
                    'SELECT COALESCE(MAX(sort_order), 0) AS max_so FROM lesson_slides WHERE lesson_id = ?',
                    [$lessonId]
                );
                $sortOrder = ((int)($maxRow['max_so'] ?? 0)) + 10;
            }
            $db->insert(
                'INSERT INTO lesson_slides
                    (lesson_id, sort_order, slide_type, title, body,
                     media_type, media_url, media_alt, key_point, ops_relevance, question)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $lessonId, $sortOrder, $slideType, $title, $body !== '' ? $body : null,
                    $mediaType, $mediaUrl !== '' ? $mediaUrl : null, $mediaAlt !== '' ? $mediaAlt : null,
                    $keyPoint !== '' ? $keyPoint : null, $opsRelevance !== '' ? $opsRelevance : null,
                    $questionJson,
                ]
            );
            $_SESSION['flash_ok'] = 'Slide added.';
        }

        $this->redirect('/admin/slides/lesson/' . $lessonId);
    }

    /**
     * Delete one slide.
     */
    public function slideDelete(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/slides');
            return;
        }
        $slideId = (int) $this->param('slideId');
        $db = DB::instance();
        $slide = $db->queryOne('SELECT lesson_id FROM lesson_slides WHERE id = ?', [$slideId]);
        if (!$slide) {
            $_SESSION['flash_error'] = 'Slide not found.';
            $this->redirect('/admin/slides');
            return;
        }
        $db->execute('DELETE FROM lesson_slides WHERE id = ?', [$slideId]);
        $_SESSION['flash_ok'] = 'Slide deleted.';
        $this->redirect('/admin/slides/lesson/' . (int)$slide['lesson_id']);
    }

    /**
     * Move a slide up or down by swapping sort_order with its neighbour.
     */
    public function slideMove(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/slides');
            return;
        }
        $slideId   = (int) $this->param('slideId');
        $direction = (string) $this->input('direction', 'up');
        $db = DB::instance();

        $slide = $db->queryOne(
            'SELECT id, lesson_id, sort_order FROM lesson_slides WHERE id = ?',
            [$slideId]
        );
        if (!$slide) {
            $_SESSION['flash_error'] = 'Slide not found.';
            $this->redirect('/admin/slides');
            return;
        }

        $op  = $direction === 'down' ? '>' : '<';
        $ord = $direction === 'down' ? 'ASC' : 'DESC';

        $neighbour = $db->queryOne(
            "SELECT id, sort_order FROM lesson_slides
             WHERE lesson_id = ? AND sort_order {$op} ?
             ORDER BY sort_order {$ord}, id {$ord} LIMIT 1",
            [$slide['lesson_id'], $slide['sort_order']]
        );

        if ($neighbour) {
            // Swap sort_order values.
            $db->execute(
                'UPDATE lesson_slides SET sort_order = ? WHERE id = ?',
                [$neighbour['sort_order'], $slide['id']]
            );
            $db->execute(
                'UPDATE lesson_slides SET sort_order = ? WHERE id = ?',
                [$slide['sort_order'], $neighbour['id']]
            );
        }

        $this->redirect('/admin/slides/lesson/' . (int)$slide['lesson_id']);
    }

    /* ---------------- Settings ---------------- */

    public function settings(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $response->html($this->view('admin/settings', [
            'title'      => 'Settings',
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function settingsUpdate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/settings');
            return;
        }
        $_SESSION['flash_ok'] = 'Settings saved.';
        $this->redirect('/admin/settings');
    }

    /* ---------------- Pricing (Phase 6 stub) ---------------- */

    public function pricing(Request $request, Response $response): void
    {
        $this->requireAdmin();

        // Detect whether the subjects table has been migrated yet.
        $subjects = [];
        try {
            $subjects = DB::instance()->query(
                'SELECT id, slug, name, category, price_usd, is_published,
                        COALESCE(is_coming_soon, 0) AS is_coming_soon, sort_order
                   FROM subjects
                  ORDER BY category ASC, sort_order ASC'
            );
        } catch (\Throwable $e) {
            // Table not yet created — view will show migration notice.
            $subjects = [];
        }

        $response->html($this->view('admin/pricing', [
            'title'      => 'Pricing',
            'subjects'   => $subjects,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function pricingUpdate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/pricing');
            return;
        }

        $prices    = (array) ($_POST['price']     ?? []);
        $published = (array) ($_POST['published'] ?? []);
        $comingSoon= (array) ($_POST['coming_soon'] ?? []);

        $db = DB::instance();
        $count = 0;
        foreach ($prices as $id => $price) {
            $id    = (int) $id;
            $price = max(0.00, (float) $price);
            $isPub = isset($published[$id]) ? 1 : 0;
            $isCS  = isset($comingSoon[$id]) ? 1 : 0;
            if ($id <= 0) continue;
            try {
                $db->execute(
                    'UPDATE subjects
                        SET price_usd = ?, is_published = ?, is_coming_soon = ?
                      WHERE id = ?',
                    [$price, $isPub, $isCS, $id]
                );
                $count++;
            } catch (\Throwable $e) {
                // Skip rows that fail (e.g. column missing on partial migration).
            }
        }

        $_SESSION['flash_ok'] = $count > 0
            ? "Updated $count subject(s)."
            : 'No changes saved.';
        $this->redirect('/admin/pricing');
    }

    /* ---------------- Inquiries (contact_messages) ---------------- */

    public function contacts(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $statusFilter = (string) $this->input('status', 'all');
        $where = '';
        $params = [];
        if (in_array($statusFilter, ['new','read','replied','archived'], true)) {
            $where = 'WHERE status = ?';
            $params = [$statusFilter];
        }

        $messages = [];
        $unreadCount = 0;
        $statusCounts = ['new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0];
        $tableExists = false;
        try {
            // Check the table actually exists before querying. An empty
            // contact_messages table is still "exists" — the previous heuristic
            // (any messages or any non-zero status count) wrongly reported
            // missing-table when the inbox just had zero messages.
            $exists = DB::instance()->query("SHOW TABLES LIKE 'contact_messages'");
            $tableExists = !empty($exists);
            if ($tableExists) {
                // Try with `subject` column (Phase-1 migration applied); fall back if not.
                try {
                    $messages = DB::instance()->query(
                        "SELECT id, name, email, subject, message, status, ip, user_id, created_at, updated_at
                           FROM contact_messages
                           $where
                          ORDER BY created_at DESC
                          LIMIT 200",
                        $params
                    );
                } catch (\Throwable $eSubj) {
                    $messages = DB::instance()->query(
                        "SELECT id, name, email, message, status, ip, user_id, created_at, updated_at
                           FROM contact_messages
                           $where
                          ORDER BY created_at DESC
                          LIMIT 200",
                        $params
                    );
                }
                $rows = DB::instance()->query('SELECT status, COUNT(*) AS c FROM contact_messages GROUP BY status');
                foreach ($rows as $r) {
                    $statusCounts[(string) $r['status']] = (int) $r['c'];
                }
                $unreadCount = $statusCounts['new'] ?? 0;
            }
        } catch (\Throwable $e) {
            // contact_messages table not migrated yet — view shows the migration notice.
            $tableExists = false;
        }

        $response->html($this->view('admin/contacts', [
            'title'        => 'Inquiries',
            'messages'     => $messages,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
            'unreadCount'  => $unreadCount,
            'tableExists'  => $tableExists,
            'csrf_token'   => CSRF::generate(),
            'flashOk'      => $this->popFlash('flash_ok'),
            'flashError'   => $this->popFlash('flash_error'),
        ], 'admin'));
    }

    public function contactShow(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $id = (int) $this->param('id');
        $row = DB::instance()->queryOne(
            'SELECT cm.*, u.name AS user_name, u.email AS user_email
               FROM contact_messages cm
               LEFT JOIN users u ON u.id = cm.user_id
              WHERE cm.id = ?',
            [$id]
        );
        if (!$row) {
            $response->status(404);
            $response->html('<h1>Not found</h1>');
            return;
        }

        // Auto-mark as read on first view of a 'new' message.
        if (($row['status'] ?? '') === 'new') {
            DB::instance()->execute('UPDATE contact_messages SET status = "read" WHERE id = ?', [$id]);
            $row['status'] = 'read';
        }

        // Load reply thread (Phase-5 contact_replies table; absent → empty).
        $replies = [];
        try {
            $replies = DB::instance()->query(
                'SELECT cr.id, cr.body, cr.sent_at, cr.mail_status, cr.error,
                        u.name AS admin_name, u.email AS admin_email
                   FROM contact_replies cr
                   JOIN users u ON u.id = cr.admin_user_id
                  WHERE cr.contact_message_id = ?
                  ORDER BY cr.sent_at ASC',
                [$id]
            );
        } catch (\Throwable $e) { /* table not migrated */ }

        $response->html($this->view('admin/contact-show', [
            'title'      => 'Inquiry from ' . ($row['name'] ?? 'unknown'),
            'msg'        => $row,
            'replies'    => $replies,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'admin'));
    }

    /**
     * Send an admin reply to a contact_messages inquiry. Persists the reply
     * in contact_replies for the audit trail and updates the parent's
     * status to 'replied'. Mail send via EmailService::send() — if it fails
     * the reply row stays with mail_status='failed' for retry/diagnosis.
     */
    public function contactReply(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/contacts');
            return;
        }

        $id   = (int) $this->param('id');
        $body = trim((string) $this->input('body', ''));
        if (mb_strlen($body) < 10) {
            $_SESSION['flash_error'] = 'Reply must be at least 10 characters.';
            $this->redirect('/admin/contacts/' . $id);
            return;
        }

        $db  = DB::instance();
        $msg = $db->queryOne('SELECT id, name, email, subject FROM contact_messages WHERE id = ?', [$id]);
        if (!$msg) {
            $_SESSION['flash_error'] = 'Inquiry not found.';
            $this->redirect('/admin/contacts');
            return;
        }

        $admin = $this->user();
        $adminId    = (int) ($admin['id']    ?? 0);
        $adminName  = (string) ($admin['name']  ?? 'AviatorTutor team');
        $adminEmail = (string) ($admin['email'] ?? 'no-reply@aviatortutor.com');

        $subject = 'Re: ' . ($msg['subject'] ?? 'your AviatorTutor inquiry');
        $htmlBody = '<p>Hi ' . htmlspecialchars((string) $msg['name'], ENT_QUOTES, 'UTF-8') . ',</p>'
                  . '<p>' . nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8')) . '</p>'
                  . '<p>&mdash; ' . htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') . ', AviatorTutor</p>'
                  . '<hr><p style="font-size:12px;color:#666;">Your original message:</p>'
                  . '<blockquote style="border-left:3px solid #ddd;padding-left:12px;color:#555;font-size:13px;">'
                  . nl2br(htmlspecialchars((string) $msg['email'] . ' wrote:' . "\n" . ($msg['message'] ?? ''), ENT_QUOTES, 'UTF-8'))
                  . '</blockquote>';

        $sent = false;
        $error = null;
        try {
            $sent = \App\Services\EmailService::send(
                (string) $msg['email'],
                $subject,
                $htmlBody,
                ['reply_to' => $adminEmail]
            );
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        // Persist reply (audit trail) — even if mail() failed, so we can retry.
        try {
            $db->insert(
                'INSERT INTO contact_replies (contact_message_id, admin_user_id, body, mail_status, error)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $id,
                    $adminId,
                    $body,
                    $sent ? 'sent' : 'failed',
                    $error,
                ]
            );
            $db->execute(
                'UPDATE contact_messages SET status = "replied" WHERE id = ?',
                [$id]
            );
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Reply could not be saved (run the contact_replies migration). Error: ' . $e->getMessage();
            $this->redirect('/admin/contacts/' . $id);
            return;
        }

        $_SESSION['flash_ok'] = $sent
            ? 'Reply sent to ' . $msg['email'] . '.'
            : 'Reply saved but mail() reported a failure — see storage/logs/mail.log. The reply is stored in the thread so you can resend.';
        $this->redirect('/admin/contacts/' . $id);
    }

    public function contactUpdate(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/admin/contacts');
            return;
        }
        $id = (int) $this->param('id');
        $status = (string) $this->input('status', 'new');
        if (!in_array($status, ['new','read','replied','archived'], true)) {
            $status = 'read';
        }
        $notes = (string) $this->input('admin_notes', '');

        $rows = DB::instance()->execute(
            'UPDATE contact_messages SET status = ?, admin_notes = ? WHERE id = ?',
            [$status, $notes !== '' ? $notes : null, $id]
        );

        $_SESSION['flash_ok'] = $rows > 0 ? 'Inquiry updated.' : 'No changes saved.';
        $this->redirect('/admin/contacts/' . $id);
    }
}
