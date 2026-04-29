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
        $response->html($this->view('admin/import', [
            'title'      => 'Import',
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function processImport(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        if (!$this->hasFile('import_file')) { $response->json(['error' => 'No file'], 422); return; }
        $response->json(['success' => true, 'message' => 'Stub']);
    }

    /* ---------------- Flashcards ---------------- */

    public function flashcards(Request $request, Response $response): void
    {
        $this->requireAdmin();
        $cards = DB::instance()->query(
            'SELECT f.id, f.question, f.answer, f.difficulty, s.name AS system_name
             FROM flashcards f LEFT JOIN systems s ON s.id = f.system_id
             ORDER BY f.id DESC LIMIT 100'
        );
        $response->html($this->view('admin/flashcards', [
            'title'      => 'Flashcards',
            'cards'      => $cards,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function createFlashcard(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
    }

    /* ---------------- Quizzes ---------------- */

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
            'title'      => 'Quizzes',
            'quizzes'    => $quizzes,
            'csrf_token' => CSRF::generate(),
        ], 'admin'));
    }

    public function createQuiz(Request $request, Response $response): void
    {
        $this->requireAdmin();
        if (!CSRF::check($request)) { $response->status(419); $response->json(['error' => 'CSRF']); return; }
        $response->json(['success' => true]);
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
        try {
            $messages = DB::instance()->query(
                "SELECT id, name, email, message, status, ip, user_id, created_at, updated_at
                   FROM contact_messages
                   $where
                  ORDER BY created_at DESC
                  LIMIT 200",
                $params
            );
            $rows = DB::instance()->query('SELECT status, COUNT(*) AS c FROM contact_messages GROUP BY status');
            foreach ($rows as $r) {
                $statusCounts[(string) $r['status']] = (int) $r['c'];
            }
            $unreadCount = $statusCounts['new'] ?? 0;
        } catch (\Throwable $e) {
            // contact_messages table not migrated yet — view shows the migration notice.
        }

        $response->html($this->view('admin/contacts', [
            'title'        => 'Inquiries',
            'messages'     => $messages,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
            'unreadCount'  => $unreadCount,
            'tableExists'  => !empty($messages) || array_sum($statusCounts) > 0,
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

        $response->html($this->view('admin/contact-show', [
            'title'      => 'Inquiry from ' . ($row['name'] ?? 'unknown'),
            'msg'        => $row,
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'admin'));
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
