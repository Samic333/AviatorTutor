# Phase 13 — Unified study shell rollout (Quiz)

**Date:** 2026-05-06
**Flag:** `study_chrome_v2` (already true on prod)

## What shipped

`StudyController` (slides, mnemonics, mind-map, deep-notes) + `FlashcardController` already rendered with the `study` layout when `study_chrome_v2` was on. **Quiz didn't.** It rendered with the legacy `pilot` layout (sidebar + topbar) — the brief's "Quiz inside unified design" complaint.

Wired Quiz into the same chrome:

1. **`QuizController::take`** now fetches the quiz's parent system, builds the same chrome data block (breadcrumb + mode switcher + lesson drawer), and renders with the `study` layout when the flag is on. Falls back to `pilot` if the quiz has no `system_id` (orphan quizzes still work).
2. **`QuizController::result`** mirrors the same logic so the post-submit page lives inside the chrome too.
3. **`buildStudyChromeData()` + `loadSystemForChrome()` helpers** added local to `QuizController` — same shape as `FlashcardController::buildStudyChromeData` so all six tabs render with consistent props. Uses `$modeKey === 'quiz'` so the Quiz tab is the active one in the mode switcher.

The mode switcher links from inside a quiz now go to the right surface for the quiz's parent system: Slides → `/study/{sys}`, Flashcards → `/flashcards/{sys}`, Mnemonics → `/study/{sys}/mnemonics`, Mind Map → `/study/{sys}/mind-map`, Deep Notes → `/study/{sys}/deep-notes`. All six tabs work.

## Files changed

```
app/Controllers/QuizController.php   +120 lines (take/result wired, buildStudyChromeData + loader helpers)
```

No view changes — `views/quiz/take.php` and `views/quiz/result.php` are layout-agnostic; they just use whatever layout they're rendered with.

## Verification checklist

- [ ] `/quiz/{id}` (a quiz with a `system_id`) — renders with the study chrome topbar (breadcrumb on the left, mode switcher with six tabs centered, Quiz tab active).
- [ ] All five sibling tabs are clickable and route correctly to the parent system.
- [ ] After submitting → `/quiz/{id}/result/{aid}` — same chrome, Quiz tab still active.
- [ ] Pilot sidebar is gone on these pages (study layout doesn't render it).
- [ ] Settings drawer still opens via the cog (the partial is included from `pilot.php` and `study.php` independently).
- [ ] An orphan quiz with `system_id IS NULL` (if any exist) still renders with the legacy pilot layout — no crash.

## Notes

- The quiz index page (`/quiz`) keeps the legacy pilot layout because it's a catalog of quizzes, not a study surface. The mode switcher only makes sense inside a system context.
- The Phase 9 study-session-end heartbeat (added to `views/quiz/take.php`) keeps working in either layout — it's a `<script>` block that doesn't depend on layout chrome.
