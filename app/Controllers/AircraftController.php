<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Services\AircraftService;

/**
 * Phase 10b — public aircraft catalog and per-aircraft detail pages.
 *
 *   GET  /aircraft               full catalog grid
 *   GET  /aircraft/{slug}        detail page (live → "Start studying";
 *                                coming-soon → "Notify me" form)
 *   POST /aircraft/{slug}/notify lead-signup capture
 *   POST /aircraft/{slug}/study  (logged-in only) set preferred aircraft
 *                                and redirect to /dashboard
 */
class AircraftController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $groups = AircraftService::groupedByCategory();
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'ItemList',
            'name'     => 'AviatorTutor aircraft catalog',
            'itemListElement' => array_values(array_map(
                static fn(array $a, int $i) => [
                    '@type'    => 'ListItem',
                    'position' => $i + 1,
                    'name'     => $a['name'],
                    'url'      => 'https://aviatortutor.com/aircraft/' . $a['slug'],
                ],
                AircraftService::all(),
                array_keys(AircraftService::all())
            )),
        ];

        $response->html($this->view('marketing/aircraft', [
            'title'       => 'Aircraft catalog — AviatorTutor',
            'description' => 'Every aircraft AviatorTutor covers — from the Q400 (live) to 737, 777, 787, A320, A330, CRJ, Embraer, Cessna and more.',
            'groups'      => $groups,
            'jsonLd'      => $jsonLd,
        ], 'marketing'));
    }

    public function show(Request $request, Response $response): void
    {
        $slug = (string) $this->param('slug', '');
        $aircraft = AircraftService::bySlug($slug);
        if (!$aircraft) {
            $response->status(404);
            $response->html($this->view('errors/marketing-404', [
                'title'   => 'Aircraft not found — AviatorTutor',
                'message' => 'We don\'t have a module for that aircraft yet.',
            ], 'marketing'));
            return;
        }

        $jsonLd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            'name'        => $aircraft['name'] . ' systems study',
            'description' => $aircraft['description'] ?? $aircraft['tagline'],
            'provider'    => [
                '@type' => 'Organization',
                'name'  => 'AviatorTutor',
                'url'   => 'https://aviatortutor.com',
            ],
        ];

        // Pull related panels (tier-2 hotspots) for the live cockpit page.
        $panels = [];
        if ($aircraft['status'] === 'live') {
            $panels = DB::instance()->query(
                'SELECT p.*, s.slug AS system_slug, s.name AS system_name
                 FROM aircraft_panels p
                 LEFT JOIN systems s ON s.id = p.system_id
                 WHERE p.aircraft_id = ?
                 ORDER BY p.sort_order, p.id',
                [(int) $aircraft['id']]
            );
        }

        $isLogged    = !empty($_SESSION['auth_user']);
        $isActiveSub = false;
        if ($isLogged) {
            $isAdmin = ($_SESSION['auth_user']['role'] ?? '') === 'admin';
            $isActiveSub = $isAdmin || \App\Services\SubscriptionService::hasActive((int) $_SESSION['auth_user']['id']);
        }

        $response->html($this->view('marketing/aircraft-show', [
            'title'        => $aircraft['name'] . ' — AviatorTutor',
            'description'  => $aircraft['tagline'] ?? '',
            'aircraft'     => $aircraft,
            'panels'       => $panels,
            'isLogged'     => $isLogged,
            'isActiveSub'  => $isActiveSub,
            'csrf_token'   => CSRF::generate(),
            'flashOk'      => $this->popFlash('flash_ok'),
            'flashError'   => $this->popFlash('flash_error'),
            'jsonLd'       => $jsonLd,
        ], 'marketing'));
    }

    /**
     * Capture a lead-signup for a coming-soon aircraft. Reuses the
     * lead_signups table created in phase 2 (originally for /coming-soon/*).
     */
    public function notify(Request $request, Response $response): void
    {
        $slug = (string) $this->param('slug', '');
        $aircraft = AircraftService::bySlug($slug);
        if (!$aircraft) {
            $response->status(404);
            $response->text('not found');
            return;
        }
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/aircraft/' . $slug);
            return;
        }
        $email = trim((string) $this->input('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please provide a valid email address.';
            $this->redirect('/aircraft/' . $slug);
            return;
        }

        DB::instance()->execute(
            'INSERT INTO lead_signups (email, requested_module_slug, ip, user_agent)
             VALUES (?, ?, ?, ?)',
            [
                $email,
                $slug,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 250),
            ]
        );
        DB::instance()->execute(
            'UPDATE aircrafts SET waitlist_count = waitlist_count + 1 WHERE id = ?',
            [(int) $aircraft['id']]
        );

        $_SESSION['flash_ok'] = 'Thanks — we\'ll email you the day the ' . $aircraft['short_name'] . ' module goes live.';
        $this->redirect('/aircraft/' . $slug);
    }

    /**
     * Logged-in user clicks "Start studying" on /aircraft/q400.
     * Sets users.preferred_aircraft_id and redirects to /dashboard.
     */
    public function startStudying(Request $request, Response $response): void
    {
        $this->requireAuth();
        $slug = (string) $this->param('slug', '');
        $aircraft = AircraftService::bySlug($slug);
        if (!$aircraft) {
            $response->status(404);
            $response->text('not found');
            return;
        }
        if (!in_array($aircraft['status'], ['live', 'beta'], true)) {
            $_SESSION['flash_error'] = $aircraft['short_name'] . ' isn\'t live yet — join the waitlist for an alert.';
            $this->redirect('/aircraft/' . $slug);
            return;
        }
        $user = $this->user();
        AircraftService::setPreferredForUser((int) $user['id'], (int) $aircraft['id']);
        $_SESSION['flash_ok'] = 'Now studying ' . $aircraft['short_name'] . '.';
        $this->redirect('/dashboard');
    }
}
