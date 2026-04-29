<?php
/**
 * Onboarding Controller
 *
 * 2-step wizard shown after a new user verifies their email:
 *   1. /onboarding/aircraft  — pick an aircraft pack
 *   2. /onboarding/subjects  — pick aviation subjects of interest
 * After step 2 the user lands on /pricing with their selections highlighted.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;
use App\Services\PurchaseService;

class OnboardingController extends Controller
{
    public function aircraft(Request $request, Response $response): void
    {
        $this->requireAuth();

        $aircraftPacks = [];
        try {
            $aircraftPacks = PurchaseService::catalog('aircraft_pack');
        } catch (\Throwable $e) {
            // subjects table not yet migrated — fall back to aircrafts table.
            $aircraftPacks = DB::instance()->query(
                'SELECT slug, short_name AS name, NULL AS short_blurb, 29 AS price_usd,
                        (status != "live") AS is_coming_soon
                   FROM aircrafts WHERE is_published = 1 ORDER BY sort_order ASC'
            );
        }

        $response->html($this->view('onboarding/aircraft', [
            'title'         => 'Pick your aircraft',
            'aircraftPacks' => $aircraftPacks,
            'csrf_token'    => CSRF::generate(),
        ], 'marketing'));
    }

    public function setAircraft(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/onboarding/aircraft');
            return;
        }

        $slug = trim((string) $request->input('subject_slug'));
        if ($slug !== '') {
            $_SESSION['onboarding_aircraft_slug'] = $slug;
        }

        $this->redirect('/onboarding/subjects');
    }

    public function subjects(Request $request, Response $response): void
    {
        $this->requireAuth();

        $airline   = [];
        $aviation  = [];
        try {
            $airline  = PurchaseService::catalog('airline_interview');
            $aviation = PurchaseService::catalog('aviation_subject');
        } catch (\Throwable $e) {
            // not migrated yet
        }

        $response->html($this->view('onboarding/subjects', [
            'title'      => 'Pick your subjects',
            'airline'    => $airline,
            'aviation'   => $aviation,
            'aircraftSlug' => (string) ($_SESSION['onboarding_aircraft_slug'] ?? ''),
            'csrf_token' => CSRF::generate(),
        ], 'marketing'));
    }

    public function setSubjects(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/onboarding/subjects');
            return;
        }

        $picked = (array) ($_POST['subjects'] ?? []);
        $picked = array_values(array_unique(array_filter(array_map('strval', $picked))));
        $_SESSION['onboarding_subject_slugs'] = $picked;

        // Land on pricing with the selections in session for highlighting.
        $_SESSION['flash_ok'] = 'Great picks. Choose what to buy first below.';
        $this->redirect('/pricing');
    }
}
