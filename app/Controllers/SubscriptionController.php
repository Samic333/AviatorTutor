<?php
/**
 * Subscription Controller
 *
 * Handles activation-code redemption and the user's account/subscription page.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Services\ActivationCodeService;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    /** GET /redeem */
    public function showRedeem(Request $request, Response $response): void
    {
        $this->requireAuth();

        $userId = (int) $this->user()['id'];
        $current = SubscriptionService::current($userId);

        $data = [
            'title' => 'Activate your subscription',
            'csrf_token' => CSRF::generate(),
            'currentSubscription' => $current,
            'flashNotice' => self::popFlash('flash_notice'),
            'flashError'  => self::popFlash('flash_error'),
            'flashOk'     => self::popFlash('flash_ok'),
        ];
        $response->html($this->view('subscription/redeem', $data, 'app'));
    }

    /** POST /redeem */
    public function redeem(Request $request, Response $response): void
    {
        $this->requireAuth();

        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please refresh and try again.';
            $this->redirect('/redeem');
            return;
        }

        $userId = (int) $this->user()['id'];
        $rawCode = (string) $this->input('code', '');

        // Per-IP rate limit: max 5 attempts in any rolling 60 min
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if (self::isRateLimited($ip)) {
            $_SESSION['flash_error'] = 'Too many attempts. Please wait an hour and try again.';
            $this->redirect('/redeem');
            return;
        }

        $result = ActivationCodeService::redeem($rawCode, $userId, $ip);

        // Always log the attempt (successful or not) for the audit log
        \App\Core\DB::instance()->insert(
            'INSERT INTO audit_log (user_id, action, target_type, target_id, ip, payload_json)
             VALUES (?, "code.redeem", "activation_code", NULL, ?, ?)',
            [$userId, $ip, json_encode([
                'code'   => substr(ActivationCodeService::normalize($rawCode), 0, 4) . '****',
                'ok'     => $result['ok'],
                'error'  => $result['error'] ?? null,
            ])]
        );

        if ($result['ok']) {
            $_SESSION['flash_ok'] = 'Your subscription is active until ' . substr($result['expires_at'], 0, 10) . '.';
            $this->redirect('/dashboard');
            return;
        }

        $_SESSION['flash_error'] = $result['error'] ?? 'We could not activate that code.';
        $this->redirect('/redeem');
    }

    /** GET /account */
    public function account(Request $request, Response $response): void
    {
        $this->requireAuth();
        $userId = (int) $this->user()['id'];

        $data = [
            'title' => 'My account',
            'currentSubscription' => SubscriptionService::current($userId),
            'latestSubscription'  => SubscriptionService::latest($userId),
            'csrf_token' => CSRF::generate(),
            'flashOk'    => self::popFlash('flash_ok'),
            'flashError' => self::popFlash('flash_error'),
        ];
        $response->html($this->view('subscription/account', $data, 'app'));
    }

    private static function popFlash(string $key): ?string
    {
        $val = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $val;
    }

    private static function isRateLimited(?string $ip): bool
    {
        if (!$ip) return false;
        $count = (int) (\App\Core\DB::instance()->queryOne(
            'SELECT COUNT(*) AS n FROM audit_log
             WHERE action = "code.redeem"
               AND ip = ?
               AND created_at > DATE_SUB(NOW(), INTERVAL 60 MINUTE)',
            [$ip]
        )['n'] ?? 0);
        return $count >= 5;
    }
}
