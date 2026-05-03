<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Services\PurchaseService;
use App\Services\SubscriptionService;

/**
 * My Subjects — authenticated learner view of enrolled subjects.
 *
 *   GET  /my-subjects          — enrolled cards + Add Subject tile
 *   POST /my-subjects/request  — pilot requests a new subject (admin notified)
 *
 * Replaces the old "My Aircraft" nav link that pointed at the public marketing
 * catalog. The nav swap is gated by the nav_my_subjects feature flag so this
 * page can ship without the sidebar change going live.
 */
class SubjectsController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $user   = $this->user();
        $userId = (int) ($user['id'] ?? 0);

        $enrolled = $this->loadEnrolledSubjects($userId);

        // Pending requests so we don't render the same Add Subject CTA twice.
        $pendingRequests = [];
        try {
            $pendingRequests = DB::instance()->query(
                'SELECT id, requested_subject, subject_slug, status, created_at
                   FROM subject_requests
                  WHERE user_id = ?
                    AND status IN ("pending","quoted")
                  ORDER BY created_at DESC',
                [$userId]
            );
        } catch (\Throwable $e) {
            // Table not migrated yet — render without the pending block.
        }

        $availableToAdd = $this->loadAvailableSubjects($enrolled, $pendingRequests);

        $response->html($this->view('subjects/index', [
            'title'           => 'My Subjects',
            'currentPath'     => '/my-subjects',
            'enrolled'        => $enrolled,
            'pendingRequests' => $pendingRequests,
            'availableToAdd'  => $availableToAdd,
            'csrf_token'      => CSRF::generate(),
            'flashOk'         => $this->popFlash('flash_ok'),
            'flashError'      => $this->popFlash('flash_error'),
        ], 'pilot'));
    }

    public function requestSubject(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/my-subjects');
            return;
        }

        $user   = $this->user();
        $userId = (int) ($user['id'] ?? 0);

        $slug  = trim((string) $this->input('subject_slug', ''));
        $name  = trim((string) $this->input('subject_name', ''));
        $notes = trim((string) $this->input('notes', ''));

        if ($slug === '' && $name === '') {
            $_SESSION['flash_error'] = 'Pick a subject or describe what you want to study.';
            $this->redirect('/my-subjects');
            return;
        }

        // If a slug was given, look up the friendly name from the catalog.
        if ($slug !== '' && $name === '') {
            try {
                $row = DB::instance()->queryOne(
                    'SELECT name FROM subjects WHERE slug = ? LIMIT 1',
                    [$slug]
                );
                $name = (string) ($row['name'] ?? $slug);
            } catch (\Throwable $e) {
                $name = $slug;
            }
        }

        try {
            DB::instance()->execute(
                'INSERT INTO subject_requests (user_id, requested_subject, subject_slug, notes)
                 VALUES (?, ?, ?, ?)',
                [$userId, $name, $slug !== '' ? $slug : null, $notes !== '' ? $notes : null]
            );
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = "We couldn't save your request — please try again in a moment.";
            $this->redirect('/my-subjects');
            return;
        }

        // Best-effort admin email; never block the request on mail failure.
        try {
            $cfg     = require BASE_PATH . '/config/app.php';
            $adminTo = (string) ($cfg['contact_to'] ?? 'samickenya@gmail.com');
            $body    = '<p>New subject request:</p>'
                     . '<ul>'
                     .   '<li><strong>Subject:</strong> ' . htmlspecialchars($name) . '</li>'
                     .   '<li><strong>Slug:</strong> ' . htmlspecialchars($slug !== '' ? $slug : '(custom)') . '</li>'
                     .   '<li><strong>From:</strong> ' . htmlspecialchars((string)($user['name'] ?? '')) . ' (' . htmlspecialchars((string)($user['email'] ?? '')) . ', user #' . $userId . ')</li>'
                     .   '<li><strong>Notes:</strong> ' . nl2br(htmlspecialchars($notes !== '' ? $notes : '(none)')) . '</li>'
                     . '</ul>';
            \App\Services\EmailService::send(
                $adminTo,
                'AviatorTutor — subject request from ' . ($user['name'] ?? $user['email'] ?? 'a pilot'),
                $body
            );
        } catch (\Throwable $e) {
            // Mail unavailable — admin will see the request in /admin/subject-requests later.
        }

        $_SESSION['flash_ok'] = "Request sent — we'll send you a quote.";
        $this->redirect('/my-subjects');
    }

    /**
     * Subjects the learner already has access to. Returns rows with at least:
     *   slug, name, price_usd, color_hex, icon_slug, source ('purchase'|'subscription')
     *
     * @return array<int,array<string,mixed>>
     */
    private function loadEnrolledSubjects(int $userId): array
    {
        if ($userId === 0) return [];
        $db = DB::instance();
        $out = [];

        // 1. Purchased subjects.
        try {
            $rows = $db->query(
                'SELECT s.slug, s.name, s.short_blurb, s.price_usd, s.color_hex,
                        s.icon_slug, s.category, p.granted_at
                   FROM purchases p
                   JOIN subjects s ON s.id = p.subject_id
                  WHERE p.user_id = ?
                    AND p.status = "active"
                    AND (p.expires_at IS NULL OR p.expires_at > NOW())
                  ORDER BY p.granted_at DESC',
                [$userId]
            );
            foreach ($rows as $r) {
                $r['source'] = 'purchase';
                $out[(string)$r['slug']] = $r;
            }
        } catch (\Throwable $e) {
            // subjects/purchases not migrated — skip.
        }

        // 2. Legacy: flat-rate subscribers get q400-pack via SubscriptionService.
        if (!isset($out['q400-pack']) && SubscriptionService::hasActive($userId)) {
            try {
                $row = $db->queryOne(
                    'SELECT slug, name, short_blurb, price_usd, color_hex, icon_slug, category
                       FROM subjects WHERE slug = "q400-pack" LIMIT 1'
                );
                if ($row) {
                    $row['source'] = 'subscription';
                    $row['granted_at'] = null;
                    $out['q400-pack'] = $row;
                }
            } catch (\Throwable $e) {
                // No subjects table — synthesise a minimal Q400 card so the
                // page isn't empty for legacy users.
                $out['q400-pack'] = [
                    'slug'        => 'q400-pack',
                    'name'        => 'Q400 Aircraft Pack',
                    'short_blurb' => 'Bombardier Q400 systems study.',
                    'price_usd'   => 29,
                    'color_hex'   => '#38BDF8',
                    'icon_slug'   => 'plane',
                    'category'    => 'aircraft_pack',
                    'source'      => 'subscription',
                    'granted_at'  => null,
                ];
            }
        }

        // 3. Admins see Q400 even without a sub, so they can QA the flow.
        if (($this->user()['role'] ?? '') === 'admin' && !isset($out['q400-pack'])) {
            try {
                $row = $db->queryOne(
                    'SELECT slug, name, short_blurb, price_usd, color_hex, icon_slug, category
                       FROM subjects WHERE slug = "q400-pack" LIMIT 1'
                );
                if ($row) {
                    $row['source'] = 'admin';
                    $row['granted_at'] = null;
                    $out['q400-pack'] = $row;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return array_values($out);
    }

    /**
     * Catalog subjects the learner can request. Excludes already-enrolled and
     * already-pending. Falls back to an empty list if subjects aren't seeded.
     *
     * @param array<int,array<string,mixed>> $enrolled
     * @param array<int,array<string,mixed>> $pendingRequests
     * @return array<int,array<string,mixed>>
     */
    private function loadAvailableSubjects(array $enrolled, array $pendingRequests): array
    {
        $exclude = [];
        foreach ($enrolled as $e)        $exclude[(string)$e['slug']] = true;
        foreach ($pendingRequests as $r) if (!empty($r['subject_slug'])) $exclude[(string)$r['subject_slug']] = true;

        try {
            $rows = DB::instance()->query(
                'SELECT slug, name, short_blurb, price_usd, color_hex, icon_slug, category, is_coming_soon
                   FROM subjects
                  WHERE is_published = 1
                  ORDER BY sort_order ASC, name ASC'
            );
        } catch (\Throwable $e) {
            return [];
        }

        $out = [];
        foreach ($rows as $r) {
            if (isset($exclude[(string)$r['slug']])) continue;
            $out[] = $r;
        }
        return $out;
    }
}
