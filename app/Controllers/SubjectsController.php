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
        // Phase 4 v2: enrich each enrolled subject with progress + due
        // flashcards + the "continue here" deep-link to the most recent
        // lesson the learner touched. Defensive — if the joined tables
        // aren't there, the card just renders without the extra meta.
        foreach ($enrolled as &$row) {
            $row['progress_pct']    = $this->subjectProgressPct($userId, (string)$row['slug']);
            $row['flashcards_due']  = $this->subjectDueFlashcards($userId, (string)$row['slug']);
            $row['continue_url']    = $this->subjectContinueUrl($userId, (string)$row['slug']);
        }
        unset($row);

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
     * Deep-link target for the "Continue" CTA on a subject card.
     *
     * Priority order:
     *   1. Most recent in_progress lesson the user touched → its slide deck.
     *   2. Most recent completed lesson → its slide deck (re-review).
     *   3. First lesson of the subject's first system → fresh start.
     *   4. /systems as a generic fallback.
     */
    private function subjectContinueUrl(int $userId, string $subjectSlug): string
    {
        if ($subjectSlug !== 'q400-pack') return '/pricing';
        $db = DB::instance();

        try {
            $row = $db->queryOne(
                'SELECT l.id AS lesson_id, l.system_id
                   FROM user_progress up
                   JOIN lessons l ON l.id = up.lesson_id
                  WHERE up.user_id = ?
                    AND l.is_published = 1
                    AND up.status IN ("in_progress","completed")
                  ORDER BY FIELD(up.status,"in_progress","completed"), up.last_studied DESC
                  LIMIT 1',
                [$userId]
            );
            if ($row) {
                return '/study/' . (int)$row['system_id'] . '/lesson/' . (int)$row['lesson_id'];
            }
        } catch (\Throwable $e) { /* fall through */ }

        try {
            $row = $db->queryOne(
                'SELECT l.id AS lesson_id, l.system_id
                   FROM lessons l
                   JOIN systems s ON s.id = l.system_id
                  WHERE l.is_published = 1 AND s.is_published = 1
                  ORDER BY s.sort_order, l.sort_order
                  LIMIT 1'
            );
            if ($row) {
                return '/study/' . (int)$row['system_id'] . '/lesson/' . (int)$row['lesson_id'];
            }
        } catch (\Throwable $e) { /* fall through */ }

        return '/systems';
    }

    /**
     * Per-subject completion percentage. Counts published lessons in the
     * subject vs. user_progress rows with status='completed'. Returns 0 if
     * either table is unavailable.
     */
    private function subjectProgressPct(int $userId, string $subjectSlug): int
    {
        if ($subjectSlug !== 'q400-pack') return 0; // only Q400 has lessons today
        try {
            $row = DB::instance()->queryOne(
                'SELECT COUNT(DISTINCT l.id) AS total,
                        COUNT(DISTINCT CASE WHEN up.status = "completed" THEN l.id END) AS done
                   FROM lessons l
                   LEFT JOIN user_progress up
                          ON up.lesson_id = l.id AND up.user_id = ?
                  WHERE l.is_published = 1',
                [$userId]
            );
        } catch (\Throwable $e) { return 0; }
        $total = (int) ($row['total'] ?? 0);
        $done  = (int) ($row['done']  ?? 0);
        return $total > 0 ? (int) round(100 * $done / $total) : 0;
    }

    /**
     * Number of flashcards due (next_review_at <= NOW()) for the subject.
     */
    private function subjectDueFlashcards(int $userId, string $subjectSlug): int
    {
        if ($subjectSlug !== 'q400-pack') return 0;
        try {
            $row = DB::instance()->queryOne(
                'SELECT COUNT(*) AS n
                   FROM flashcards f
                   LEFT JOIN flashcard_reviews fr
                          ON fr.flashcard_id = f.id AND fr.user_id = ?
                  WHERE (f.status IS NULL OR f.status = "published")
                    AND (fr.next_review_at IS NULL OR fr.next_review_at <= NOW())',
                [$userId]
            );
        } catch (\Throwable $e) { return 0; }
        return (int) ($row['n'] ?? 0);
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
