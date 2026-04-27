<?php
/**
 * Phase 10c — platform admin dashboard + ops pages.
 *
 *   /admin                 live metrics dashboard
 *   /admin/codes           list + generate + revoke activation codes
 *   /admin/codes/generate  POST: generate N codes
 *   /admin/codes/revoke    POST: revoke a single code
 *   /admin/users           list users + role
 *   /admin/leads           list lead-signups
 *   /admin/aircrafts       list catalog + status edits
 *   (existing stubs preserved for content/import/flashcards/quizzes)
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

class AdminController extends Controller
{
    public function dashboard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $metrics = AdminMetricsService::dashboard();
        $response->html($this->view('admin/dashboard', [
            'title'   => 'Admin dashboard — AviatorTutor',
            'metrics' => $metrics,
        ]));
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
            'title'        => 'Activation codes — Admin',
            'codes'        => $codes,
            'summary'      => $summary,
            'statusFilter' => $statusFilter,
            'csrf_token'   => CSRF::generate(),
            'flashOk'      => $this->popFlash('flash_ok'),
            'flashError'   => $this->popFlash('flash_error'),
        ]));
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
        $_SESSION['flash_ok'] = "Generated {$count} codes ({$days}-day {$plan}). " . implode(', ', array_slice(array_map([ActivationCodeService::class, 'format'], $codes), 0, 5)) . (count($codes) > 5 ? '…' : '');
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
                    a.short_name AS preferred_aircraft,
                    (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id AND s.status="active" AND s.expires_at > NOW()) AS sub_active
             FROM users u
             LEFT JOIN aircrafts a ON a.id = u.preferred_aircraft_id
             ORDER BY u.id DESC
             LIMIT 200'
        );
        $response->html($this->view('admin/users', [
            'title'      => 'Users — Admin',
            'users'      => $users,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ]));
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
        // Prevent demoting yourself out of admin (lockout safety)
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
            'title'    => 'Lead signups — Admin',
            'leads'    => $leads,
            'byModule' => $byModule,
        ]));
    }

    /* ---------------- Aircrafts ---------------- */

    public function aircrafts(Request $request, Response $response): void
    {
        $this->requireAdmin();
        AircraftService::refreshWaitlistCounts();
        $rows = AircraftService::all();
        $response->html($this->view('admin/aircrafts', [
            'title'      => 'Aircraft catalog — Admin',
            'aircrafts'  => $rows,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ]));
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

    /* ---------------- Existing stubs (preserved as-is) ---------------- */

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
            'title'      => 'Content — Admin',
            'systems'    => $systems,
            'aircrafts'  => $aircrafts,
            'aircraftId' => $aircraftId,
            'csrf_token' => CSRF::generate(),
        ]));
    }

    public function createContent(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
    }

    public function import(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $response->html($this->view('admin/import', [
            'title' => 'Import content — Admin',
            'csrf_token' => CSRF::generate(),
        ]));
    }

    public function processImport(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        if (!$this->hasFile('import_file')) { $response->json(['error' => 'No file'], 422); return; }
        $response->json(['success' => true, 'message' => 'Stub']);
    }

    public function flashcards(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $cards = DB::instance()->query(
            'SELECT f.id, f.question, f.answer, f.difficulty, s.name AS system_name
             FROM flashcards f LEFT JOIN systems s ON s.id = f.system_id
             ORDER BY f.id DESC LIMIT 100'
        );
        $response->html($this->view('admin/flashcards', [
            'title' => 'Flashcards — Admin',
            'cards' => $cards,
            'csrf_token' => CSRF::generate(),
        ]));
    }

    public function createFlashcard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
    }

    public function quizzes(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $quizzes = DB::instance()->query(
            'SELECT q.id, q.title, q.difficulty, s.name AS system_name,
                    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) AS qcount
             FROM quizzes q LEFT JOIN systems s ON s.id = q.system_id
             ORDER BY q.id DESC LIMIT 100'
        );
        $response->html($this->view('admin/quizzes', [
            'title' => 'Quizzes — Admin',
            'quizzes' => $quizzes,
            'csrf_token' => CSRF::generate(),
        ]));
    }

    public function createQuiz(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
    }
}
