<?php
/**
 * AI Content Service — multi-provider wrapper for LLM-backed lesson
 * generation and flashcard grading.
 *
 * Supported providers (Phase 6):
 *   - anthropic — Claude Messages API
 *   - openai    — GPT Chat Completions API
 *   - gemini    — Google Generative Language API
 *
 * One unified contract:
 *
 *   AIContentService::generate($systemPrompt, $userText, [
 *     'provider'    => 'anthropic'|'openai'|'gemini',  // optional override
 *     'model'       => '...',                          // optional override
 *     'max_tokens'  => 16000,
 *     'temperature' => 0.2,
 *   ])
 *
 * Returns the same {ok, text, usage, request_ms, ...} shape regardless
 * of which provider answered, so callers (admin AI test, the cron
 * worker, the flashcard grader) stay provider-agnostic.
 *
 * If the chosen provider isn't configured we fall back to the next
 * configured provider, in this order: anthropic → openai → gemini.
 * Set 'ai_default_provider' in config/app.local.php to change the
 * default.
 *
 * Required config keys (config/app.local.php):
 *   'anthropic_api_key' => 'sk-ant-…'   (any one of these is enough)
 *   'openai_api_key'    => 'sk-…'
 *   'gemini_api_key'    => 'AIzaSy…'
 *
 * The other config defaults live in config/app.php.
 */

declare(strict_types=1);

namespace App\Services;

class AIContentService
{
    /** Providers in fallback order */
    public const PROVIDERS = ['anthropic', 'openai', 'gemini'];

    /**
     * @return array{
     *   default_provider: string,
     *   providers: array<string, array{
     *     configured: bool, api_key: string, api_url: string,
     *     model_outline: string, model_chunk: string,
     *     max_tokens: int, timeout: int
     *   }>
     * }
     */
    public static function config(): array
    {
        // Lookup order for each value: AppSetting (DB, admin-editable) → config/app.php
        $cfg = require BASE_PATH . '/config/app.php';
        $resolve = function (string $settingKey, $fileFallback): string {
            $db = AppSetting::get($settingKey);
            if ($db !== null && $db !== '') return $db;
            return (string) $fileFallback;
        };

        $providers = [
            'anthropic' => [
                'label'         => 'Anthropic Claude',
                'api_key'       => $resolve('anthropic_api_key',      $cfg['anthropic_api_key']      ?? ''),
                'api_url'       => $resolve('anthropic_api_url',      $cfg['anthropic_api_url']      ?? 'https://api.anthropic.com/v1/messages'),
                'api_version'   => $resolve('anthropic_api_version',  $cfg['anthropic_api_version']  ?? '2023-06-01'),
                'model_outline' => $resolve('anthropic_model_outline', $cfg['anthropic_model_outline'] ?? 'claude-opus-4-5'),
                'model_chunk'   => $resolve('anthropic_model_chunk',   $cfg['anthropic_model_chunk']   ?? 'claude-sonnet-4-5'),
                'max_tokens'    => (int) $resolve('anthropic_max_tokens', (string) ($cfg['anthropic_max_tokens'] ?? 16000)),
                'timeout'       => (int) $resolve('anthropic_timeout',    (string) ($cfg['anthropic_timeout']    ?? 120)),
            ],
            'openai' => [
                'label'         => 'OpenAI GPT',
                'api_key'       => $resolve('openai_api_key',         $cfg['openai_api_key']         ?? ''),
                'api_url'       => $resolve('openai_api_url',         $cfg['openai_api_url']         ?? 'https://api.openai.com/v1/chat/completions'),
                'model_outline' => $resolve('openai_model_outline',   $cfg['openai_model_outline']   ?? 'gpt-4.1'),
                'model_chunk'   => $resolve('openai_model_chunk',     $cfg['openai_model_chunk']     ?? 'gpt-4.1-mini'),
                'max_tokens'    => (int) $resolve('openai_max_tokens', (string) ($cfg['openai_max_tokens'] ?? 8192)),
                'timeout'       => (int) $resolve('openai_timeout',    (string) ($cfg['openai_timeout']    ?? 120)),
            ],
            'gemini' => [
                'label'         => 'Google Gemini',
                'api_key'       => $resolve('gemini_api_key',         $cfg['gemini_api_key']         ?? ''),
                'api_url'       => $resolve('gemini_api_url',         $cfg['gemini_api_url']         ?? 'https://generativelanguage.googleapis.com/v1beta/models/'),
                'model_outline' => $resolve('gemini_model_outline',   $cfg['gemini_model_outline']   ?? 'gemini-2.5-pro'),
                'model_chunk'   => $resolve('gemini_model_chunk',     $cfg['gemini_model_chunk']     ?? 'gemini-2.5-flash'),
                'max_tokens'    => (int) $resolve('gemini_max_tokens', (string) ($cfg['gemini_max_tokens'] ?? 8192)),
                'timeout'       => (int) $resolve('gemini_timeout',    (string) ($cfg['gemini_timeout']    ?? 120)),
            ],
        ];
        foreach ($providers as $name => &$p) {
            $p['configured'] = $p['api_key'] !== '';
        }

        $default = $resolve('ai_default_provider', (string) ($cfg['ai_default_provider'] ?? 'anthropic'));
        if (!in_array($default, self::PROVIDERS, true)) $default = 'anthropic';

        return [
            'default_provider' => $default,
            'providers'        => $providers,
        ];
    }

    /** True if at least one provider has an api key configured. */
    public static function anyConfigured(): bool
    {
        foreach (self::config()['providers'] as $p) {
            if ($p['configured']) return true;
        }
        return false;
    }

    /**
     * Resolve the provider to actually use. Honours an explicit override,
     * then the configured default, then the first configured provider.
     */
    public static function resolveProvider(?string $requested = null): ?string
    {
        $cfg = self::config();
        $providers = $cfg['providers'];

        if ($requested && isset($providers[$requested]) && $providers[$requested]['configured']) {
            return $requested;
        }
        $default = $cfg['default_provider'];
        if (isset($providers[$default]) && $providers[$default]['configured']) {
            return $default;
        }
        foreach (self::PROVIDERS as $p) {
            if ($providers[$p]['configured']) return $p;
        }
        return null;
    }

    /**
     * Multi-provider generate. Same return shape regardless of provider.
     *
     * @param array  $opts {
     *   provider?:    'anthropic'|'openai'|'gemini',
     *   model?:       string (override the chunk model),
     *   max_tokens?:  int,
     *   temperature?: float,
     * }
     *
     * @return array{
     *   ok:bool,
     *   text?:string,
     *   stop_reason?:string,
     *   model?:string,
     *   provider?:string,
     *   usage?:array{input_tokens?:int,output_tokens?:int},
     *   request_ms?:int,
     *   http_code?:int,
     *   error?:string,
     *   error_detail?:string,
     *   raw?:array,
     * }
     */
    public static function generate(string $systemPrompt, string $userText, array $opts = []): array
    {
        $provider = self::resolveProvider($opts['provider'] ?? null);
        if ($provider === null) {
            return [
                'ok'           => false,
                'error'        => 'no_provider_configured',
                'error_detail' => 'No AI provider API key is set in config/app.local.php (anthropic_api_key, openai_api_key, or gemini_api_key).',
            ];
        }

        $cfg = self::config()['providers'][$provider];
        $model       = (string) ($opts['model']        ?? $cfg['model_chunk']);
        $maxTokens   = (int)    ($opts['max_tokens']   ?? $cfg['max_tokens']);
        $temperature = (float)  ($opts['temperature']  ?? 0.2);

        switch ($provider) {
            case 'openai': return self::generateOpenAI($cfg, $model, $maxTokens, $temperature, $systemPrompt, $userText);
            case 'gemini': return self::generateGemini($cfg, $model, $maxTokens, $temperature, $systemPrompt, $userText);
            case 'anthropic':
            default:       return self::generateAnthropic($cfg, $model, $maxTokens, $temperature, $systemPrompt, $userText);
        }
    }

    /** ----------------------------------------------------------------
     *  Anthropic
     *  ---------------------------------------------------------------- */
    private static function generateAnthropic(array $cfg, string $model, int $maxTokens, float $temperature, string $systemPrompt, string $userText): array
    {
        $payload = [
            'model'       => $model,
            'max_tokens'  => $maxTokens,
            'temperature' => $temperature,
            'system'      => $systemPrompt,
            'messages'    => [['role' => 'user', 'content' => $userText]],
        ];
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $cfg['api_key'],
            'anthropic-version: ' . ($cfg['api_version'] ?? '2023-06-01'),
        ];
        $resp = self::httpPostJson($cfg['api_url'], $headers, $payload, $cfg['timeout']);
        if (!$resp['ok']) return $resp + ['provider' => 'anthropic'];

        $data = $resp['data'];
        $text = '';
        foreach ((array) ($data['content'] ?? []) as $block) {
            if (is_array($block) && ($block['type'] ?? '') === 'text') {
                $text .= (string) ($block['text'] ?? '');
            }
        }
        return [
            'ok'          => true,
            'provider'    => 'anthropic',
            'text'        => $text,
            'stop_reason' => (string) ($data['stop_reason'] ?? ''),
            'model'       => (string) ($data['model']       ?? $model),
            'usage'       => [
                'input_tokens'  => (int) ($data['usage']['input_tokens']  ?? 0),
                'output_tokens' => (int) ($data['usage']['output_tokens'] ?? 0),
            ],
            'raw'         => $data,
            'http_code'   => $resp['http_code'],
            'request_ms'  => $resp['request_ms'],
        ];
    }

    /** ----------------------------------------------------------------
     *  OpenAI
     *  ---------------------------------------------------------------- */
    private static function generateOpenAI(array $cfg, string $model, int $maxTokens, float $temperature, string $systemPrompt, string $userText): array
    {
        $payload = [
            'model'       => $model,
            'max_tokens'  => $maxTokens,
            'temperature' => $temperature,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userText],
            ],
        ];
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $cfg['api_key'],
        ];
        $resp = self::httpPostJson($cfg['api_url'], $headers, $payload, $cfg['timeout']);
        if (!$resp['ok']) return $resp + ['provider' => 'openai'];

        $data = $resp['data'];
        $choice = $data['choices'][0] ?? [];
        $text   = (string) ($choice['message']['content'] ?? '');
        return [
            'ok'          => true,
            'provider'    => 'openai',
            'text'        => $text,
            'stop_reason' => (string) ($choice['finish_reason'] ?? ''),
            'model'       => (string) ($data['model'] ?? $model),
            'usage'       => [
                // Normalise OpenAI's prompt_tokens/completion_tokens to our
                // input_tokens/output_tokens shape.
                'input_tokens'  => (int) ($data['usage']['prompt_tokens']     ?? 0),
                'output_tokens' => (int) ($data['usage']['completion_tokens'] ?? 0),
            ],
            'raw'         => $data,
            'http_code'   => $resp['http_code'],
            'request_ms'  => $resp['request_ms'],
        ];
    }

    /** ----------------------------------------------------------------
     *  Gemini
     *  ---------------------------------------------------------------- */
    private static function generateGemini(array $cfg, string $model, int $maxTokens, float $temperature, string $systemPrompt, string $userText): array
    {
        // Gemini puts the api key in the URL query string and the model in
        // the path: …/models/{model}:generateContent?key=…
        $url = rtrim($cfg['api_url'], '/') . '/' . rawurlencode($model) . ':generateContent?key=' . urlencode($cfg['api_key']);

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $userText]]],
            ],
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature'     => $temperature,
            ],
        ];
        $headers = ['Content-Type: application/json'];
        $resp = self::httpPostJson($url, $headers, $payload, $cfg['timeout']);
        if (!$resp['ok']) return $resp + ['provider' => 'gemini'];

        $data = $resp['data'];
        $candidate = $data['candidates'][0] ?? [];
        $text = '';
        foreach ((array) ($candidate['content']['parts'] ?? []) as $part) {
            if (isset($part['text'])) $text .= (string) $part['text'];
        }
        return [
            'ok'          => true,
            'provider'    => 'gemini',
            'text'        => $text,
            'stop_reason' => (string) ($candidate['finishReason'] ?? ''),
            'model'       => $model,
            'usage'       => [
                'input_tokens'  => (int) ($data['usageMetadata']['promptTokenCount']     ?? 0),
                'output_tokens' => (int) ($data['usageMetadata']['candidatesTokenCount'] ?? 0),
            ],
            'raw'         => $data,
            'http_code'   => $resp['http_code'],
            'request_ms'  => $resp['request_ms'],
        ];
    }

    /** ----------------------------------------------------------------
     *  Shared cURL helper
     *  ---------------------------------------------------------------- */
    private static function httpPostJson(string $url, array $headers, array $payload, int $timeout): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            return ['ok' => false, 'error' => 'json_encode_failed'];
        }
        $startedAt = microtime(true);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $raw      = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);
        $requestMs = (int) round((microtime(true) - $startedAt) * 1000);

        if ($raw === false) {
            return ['ok' => false, 'error' => 'curl_error', 'error_detail' => $curlErr, 'http_code' => $httpCode, 'request_ms' => $requestMs];
        }
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'error' => 'invalid_json_response', 'error_detail' => substr((string) $raw, 0, 600), 'http_code' => $httpCode, 'request_ms' => $requestMs];
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            $msg = $decoded['error']['message'] ?? $decoded['error']['code'] ?? 'HTTP ' . $httpCode;
            return ['ok' => false, 'error' => 'api_error', 'error_detail' => is_string($msg) ? $msg : json_encode($msg), 'http_code' => $httpCode, 'request_ms' => $requestMs, 'raw' => $decoded];
        }
        return ['ok' => true, 'data' => $decoded, 'http_code' => $httpCode, 'request_ms' => $requestMs];
    }

    /**
     * Grade a learner's typed flashcard answer.
     *
     * Now provider-agnostic: uses the configured default. If no provider
     * is configured, falls back to the offline string-similarity grader.
     */
    public static function gradeAnswer(string $typed, string $expected, string $rubric = '', ?string $provider = null): array
    {
        $typed    = trim($typed);
        $expected = trim($expected);

        if ($typed === '') {
            return ['ok'=>true,'is_correct'=>false,'score'=>0,'feedback'=>'You didn’t type anything. Take a stab — partial credit counts.','source'=>'empty_input'];
        }
        if (!self::anyConfigured()) {
            return self::offlineGrade($typed, $expected);
        }

        $rubricLine = $rubric !== '' ? "RUBRIC: " . $rubric . "\n" : '';

        $sys = <<<'PROMPT'
You are an aviation-systems instructor grading a flashcard typed answer.
You will be given the question's CANONICAL ANSWER and the LEARNER'S
TYPED ANSWER. Decide whether the learner conveyed the same meaning,
allowing for paraphrasing, synonyms, and minor omissions.

Return STRICT JSON ONLY — no prose, no fences:

{
  "score": integer 0-100,
  "is_correct": boolean,
  "feedback": string
}

Rules: be generous on phrasing, strict on meaning. Score >= 70 is_correct=true.
PROMPT;

        $usr = "QUESTION ANSWER (canonical): " . $expected . "\n" .
               $rubricLine .
               "LEARNER TYPED: " . $typed . "\n\n" .
               "Now produce the JSON judgement.";

        $resp = self::generate($sys, $usr, [
            'provider'    => $provider,
            'temperature' => 0.0,
            'max_tokens'  => 400,
        ]);
        if (($resp['ok'] ?? false) !== true) {
            $fb = self::offlineGrade($typed, $expected);
            $fb['error']  = (string) ($resp['error_detail'] ?? $resp['error'] ?? 'api_error');
            $fb['source'] = 'offline_fallback';
            return $fb;
        }
        $j = self::extractJson((string) $resp['text']);
        if (!is_array($j) || !isset($j['score'])) return self::offlineGrade($typed, $expected);

        $score     = max(0, min(100, (int) $j['score']));
        $isCorrect = isset($j['is_correct']) ? (bool) $j['is_correct'] : ($score >= 70);
        return [
            'ok'         => true,
            'is_correct' => $isCorrect,
            'score'      => $score,
            'feedback'   => (string) ($j['feedback'] ?? ''),
            'source'     => $resp['provider'] ?? 'ai',
            'request_ms' => (int) ($resp['request_ms'] ?? 0),
        ];
    }

    /** Offline fallback grader (token overlap, no AI). */
    private static function offlineGrade(string $typed, string $expected): array
    {
        $norm = function (string $s): string {
            $s = strtolower($s);
            $s = (string) preg_replace('/[^a-z0-9 ]+/', ' ', $s);
            $s = (string) preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };
        $a = $norm($typed); $b = $norm($expected);
        if ($a === '' || $b === '') {
            return ['ok'=>true,'is_correct'=>false,'score'=>0,'feedback'=>'Need a longer answer to grade.','source'=>'offline_empty'];
        }
        $aT = array_unique(explode(' ', $a));
        $bT = array_unique(explode(' ', $b));
        $shared = count(array_intersect($aT, $bT));
        $needed = max(1, count($bT));
        $ratio  = $shared / $needed;
        if ($a === $b)            { $score = 100; $msg = 'Exact match.'; }
        elseif (str_contains($a, $b) || str_contains($b, $a)) { $score = 95; $msg = 'Looks right.'; }
        elseif ($ratio >= 0.6)    { $score = 80; $msg = 'You got the gist — make sure you can recite the canonical phrasing.'; }
        elseif ($ratio >= 0.3)    { $score = 50; $msg = 'You\'re on the right track but missing key terms.'; }
        else                      { $score = 20; $msg = 'That doesn\'t match — review the answer and try again later.'; }
        return ['ok'=>true,'is_correct'=>$score>=70,'score'=>$score,'feedback'=>$msg,'source'=>'offline'];
    }

    /** Fence-tolerant JSON extractor from a chatty model response. */
    public static function extractJson(string $text): ?array
    {
        $t = trim($text);
        if ($t === '') return null;
        if (str_starts_with($t, '```')) {
            $t = (string) preg_replace('/^```(?:json)?\s*/i', '', $t);
            $t = (string) preg_replace('/```\s*$/', '', $t);
            $t = trim($t);
        }
        $decoded = json_decode($t, true);
        if (is_array($decoded)) return $decoded;
        $start = strpos($t, '{');
        $end   = strrpos($t, '}');
        if ($start === false || $end === false || $end <= $start) return null;
        $candidate = substr($t, $start, $end - $start + 1);
        $decoded = json_decode($candidate, true);
        return is_array($decoded) ? $decoded : null;
    }
}
