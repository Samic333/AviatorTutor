# Phase 8 — Quiz Scoring Zero-Bug Fix

**Status:** Core scoring bug fixed. Selector + layout-consistency parts are deferred to a follow-up. Browser QA pending.

## Root cause

`QuizController::submit` lines 159–168 (pre-fix) compared two JSON-encoded values that could never be equal:

```php
$correctAnswer = json_decode($question['correct_answer'], true);   // ["28 VDC"]
$isCorrect = json_encode($correctAnswer) === json_encode($answer); // '["28 VDC"]' === '"28 VDC"' → ALWAYS FALSE
```

The frontend (`views/quiz/take.php` line 398–401) sends a **single string** per question (`answer: answers[q.id]`). The DB stores `correct_answer` as a **JSON-encoded array** (`'["28 VDC"]'` per the seed data and schema's `JSON` column type). They never match, so every quiz scores 0% — exactly the user's complaint.

## Fix

`QuizController::submit` rewritten to:

1. Decode `correct_answer` defensively (handles JSON-array, JSON-scalar, and legacy bare-string storage).
2. Normalise both sides to a sorted array of trimmed lowercased strings via a pure `$normalise` closure.
3. Compare arrays with `===` after sort — works for single-answer MCQ, multi-select, true/false, and string-equality questions.
4. Compute `$totalQuestions` from `quiz_questions` table (filtered to `status = 'published'`), not from the user's submission count, so a learner who skips half the quiz doesn't score 100% on three answered questions.
5. Defensive denominator fallback prevents divide-by-zero if a quiz has no published questions.

## Bonus side effects

- `correct_answer` lookups are now case-insensitive and whitespace-tolerant (`"28 VDC"` matches `" 28 vdc"` etc.) — useful when admin-entered options have inconsistent casing.
- Multi-select questions where the correct answer is `["A","C"]` and the user submits `["C","A"]` now match. (Frontend currently only renders radios so this is mostly future-proofing for when the v2 take view supports checkboxes.)

## Files changed

- `app/Controllers/QuizController.php` — `submit()` block from line ~129 onward.

## Files NOT changed (yet) — deferred

- `views/quiz/index.php` — adding search + aircraft/system dropdowns (brief item 27). Subjects-page Phase 4 will produce a reusable picker partial; quiz index can re-use it then.
- `app/Controllers/QuizController.php` `take()` and `result()` — wiring through `studyChromeData(...)` so the quiz player renders inside the v2 study shell (brief item 28/29). Belongs to Phase 13.
- `views/quiz/result.php` — already decodes user/correct answers per row (lines 90–96), so once scoring works the display is automatically correct. No change needed.

## QA checklist

- [ ] Take a quiz, answer correctly → score > 0%.
- [ ] Take a quiz, answer incorrectly → "correct answer" column shows the actual right answer, not the user's pick.
- [ ] Skip half the questions → score reflects total quiz length, not submitted count.
- [ ] Mixed case answers (if any in seed data) score correctly.
- [ ] `quiz_attempts.score` row is set; dashboard "Avg Quiz Score" updates after the next dashboard load.
- [ ] `quiz_answers.is_correct` is 1 for correct, 0 for wrong (check via SQL spot-check).

## Risks

- Case-insensitive matching is more lenient than the previous (broken) behavior, which is fine for the user but means admin-authored "trick questions" that hinge on capitalisation will now mark either case as correct. Acceptable for an aviation study tool.
- If any `quiz_questions.correct_answer` is stored in a format other than (a) JSON array of strings, (b) JSON scalar, (c) bare string, the normaliser falls through to `(string) $value`. Worth a one-time audit query before launch:
  `SELECT id, question_text, correct_answer FROM quiz_questions WHERE correct_answer NOT REGEXP '^\\[' AND correct_answer NOT REGEXP '^\".*\"$' AND correct_answer NOT REGEXP '^[a-zA-Z0-9]';`
