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

    /**
     * Phase 3 — Full lesson generation system prompt. Asks Claude to
     * produce an entire lesson worth of slides, flashcards, and quiz
     * questions in one structured JSON object.
     *
     * Detail mode bumps the slide/flashcard quotas and requires at least
     * one scenario + abnormal slide; standard mode is lighter.
     *
     * @param string $depth   'standard' or 'detail'
     */
    public static function lessonSystem(string $depth = 'standard'): string
    {
        $isDetail   = $depth === 'detail';
        $slideMin   = $isDetail ? 14 : 8;
        $slideMax   = $isDetail ? 20 : 12;
        $flashcards = $isDetail ? 18 : 10;
        $quizCount  = $isDetail ? 10 : 6;
        $detailRule = $isDetail
            ? "DETAIL MODE: Include at least one slide of slide_type 'scenario' and at least one of slide_type 'abnormal' or 'qrh'. Quiz items: 50% must be abnormal-procedure scenarios."
            : "STANDARD MODE: Cover the system end-to-end without exhaustive abnormal-procedure depth.";

        return <<<PROMPT
You are an aviation-systems instructor working with Captain Samic on the
Q400 study platform "AviatorTutor". You will be given an excerpt from a
Q400 aircraft-system manual. Produce a COMPLETE LESSON for one system,
formatted as a single JSON object.

RETURN STRICT JSON ONLY — no prose, no code fences, no leading or
trailing text.

Schema:

{
  "system_name": string,                  // e.g. "Hydraulic Power"
  "ata_code": string,                     // e.g. "ATA29"
  "lesson": {
    "title": string,                      // <= 80 chars
    "summary": string,                    // 2-3 sentence overview
    "key_facts": [string, ...],           // 4-8 short bullets
    "must_know": [string, ...],           // 3-6 critical items
    "exam_traps": [string, ...]           // 3-6 common mistakes
  },
  "slides": [                             // {$slideMin}-{$slideMax} slides total
    {
      "slide_type": "intro" | "concept" | "system" | "normal_op" |
                    "abnormal" | "operational" | "qrh" | "scenario" |
                    "revision",
      "title": string,                    // <= 80 chars
      "body": string,                     // 2-4 short paragraphs, plain text
      "key_point": string,                // memory hook, <= 140 chars
      "ops_relevance": string,            // operational implication, <= 160 chars
      "question": {                       // present on every slide
        "prompt": string,
        "options": [string, string, string, string],
        "correct_index": integer (0-3),
        "explanation": string
      },
      "source_quote": string              // direct excerpt this slide
                                          // is grounded in (verbatim text
                                          // from the source); empty string
                                          // if purely synthesised
    }
    // ...
  ],
  "flashcards": [                         // {$flashcards} flashcards
    {
      "front": string,                    // question or term
      "back": string,                     // model answer (1-2 sentences)
      "hint": string,                     // optional hint, can be empty
      "expected_answer": string,          // canonical answer text used by
                                          // typed-answer AI grading
      "grading_rubric": string,           // 1-line rubric, e.g.
                                          // "must mention pump pressure
                                          //  and reservoir level"
      "difficulty": "easy" | "medium" | "hard"
    }
  ],
  "quiz": [                               // {$quizCount} multiple-choice questions
    {
      "question_text": string,
      "options": [string, string, string, string],
      "correct_index": integer (0-3),
      "explanation": string,
      "difficulty": "easy" | "medium" | "hard"
    }
  ]
}

Rules:
- Be technically accurate. If the source doesn't say something, do not
  invent it. Stay grounded in the excerpt — every 'abnormal' or 'qrh'
  slide MUST set source_quote to a verbatim substring of the excerpt.
- Plain text only — no markdown, no bullet characters, no emoji. Use
  newlines between paragraphs.
- Each question must have exactly 4 options and one correct_index.
- Distractors should be plausible to a tired captain under fatigue,
  not trivially wrong.
- {$detailRule}
- Keep titles concise. Keep paragraphs short. Captain Samic studies
  on a tablet between flights.

OUTPUT JSON ONLY. NO MARKDOWN FENCES. NO COMMENTARY.
PROMPT;
    }

    /**
     * Build the user message for a full-lesson generation call.
     */
    public static function lessonUser(string $sourceLabel, string $ataHint, string $excerpt): string
    {
        $hintLine = $ataHint !== '' ? "ATA HINT: " . $ataHint . "\n" : '';
        return "SOURCE: " . $sourceLabel . "\n" .
               $hintLine .
               "\nEXCERPT:\n" . $excerpt . "\n\n" .
               "Now produce the complete lesson JSON.";
    }
}
