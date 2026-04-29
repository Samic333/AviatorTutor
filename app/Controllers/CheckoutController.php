<?php
/**
 * Checkout Controller
 *
 * Initiates Stripe Checkout for a single subject, handles success/cancel
 * redirects, and processes the Stripe webhook to grant purchases.
 *
 * Routes:
 *   GET  /checkout/{slug}         show pre-checkout summary
 *   POST /checkout/{slug}         create Stripe session, redirect
 *   GET  /checkout/success        post-payment landing
 *   GET  /checkout/cancel         user-cancelled landing
 *   POST /stripe/webhook          server-to-server (CSRF-exempt)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;
use App\Services\PurchaseService;
use App\Services\StripeService;

class CheckoutController extends Controller
{
    public function show(Request $request, Response $response): void
    {
        $this->requireAuth();

        $slug    = (string) $this->param('slug');
        $subject = PurchaseService::findBySlug($slug);
        if (!$subject) {
            $response->status(404);
            $response->html($this->view('errors/404', ['title' => 'Not found'], 'marketing'));
            return;
        }

        $userId = (int) ($this->user()['id'] ?? 0);
        if (PurchaseService::hasAccess($userId, $slug)) {
            $_SESSION['flash_ok'] = 'You already own ' . $subject['name'] . '.';
            $this->redirect('/dashboard');
            return;
        }

        if (!empty($subject['is_coming_soon'])) {
            $_SESSION['flash_notice'] = $subject['name'] . ' is launching soon. Join the waitlist on the catalog.';
            $this->redirect('/aircraft/' . $slug);
            return;
        }

        $stripe = StripeService::config();

        $response->html($this->view('subscription/checkout', [
            'title'           => 'Checkout — ' . $subject['name'],
            'subject'         => $subject,
            'stripeReady'     => $stripe['configured'],
            'csrf_token'      => CSRF::generate(),
        ], 'marketing'));
    }

    public function create(Request $request, Response $response): void
    {
        $this->requireAuth();
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired.';
            $this->redirect('/pricing');
            return;
        }

        $slug    = (string) $this->param('slug');
        $subject = PurchaseService::findBySlug($slug);
        if (!$subject) {
            $response->status(404);
            return;
        }

        $userId = (int) ($this->user()['id'] ?? 0);
        if (PurchaseService::hasAccess($userId, $slug)) {
            $this->redirect('/dashboard');
            return;
        }

        $stripe = StripeService::config();
        if (!$stripe['configured']) {
            $_SESSION['flash_error'] = 'Online checkout is not configured yet. Please use an activation code or contact support.';
            $this->redirect('/checkout/' . $slug);
            return;
        }

        $cfg     = require BASE_PATH . '/config/app.php';
        $base    = rtrim((string) ($cfg['base_url'] ?? ''), '/');
        if ($base === '') {
            $base = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'aviatortutor.com');
        }

        $session = StripeService::createCheckoutSession(
            $userId,
            [
                'id'        => (int)    $subject['id'],
                'slug'      => (string) $subject['slug'],
                'name'      => (string) $subject['name'],
                'price_usd' => (float)  $subject['price_usd'],
            ],
            $base . '/checkout/success',
            $base . '/checkout/cancel'
        );

        if (!$session) {
            $_SESSION['flash_error'] = 'Could not start checkout. Please try again or contact support.';
            $this->redirect('/checkout/' . $slug);
            return;
        }

        // Stash session id so /checkout/success can verify on landing.
        $_SESSION['pending_checkout_session_id'] = $session['session_id'];
        $this->redirect($session['url']);
    }

    public function success(Request $request, Response $response): void
    {
        $this->requireAuth();
        $sessionId = (string) $request->input('session_id');
        $userId    = (int) ($this->user()['id'] ?? 0);

        // The webhook is the source of truth for granting access. This page
        // optimistically reads the session and shows the result; if the
        // webhook hasn't fired yet, we tell the user it'll appear shortly.
        $subject = null;
        $session = $sessionId !== '' ? StripeService::retrieveSession($sessionId) : null;
        if ($session && !empty($session['metadata']['subject_slug'])) {
            $subject = PurchaseService::findBySlug((string) $session['metadata']['subject_slug']);
        }

        unset($_SESSION['pending_checkout_session_id']);

        $response->html($this->view('subscription/success', [
            'title'   => 'Payment received',
            'subject' => $subject,
            'session' => $session,
        ], 'marketing'));
    }

    public function cancel(Request $request, Response $response): void
    {
        $_SESSION['flash_notice'] = 'Checkout cancelled — no charge was made.';
        $this->redirect('/pricing');
    }

    /**
     * Stripe webhook receiver. CSRF is bypassed at the route level since
     * Stripe doesn't have a session. Signature verification stands in.
     */
    public function webhook(Request $request, Response $response): void
    {
        $payload   = (string) file_get_contents('php://input');
        $sigHeader = (string) ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');

        // Log every webhook hit for debugging.
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
        @file_put_contents($logDir . '/stripe-webhook.log', "[" . date('c') . "] " . substr($payload, 0, 4000) . "\n----\n", FILE_APPEND);

        if (!StripeService::verifyWebhookSignature($payload, $sigHeader)) {
            $response->status(400);
            $response->json(['error' => 'invalid_signature']);
            return;
        }

        $event = json_decode($payload, true);
        $type  = (string) ($event['type'] ?? '');

        if ($type === 'checkout.session.completed' || $type === 'checkout.session.async_payment_succeeded') {
            $session    = $event['data']['object'] ?? [];
            $userId     = (int) ($session['metadata']['user_id']    ?? 0);
            $subjectId  = (int) ($session['metadata']['subject_id'] ?? 0);
            $intentId   = (string) ($session['payment_intent']      ?? '');
            $amountPaid = (float) (($session['amount_total']         ?? 0) / 100);

            if ($userId > 0 && $subjectId > 0) {
                PurchaseService::grant($userId, $subjectId, 'stripe', [
                    'stripe_payment_intent_id' => $intentId !== '' ? $intentId : null,
                    'amount_paid_usd'          => $amountPaid,
                ]);
            }
        }

        $response->json(['received' => true]);
    }
}
