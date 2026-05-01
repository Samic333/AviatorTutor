<?php
/**
 * AI Job Service — async pipeline for AI lesson generation.
 *
 * Lifecycle:
 *   queued    — admin uploaded a PDF / pasted text via /admin/import
 *   running   — cron worker picked it up
 *   review    — generation succeeded; drafts written, awaiting admin
 *               review + publish (Phase 4)
 *   published — admin clicked Publish, draft rows flipped to published
 *   failed    — model returned junk twice or extraction failed
 *
 * Cron entry: scripts/run_ai_jobs.php — runs every minute, processes
 * one queued job per tick to stay well under PHP's max_execution_time.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

class AIJobService
{
    /**
     * Enqueue a new generation job. Returns the job id.
     *
     * @param array{
     *   admin_user_id:int,
     *   target_system_id?:?int,
     *   pdf_path?:?string,
     *   original_filename?:?string,
     *   source_label?:?string,
     *   pasted_text?:?string,
     *   mode:string,
     *   analysis_depth:string
     * } $input
     */
    public static function enqueue(array $input): int
    {
        $db = DB::instance();
        $provider = (string) ($input['provider'] ?? 'anthropic');
        if (!in_array($provider, AIContentService::PROVIDERS, true)) {
            $provider = 'anthropic';
        }
        $db->execute(
            'INSERT INTO ai_generation_jobs
                 (admin_user_id, target_system_id, pdf_path, original_filename,
                  source_label, pasted_text, mode, analysis_depth, provider,
                  status, progress_pct, progress_message)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "queued", 0, "Queued")',
            [
                $input['admin_user_id'],
                $input['target_system_id']  ?? null,
                $input['pdf_path']          ?? null,
                $input['original_filename'] ?? null,
                $input['source_label']      ?? null,
                $input['pasted_text']       ?? null,
                $input['mode']              ?? 'full',
                $input['analysis_depth']    ?? 'standard',
                $provider,
            ]
        );
        return (int) $db->lastInsertId();
    }

    /** Find one job by id, or null. */
    public static function find(int $id): ?array
    {
        $row = DB::instance()->queryOne(
            'SELECT * FROM ai_generation_jobs WHERE id = ?',
            [$id]
        );
        return is_array($row) ? $row : null;
    }

    /**
     * Pick the oldest queued job and atomically mark it running. Returns
     * null if there's nothing to do or another worker grabbed it.
     */
    public static function claimNextQueued(): ?array
    {
        $db = DB::instance();
        $db->execute('START TRANSACTION');
        try {
            $row = $db->queryOne(
                'SELECT * FROM ai_generation_jobs
                 WHERE status = "queued"
                   AND (locked_at IS NULL OR locked_at < (NOW() - INTERVAL 30 MINUTE))
                 ORDER BY id ASC
                 LIMIT 1
                 FOR UPDATE'
            );
            if (!is_array($row)) {
                $db->execute('COMMIT');
                return null;
            }
            $db->execute(
                'UPDATE ai_generation_jobs
                 SET status = "running",
                     locked_at = NOW(),
                     started_at = NOW(),
                     progress_pct = 5,
                     progress_message = "Claimed by worker"
                 WHERE id = ?',
                [(int) $row['id']]
            );
            $db->execute('COMMIT');
            $row['status'] = 'running';
            return $row;
        } catch (\Throwable $e) {
            $db->execute('ROLLBACK');
            throw $e;
        }
    }

    /** Update progress for a running job. */
    public static function updateProgress(int $id, int $pct, string $msg): void
    {
        DB::instance()->execute(
            'UPDATE ai_generation_jobs
             SET progress_pct = ?, progress_message = ?
             WHERE id = ?',
            [max(0, min(100, $pct)), substr($msg, 0, 250), $id]
        );
    }

    /** Mark a job failed and record an error message. */
    public static function fail(int $id, string $error): void
    {
        DB::instance()->execute(
            'UPDATE ai_generation_jobs
             SET status = "failed",
                 error = ?,
                 finished_at = NOW(),
                 progress_pct = 100,
                 progress_message = "Failed"
             WHERE id = ?',
            [substr($error, 0, 4000), $id]
        );
    }

    /**
     * Run the full pipeline for one job: extract text, call Claude, parse
     * JSON, write drafts. Updates progress + status as it goes. Catches
     * its own exceptions and maps them to fail().
     */
    public static function run(array $job): void
    {
        $id = (int) $job['id'];
        try {
            // ------------------------------------------------------------
            // 1. Get the source text — from uploaded PDF or pasted body.
            // ------------------------------------------------------------
            self::updateProgress($id, 10, 'Extracting source text');

            $text = (string) ($job['pasted_text'] ?? '');
            if ($text === '' && !empty($job['pdf_path'])) {
                $extract = PdfTextService::extract((string) $job['pdf_path'], 200000);
                if (($extract['ok'] ?? false) !== true) {
                    self::fail($id, 'PDF extraction failed: ' . ($extract['error_detail'] ?? $extract['error'] ?? 'unknown'));
                    return;
                }
                $text = (string) $extract['text'];
            }
            $text = trim($text);
            if ($text === '') {
                self::fail($id, 'No source text available — neither PDF nor pasted_text yielded content.');
                return;
            }

            // ------------------------------------------------------------
            // 2. Build the prompt & call Claude.
            // ------------------------------------------------------------
            self::updateProgress($id, 30, 'Calling Claude');

            $depth        = (string) ($job['analysis_depth'] ?? 'standard');
            $sourceLabel  = (string) ($job['source_label'] ?? ($job['original_filename'] ?? 'pasted excerpt'));

            // Look up target system (for ATA hint) if specified
            $ataHint = '';
            $targetSystemId = $job['target_system_id'] ?? null;
            if ($targetSystemId) {
                $sys = DB::instance()->queryOne(
                    'SELECT name, ata_code FROM systems WHERE id = ?',
                    [(int) $targetSystemId]
                );
                if (is_array($sys)) {
                    $ataHint = trim(($sys['ata_code'] ?? '') . ' — ' . ($sys['name'] ?? ''), '— ');
                }
            }

            $systemPrompt = AIPromptBuilder::lessonSystem($depth);
            $userPrompt   = AIPromptBuilder::lessonUser($sourceLabel, $ataHint, $text);

            $provider = (string) ($job['provider'] ?? 'anthropic');
            $apiResp = AIContentService::generate($systemPrompt, $userPrompt, [
                'provider'    => $provider,
                'temperature' => 0.2,
            ]);
            // Record which model actually answered (provider may have
            // fallen back to a different one if the requested provider
            // was unconfigured).
            if (!empty($apiResp['model'])) {
                DB::instance()->execute(
                    'UPDATE ai_generation_jobs SET model = ?, provider = ? WHERE id = ?',
                    [(string) $apiResp['model'], (string) ($apiResp['provider'] ?? $provider), $id]
                );
            }

            if (($apiResp['ok'] ?? false) !== true) {
                self::fail($id, 'Claude API: ' . ($apiResp['error_detail'] ?? $apiResp['error'] ?? 'unknown'));
                return;
            }

            // ------------------------------------------------------------
            // 3. Parse the JSON.
            // ------------------------------------------------------------
            self::updateProgress($id, 70, 'Parsing model output');
            $parsed = AIContentService::extractJson((string) ($apiResp['text'] ?? ''));
            if (!is_array($parsed) || empty($parsed['lesson']) || empty($parsed['slides'])) {
                self::fail($id, 'Model output did not contain a parseable lesson JSON.');
                self::storeRawResponse($id, $apiResp);
                return;
            }

            // ------------------------------------------------------------
            // 4. Write drafts to the DB.
            // ------------------------------------------------------------
            self::updateProgress($id, 85, 'Writing drafts');

            $draftSummary = self::writeDrafts($id, $job, $parsed);

            // ------------------------------------------------------------
            // 5. Mark job as awaiting review.
            // ------------------------------------------------------------
            DB::instance()->execute(
                'UPDATE ai_generation_jobs
                 SET status = "review",
                     progress_pct = 100,
                     progress_message = ?,
                     prompt_tokens = ?,
                     completion_tokens = ?,
                     request_ms = ?,
                     raw_response = ?,
                     payload_json = ?,
                     lesson_id = ?,
                     finished_at = NOW()
                 WHERE id = ?',
                [
                    'Drafts ready: ' . $draftSummary['slides'] . ' slides, '
                                     . $draftSummary['flashcards'] . ' flashcards, '
                                     . $draftSummary['quiz'] . ' quiz items',
                    (int) ($apiResp['usage']['input_tokens']  ?? 0),
                    (int) ($apiResp['usage']['output_tokens'] ?? 0),
                    (int) ($apiResp['request_ms'] ?? 0),
                    (string) ($apiResp['text'] ?? ''),
                    json_encode($parsed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $draftSummary['lesson_id'],
                    $id,
                ]
            );
        } catch (\Throwable $e) {
            self::fail($id, 'Worker exception: ' . $e->getMessage());
        }
    }

    /**
     * Write a brand-new lesson + slides + flashcards + quiz_questions to
     * the DB in DRAFT state, all linked back to this AI job.
     *
     * @return array{lesson_id:int,slides:int,flashcards:int,quiz:int}
     */
    private static function writeDrafts(int $jobId, array $job, array $parsed): array
    {
        $db = DB::instance();

        $systemId = (int) ($job['target_system_id'] ?? 0);
        if (!$systemId) {
            // Fall back: try to match an existing system by ata_code, else
            // default to the first system. The admin can re-target later.
            $ataCode = (string) ($parsed['ata_code'] ?? '');
            if ($ataCode !== '') {
                $sys = $db->queryOne('SELECT id FROM systems WHERE ata_code = ? LIMIT 1', [$ataCode]);
                if (is_array($sys)) $systemId = (int) $sys['id'];
            }
            if (!$systemId) {
                $sys = $db->queryOne('SELECT id FROM systems ORDER BY sort_order, id LIMIT 1');
                $systemId = (int) ($sys['id'] ?? 1);
            }
        }

        $lesson = $parsed['lesson'] ?? [];
        $title  = trim((string) ($lesson['title'] ?? 'AI-generated lesson'));
        $slug   = self::slugify($title) . '-ai-' . $jobId;
        $depth  = (string) ($job['analysis_depth'] ?? 'standard');

        $db->execute(
            'INSERT INTO lessons
                 (system_id, title, slug, content_type, analysis_depth,
                  body, summary, key_facts, must_know, exam_traps,
                  is_published, draft_status)
             VALUES (?, ?, ?, "overview", ?, ?, ?, ?, ?, ?, 0, "draft")',
            [
                $systemId,
                substr($title, 0, 250),
                substr($slug, 0, 250),
                $depth,
                '', // body lives on slides
                substr((string) ($lesson['summary'] ?? ''), 0, 1000),
                json_encode((array) ($lesson['key_facts']  ?? []), JSON_UNESCAPED_UNICODE),
                json_encode((array) ($lesson['must_know']  ?? []), JSON_UNESCAPED_UNICODE),
                json_encode((array) ($lesson['exam_traps'] ?? []), JSON_UNESCAPED_UNICODE),
            ]
        );
        $lessonId = (int) $db->lastInsertId();

        // Slides
        $slideCount = 0;
        foreach ((array) ($parsed['slides'] ?? []) as $i => $s) {
            if (!is_array($s)) continue;
            $q = is_array($s['question'] ?? null) ? $s['question'] : null;
            $questionJson = $q ? json_encode($q, JSON_UNESCAPED_UNICODE) : null;
            $db->execute(
                'INSERT INTO lesson_slides
                     (lesson_id, sort_order, slide_type, title, body,
                      key_point, ops_relevance, question,
                      status, source, ai_job_id, source_quote)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, "draft", "ai", ?, ?)',
                [
                    $lessonId,
                    (int) ($s['sort_order'] ?? $i),
                    self::clampSlideType((string) ($s['slide_type'] ?? 'concept')),
                    substr((string) ($s['title'] ?? ''), 0, 250),
                    (string) ($s['body'] ?? ''),
                    substr((string) ($s['key_point'] ?? ''), 0, 250),
                    substr((string) ($s['ops_relevance'] ?? ''), 0, 250),
                    $questionJson,
                    $jobId,
                    (string) ($s['source_quote'] ?? ''),
                ]
            );
            $slideCount++;
        }

        // Flashcards
        $cardCount = 0;
        foreach ((array) ($parsed['flashcards'] ?? []) as $f) {
            if (!is_array($f)) continue;
            $db->execute(
                'INSERT INTO flashcards
                     (system_id, front, back, expected_answer, grading_rubric,
                      hint, difficulty, status, source, ai_job_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, "draft", "ai", ?)',
                [
                    $systemId,
                    (string) ($f['front'] ?? ''),
                    (string) ($f['back']  ?? ''),
                    (string) ($f['expected_answer'] ?? ($f['back'] ?? '')),
                    (string) ($f['grading_rubric']  ?? ''),
                    (string) ($f['hint'] ?? ''),
                    self::clampDifficulty((string) ($f['difficulty'] ?? 'medium')),
                    $jobId,
                ]
            );
            $cardCount++;
        }

        // Quiz: each AI-generated lesson gets its own quiz row + questions
        $quizItems = (array) ($parsed['quiz'] ?? []);
        $qCount = 0;
        if (!empty($quizItems)) {
            $db->execute(
                'INSERT INTO quizzes (system_id, title, description, quiz_type, pass_score, is_published)
                 VALUES (?, ?, ?, "practice", 70, 0)',
                [
                    $systemId,
                    substr($title . ' — AI quiz', 0, 250),
                    'Auto-generated by AI job #' . $jobId,
                ]
            );
            $quizId = (int) $db->lastInsertId();

            foreach ($quizItems as $idx => $q) {
                if (!is_array($q)) continue;
                $opts = array_values((array) ($q['options'] ?? []));
                $correct = (int) ($q['correct_index'] ?? 0);
                $db->execute(
                    'INSERT INTO quiz_questions
                         (quiz_id, question_text, question_type, options, correct_answer,
                          explanation, difficulty, sort_order, status, source, ai_job_id)
                     VALUES (?, ?, "mcq", ?, ?, ?, ?, ?, "draft", "ai", ?)',
                    [
                        $quizId,
                        (string) ($q['question_text'] ?? ''),
                        json_encode($opts, JSON_UNESCAPED_UNICODE),
                        json_encode(['index' => $correct], JSON_UNESCAPED_UNICODE),
                        (string) ($q['explanation'] ?? ''),
                        self::clampDifficulty((string) ($q['difficulty'] ?? 'medium')),
                        (int) ($q['sort_order'] ?? $idx),
                        $jobId,
                    ]
                );
                $qCount++;
            }
        }

        return [
            'lesson_id'  => $lessonId,
            'slides'     => $slideCount,
            'flashcards' => $cardCount,
            'quiz'       => $qCount,
        ];
    }

    private static function storeRawResponse(int $id, array $apiResp): void
    {
        DB::instance()->execute(
            'UPDATE ai_generation_jobs
             SET raw_response = ?, prompt_tokens = ?, completion_tokens = ?, request_ms = ?
             WHERE id = ?',
            [
                (string) ($apiResp['text'] ?? ''),
                (int) ($apiResp['usage']['input_tokens']  ?? 0),
                (int) ($apiResp['usage']['output_tokens'] ?? 0),
                (int) ($apiResp['request_ms'] ?? 0),
                $id,
            ]
        );
    }

    private static function clampSlideType(string $t): string
    {
        $allowed = ['intro','concept','system','normal_op','abnormal','operational','qrh','scenario','revision','quiz'];
        return in_array($t, $allowed, true) ? $t : 'concept';
    }

    private static function clampDifficulty(string $d): string
    {
        $allowed = ['easy','medium','hard'];
        return in_array($d, $allowed, true) ? $d : 'medium';
    }

    private static function slugify(string $s): string
    {
        $s = strtolower(trim($s));
        $s = (string) preg_replace('/[^a-z0-9]+/', '-', $s);
        $s = trim($s, '-');
        return $s !== '' ? $s : 'lesson';
    }
}
