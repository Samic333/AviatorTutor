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
     * Grade a learner's typed flashcard answer against the canonical
     * expected_answer and (optional) rubric using Claude.
     *
     * The model returns a strict JSON judgement we parse and persist to
     * `flashcard_attempts`. If the API isn't configured we degrade
     * gracefully to a string-similarity fallback so the gate doesn't
     * become a hard blocker.
     *
     * @return array{ok:bool, is_correct:bool, score:int, feedback:string,
     *               source:string, request_ms?:int, error?:string}
     */
    public static function gradeAnswer(string $typed, string $expected, string $rubric = ''): array
    {
        $typed    = trim($typed);
        $expected = trim($expected);

        if ($typed === '') {
            return [
                'ok'         => true,
                'is_correct' => false,
                'score'      => 0,
                'feedback'   => 'You didn’t type anything. Take a stab — partial credit counts.',
                'source'     => 'empty_input',
            ];
        }

        // Quick cheap-grade fallback when the API key isn't set: case- and
        // punctuation-insensitive substring match.
        $cfg = self::config();
        if (!$cfg['configured']) {
            return self::offlineGrade($typed, $expected);
        }

        $rubricLine = $rubric !== ''
            ? "RUBRIC: " . $rubric . "\n"
            : '';

        $sys = <<<'PROMPT'
You are an aviation-systems instructor grading a flashcard typed answer.
You will be given the question's CANONICAL ANSWER and the LEARNER'S
TYPED ANSWER. Decide whether the learner conveyed the same meaning,
allowing for paraphrasing, synonyms, and minor omissions.

Return STRICT JSON ONLY — no prose, no fences:

{
  "score": integer 0-100,
  "is_correct": boolean,           // true when score >= 70
  "feedback": string                // 1-2 sentences. If wrong: gently
                                    // identify what's missing or off,
                                    // referencing the canonical answer.
                                    // If correct: brief reinforcement.
}

Rules:
- Be generous on phrasing, strict on meaning. A learner who hits all the
  key concepts in different words is correct. A learner who omits a
  safety-critical clause (e.g. "with hydraulic pressure" on a brake
  question) is not.
- If a rubric is provided, weight your score against it.
- Do NOT add markdown fences. Output JSON only.
PROMPT;

        $usr = "QUESTION ANSWER (canonical): " . $expected . "\n" .
               $rubricLine .
               "LEARNER TYPED: " . $typed . "\n\n" .
               "Now produce the JSON judgement.";

        $resp = self::generate($sys, $usr, [
            'temperature' => 0.0,
            'max_tokens'  => 400,
        ]);
        if (($resp['ok'] ?? false) !== true) {
            // Fall back to offline grade if Claude is unreachable.
            $fb = self::offlineGrade($typed, $expected);
            $fb['error'] = (string) ($resp['error_detail'] ?? $resp['error'] ?? 'api_error');
            $fb['source'] = 'offline_fallback';
            return $fb;
        }

        $j = self::extractJson((string) $resp['text']);
        if (!is_array($j) || !isset($j['score'])) {
            return self::offlineGrade($typed, $expected);
        }

        $score      = max(0, min(100, (int) $j['score']));
        $isCorrect  = isset($j['is_correct']) ? (bool) $j['is_correct'] : ($score >= 70);
        $feedback   = (string) ($j['feedback'] ?? '');

        return [
            'ok'         => true,
            'is_correct' => $isCorrect,
            'score'      => $score,
            'feedback'   => $feedback,
            'source'     => 'claude',
            'request_ms' => (int) ($resp['request_ms'] ?? 0),
        ];
    }

    /**
     * Cheap offline grader used when Anthropic is unconfigured or
     * unreachable. Produces a usable 0/40/70/95 verdict based on
     * normalized substring overlap.
     *
     * @return array{ok:bool, is_correct:bool, score:int, feedback:string, source:string}
     */
    private static function offlineGrade(string $typed, string $expected): array
    {
        $norm = function (string $s): string {
            $s = strtolower($s);
            $s = (string) preg_replace('/[^a-z0-9 ]+/', ' ', $s);
            $s = (string) preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };
        $a = $norm($typed);
        $b = $norm($expected);
        if ($a === '' || $b === '') {
            return [
                'ok'=>true,'is_correct'=>false,'score'=>0,
                'feedback'=>'Need a longer answer to grade.',
                'source'=>'offline_empty'
            ];
        }
        $aTokens = array_unique(explode(' ', $a));
        $bTokens = array_unique(explode(' ', $b));
        $shared  = count(array_intersect($aTokens, $bTokens));
        $needed  = max(1, count($bTokens));
        $ratio   = $shared / $needed;

        if ($a === $b)            { $score = 100; $msg = 'Exact match.'; }
        elseif (str_contains($a, $b) || str_contains($b, $a)) { $score = 95; $msg = 'Looks right.'; }
        elseif ($ratio >= 0.6)    { $score = 80; $msg = 'You got the gist — make sure you can recite the canonical phrasing.'; }
        elseif ($ratio >= 0.3)    { $score = 50; $msg = 'You\'re on the right track but missing key terms.'; }
        else                      { $score = 20; $msg = 'That doesn\'t match — review the answer and try again later.'; }

        return [
            'ok'         => true,
            'is_correct' => $score >= 70,
            'score'      => $score,
            'feedback'   => $msg,
            'source'     => 'offline',
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
