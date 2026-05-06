# Phase 7 follow-up — typed-answer support in flashcards v2

**Date:** 2026-05-06
**Flag:** `flashcards_v2` (already on in prod)

## What shipped

Ported the typed-answer flow from the legacy `views/flashcards/study.php` into the deployed `views/flashcards/study_v2.php`. Cards with a non-empty `expected_answer` (`typeable=true`) now render a textarea + Submit / Skip buttons on the front face. Cards without one keep the simple Flip flow.

On submit:
1. POST `/api/flashcards/{id}/grade` with `typed_answer` + CSRF token.
2. The existing `ApiController::flashcardGrade` endpoint grades via `AIContentService::gradeAnswer` — AI when configured, offline string-overlap fallback otherwise. (No backend changes needed; `AIContentService::offlineGrade` already does the lowercase + token-set comparison the brief asked for.)
3. The card auto-flips to the back face, which now includes a verdict pill (CORRECT / NEEDS WORK + score/100) and feedback text above the canonical answer.
4. `Got it wrong` / `Got it right` buttons still drive the SRS review POST.

Mode coexistence: `data-typeable="0|1"` on every `.fcv2-card` makes the typeable + plain modes pick the right UI per card. Pointer-drag swipe is suppressed when the gesture starts inside the typed-answer UI so typing/clicking buttons doesn't trigger a swipe.

## Files changed

```
views/flashcards/study_v2.php           rewrite (added typed UI + verdict markup)
public/assets/js/flashcards-v2.js       +75 lines (submitTyped, click delegates, swipe-suppression)
public/assets/css/flashcards-v2.css     +50 lines (typed input + verdict styles)
```

No backend changes. `flashcardGrade` already handled both AI + offline fuzzy match.

## Verification checklist

- [ ] `/flashcards/1` (a system that has at least one card with `expected_answer` set) — top-of-deck typeable card shows textarea + Submit + Skip.
- [ ] Typing a near-match and clicking Submit → card flips, green CORRECT badge with score, then `Got it right` advances to next.
- [ ] Typing nonsense and clicking Submit → orange NEEDS WORK badge with feedback; card still flipped so learner can compare; can grade `Got it wrong` to resurface.
- [ ] Skip & flip → flips without grading; learner uses the legacy right/wrong buttons.
- [ ] Cards without `expected_answer` still show the original Flip button — no regression.
- [ ] Cmd/Ctrl+Enter inside the textarea submits.
- [ ] Pointer-drag inside the textarea does NOT initiate a swipe (typing stays a normal text interaction).
- [ ] Server-down case: if the grade endpoint 500s, the inputs re-enable so the learner can retry.

## Notes

- The AI/offline fallback selection happens inside `AIContentService::gradeAnswer`. Prod `app.local.php` has `anthropic_api_key` set (per CPANEL_KNOWLEDGE.md), so live grading uses Claude unless the API errors, in which case it falls back to the offline grader and tags `source: 'offline_fallback'`.
- `flashcard_attempts` still records every submit (typed answer, score, is_correct) so `/progress` and the unlock-criteria query keep working unchanged.
