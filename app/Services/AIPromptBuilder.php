<?php
/**
 * AI Prompt Builder — assembles the system + user prompts that go to Claude.
 *
 * Centralised here so we have one place to update the contract when the
 * lesson schema evolves. Phase 2 implements only the smoke-test prompt;
 * Phase 3 will add full lesson generation, repair-pass, and detail-mode
 * variants.
 */

declare(strict_types=1);

namespace App\Services;

class AIPromptBuilder
{
    /**
     * Phase 2 smoke-test prompt: ask Claude to summarise an aircraft-system
     * passage as a single sample slide with a question gate. Output is a
     * JSON object with the same shape we'll use for real lesson slides in
     * Phase 3, so we can confirm the contract end-to-end.
     */
    public static function smokeTestSystem(): string
    {
        return <<<'PROMPT'
You are an aviation-systems instructor working with Captain Samic on the
Q400 study platform "AviatorTutor". You will be given an excerpt from a
Q400 aircraft-system manual. Produce ONE sample slide for the platform.

Return your response as a single JSON object — no prose, no markdown
fences. Schema:

{
  "slide_type": "concept" | "system" | "normal_op" | "abnormal" | "qrh",
  "title": string (max 80 chars),
  "body": string (2-4 short paragraphs of plain text, ~120 words total),
  "key_point": string (one-line memory hook, max 140 chars),
  "ops_relevance": string (one-line operational implication, max 160 chars),
  "question": {
    "prompt": string (a sharp single-answer question that tests
                      understanding of this slide, not trivia),
    "options": [string, string, string, string]   (exactly 4 options),
    "correct_index": integer (0-3),
    "explanation": string (1-2 sentence explanation of the correct answer
                            referencing the slide content)
  }
}

Rules:
- Be technically accurate. If the excerpt doesn't say something, don't
  invent it. Stay grounded in the source.
- Keep the body in plain text (no markdown). Newlines between paragraphs
  are fine.
- Make the question discriminating — incorrect options should be
  plausible distractors a tired captain might pick under fatigue.
- Output JSON only. No leading or trailing text. No code fences.
PROMPT;
    }

    /**
     * Build the user-message text. We tag the source so future variants
     * (PDF page range, ATA chapter, etc.) can be injected here without
     * changing the system prompt.
     */
    public static function smokeTestUser(string $sourceLabel, string $excerpt): string
    {
        return "SOURCE: " . $sourceLabel . "\n\n" .
               "EXCERPT:\n" . $excerpt . "\n\n" .
               "Now produce one sample slide following the schema above.";
    }
}
