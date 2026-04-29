<?php
/**
 * Stripe Service — wrapper around Stripe Checkout (hosted).
 *
 * Implementation uses the Stripe REST API directly via cURL so we don't
 * require composer dependencies. If the Stripe SDK is later added, swap
 * the cURL calls for $stripeClient->checkout->sessions->create(...) etc.
 *
 * Required config keys (config/app.local.php):
 *   'stripe_secret_key'      — sk_live_… or sk_test_…
 *   'stripe_publishable_key' — pk_live_… or pk_test_…
 *   'stripe_webhook_secret'  — whsec_…
 */

declare(strict_types=1);

namespace App\Services;

class StripeService
{
    /** @return array{configured: bool, secret_key?: string, publishable_key?: string, webhook_secret?: string} */
    public static function config(): array
    {
        $cfg = require BASE_PATH . '/config/app.php';
        $secret = (string) ($cfg['stripe_secret_key']      ?? '');
        $pub    = (string) ($cfg['stripe_publishable_key'] ?? '');
        $whsec  = (string) ($cfg['stripe_webhook_secret']  ?? '');

        return [
            'configured'      => $secret !== '' && $pub !== '',
            'secret_key'      => $secret,
            'publishable_key' => $pub,
            'webhook_secret'  => $whsec,
        ];
    }

    /**
     * Create a Stripe Checkout Session for a subject.
     * Returns the session URL the user should be redirected to, or null on failure.
     *
     * @param array{id:int,slug:string,name:string,price_usd:float} $subject
     */
    public static function createCheckoutSession(int $userId, array $subject, string $successUrl, string $cancelUrl): ?array
    {
        $cfg = self::config();
        if (!$cfg['configured']) {
            return null;
        }

        $unitAmount = (int) round(((float) $subject['price_usd']) * 100); // cents

        $params = [
            'mode'                                     => 'payment',
            'success_url'                              => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'                               => $cancelUrl,
            'client_reference_id'                      => (string) $userId,
            'metadata[user_id]'                        => (string) $userId,
            'metadata[subject_id]'                     => (string) $subject['id'],
            'metadata[subject_slug]'                   => (string) $subject['slug'],
            'line_items[0][quantity]'                  => '1',
            'line_items[0][price_data][currency]'      => 'usd',
            'line_items[0][price_data][unit_amount]'   => (string) $unitAmount,
            'line_items[0][price_data][product_data][name]'        => (string) $subject['name'],
            'line_items[0][price_data][product_data][description]' => 'AviatorTutor — ' . $subject['name'],
        ];

        $response = self::request('POST', '/v1/checkout/sessions', $params);
        if (!$response || empty($response['id']) || empty($response['url'])) {
            return null;
        }

        return [
            'session_id' => (string) $response['id'],
            'url'        => (string) $response['url'],
        ];
    }

    /** Verify a webhook signature header. Returns true if the body matches the signature. */
    public static function verifyWebhookSignature(string $payload, string $sigHeader): bool
    {
        $cfg = self::config();
        $secret = $cfg['webhook_secret'] ?? '';
        if ($secret === '' || $sigHeader === '') return false;

        $items = [];
        foreach (explode(',', $sigHeader) as $part) {
            [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
            $items[$k][] = $v;
        }
        $timestamp = $items['t'][0] ?? null;
        $signatures = $items['v1'] ?? [];
        if ($timestamp === null || empty($signatures)) return false;

        $signed = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signed, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) {
                // Tolerance: 5 minutes
                if (abs(time() - (int) $timestamp) <= 300) return true;
            }
        }
        return false;
    }

    /** Retrieve a Checkout Session by id (for /checkout/success page lookups). */
    public static function retrieveSession(string $sessionId): ?array
    {
        $cfg = self::config();
        if (!$cfg['configured']) return null;
        return self::request('GET', '/v1/checkout/sessions/' . urlencode($sessionId));
    }

    /** Low-level HTTP request to api.stripe.com using the secret key. */
    private static function request(string $method, string $path, array $params = []): ?array
    {
        $cfg = self::config();
        $url = 'https://api.stripe.com' . $path;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $cfg['secret_key'],
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code >= 400) return null;
        $decoded = json_decode((string) $body, true);
        return is_array($decoded) ? $decoded : null;
    }
}
