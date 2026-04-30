<?php
/**
 * AI Content Service — wrapper around the Anthropic Messages API.
 *
 * Talks to https://api.anthropic.com/v1/messages directly via cURL so we
 * don't pull in a Composer dependency. If the SDK is added later, swap
 * the cURL call in send() for $client->messages->create(...).
 *
 * Phase 2 scope (smoke test):
 *   - generate($systemPrompt, $userText, $opts)
 *   - returns the parsed JSON response or a structured error
 *
 * Phase 3+ will add:
 *   - prompt caching (system_prompt cache_control)
 *   - chunked outline → per-chunk fill flow
 *   - JSON Schema validation + repair pass
 *
 * Required config keys (config/app.local.php):
 *   'anthropic_api_key'        — sk-ant-…   (REQUIRED)
 *   'anthropic_api_url'        — defaults to https://api.anthropic.com/v1/messages
 *   'anthropic_api_version'    — defaults to 2023-06-01
 *   'anthropic_model_chunk'    — defaults to claude-sonnet-4-5
 *   'anthropic_model_outline'  — defaults to claude-opus-4-5
 *   'anthropic_max_tokens'     — defaults to 16000
 *   'anthropic_timeout'        — defaults to 120 seconds
 */

declare(strict_types=1);

namespace App\Services;

class AIContentService
{
    /** @return array{configured:bool, api_key:string, api_url:string, api_version:string, model_outline:string, model_chunk:string, max_tokens:int, timeout:int} */
    public static function config(): array
    {
        $cfg = require BASE_PATH . '/config/app.php';

        $key = (string) ($cfg['anthropic_api_key'] ?? '');

        return [
            'configured'    => $key !== '',
            'api_key'       => $key,
            'api_url'       => (string) ($cfg['anthropic_api_url']     ?? 'https://api.anthropic.com/v1/messages'),
            'api_version'   => (string) ($cfg['anthropic_api_version'] ?? '2023-06-01'),
            'model_outline' => (string) ($cfg['anthropic_model_outline'] ?? 'claude-opus-4-5'),
            'model_chunk'   => (string) ($cfg['anthropic_model_chunk']   ?? 'claude-sonnet-4-5'),
            'max_tokens'    => (int)    ($cfg['anthropic_max_tokens']    ?? 16000),
            'timeout'       => (int)    ($cfg['anthropic_timeout']       ?? 120),
        ];
    }

    /**
     * Send a single Messages API call.
     *
     * @param string $systemPrompt   Static instructions; this is what we'll
     *                                cache in Phase 3.
     * @param string $userText       The dynamic per-call payload (PDF
     *                                excerpt, etc.).
     * @param array  $opts           {
     *   model?: string              Override the configured model
     *   max_tokens?: int            Override max_tokens
     *   temperature?: float         Default 0.2 — keep low for structured output
     * }
     *
     * @return array{
     *   ok:bool,
     *   text?:string,                Raw model text (concatenated content blocks)
     *   stop_reason?:string,
     *   model?:string,
     *   usage?:array,
     *   raw?:array,                  Full decoded response body
     *   http_code?:int,
     *   error?:string,
     *   error_detail?:string,
     *   request_ms?:int,
     * }
     */
    public static function generate(string $systemPrompt, string $userText, array $opts = []): array
    {
        $cfg = self::config();
        if (!$cfg['configured']) {
            return [
                'ok'    => false,
                'error' => 'not_configured',
                'error_detail' => 'anthropic_api_key is not set in config/app.local.php',
            ];
        }

        $model       = (string) ($opts['model']        ?? $cfg['model_chunk']);
        $maxTokens   = (int)    ($opts['max_tokens']   ?? $cfg['max_tokens']);
        $temperature = (float)  ($opts['temperature']  ?? 0.2);

        $payload = [
            'model'       => $model,
            'max_tokens'  => $maxTokens,
            'temperature' => $temperature,
            'system'      => $systemPrompt,
            'messages'    => [[
                'role'    => 'user',
                'content' => $userText,
            ]],
        ];

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            return ['ok' => false, 'error' => 'json_encode_failed'];
        }

        $startedAt = microtime(true);

        $ch = curl_init($cfg['api_url']);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $cfg['timeout'],
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . $cfg['api_key'],
                'anthropic-version: ' . $cfg['api_version'],
            ],
        ]);

        $raw      = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        $requestMs = (int) round((microtime(true) - $startedAt) * 1000);

        if ($raw === false) {
            return [
                'ok'           => false,
                'error'        => 'curl_error',
                'error_detail' => $curlErr,
                'http_code'    => $httpCode,
                'request_ms'   => $requestMs,
            ];
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return [
                'ok'           => false,
                'error'        => 'invalid_json_response',
                'error_detail' => substr((string) $raw, 0, 600),
                'http_code'    => $httpCode,
                'request_ms'   => $requestMs,
            ];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'ok'           => false,
                'error'        => 'api_error',
                'error_detail' => $decoded['error']['message'] ?? 'HTTP ' . $httpCode,
                'http_code'    => $httpCode,
                'request_ms'   => $requestMs,
                'raw'          => $decoded,
            ];
        }

        // Concatenate text content blocks (non-text blocks ignored for now)
        $text = '';
        foreach ((array) ($decoded['content'] ?? []) as $block) {
            if (is_array($block) && ($block['type'] ?? '') === 'text') {
                $text .= (string) ($block['text'] ?? '');
            }
        }

        return [
            'ok'          => true,
            'text'        => $text,
            'stop_reason' => (string) ($decoded['stop_reason'] ?? ''),
            'model'       => (string) ($decoded['model']       ?? $model),
            'usage'       => (array)  ($decoded['usage']       ?? []),
            'raw'         => $decoded,
            'http_code'   => $httpCode,
            'request_ms'  => $requestMs,
        ];
    }

    /**
     * Helper: try to extract a JSON object from a model response that may
     * have been wrapped in ```json fences or chatty preamble. Returns the
     * decoded array, or null on failure.
     */
    public static function extractJson(string $text): ?array
    {
        $t = trim($text);
        if ($t === '') return null;

        // Strip leading/trailing markdown fences if present
        if (str_starts_with($t, '```')) {
            $t = (string) preg_replace('/^```(?:json)?\s*/i', '', $t);
            $t = (string) preg_replace('/```\s*$/', '', $t);
            $t = trim($t);
        }

        // Try direct decode first
        $decoded = json_decode($t, true);
        if (is_array($decoded)) return $decoded;

        // Otherwise, find the first {…} block
        $start = strpos($t, '{');
        $end   = strrpos($t, '}');
        if ($start === false || $end === false || $end <= $start) return null;

        $candidate = substr($t, $start, $end - $start + 1);
        $decoded = json_decode($candidate, true);
        return is_array($decoded) ? $decoded : null;
    }
}
