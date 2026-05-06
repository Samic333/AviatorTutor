# Phase 3 — Dashboard Data Wiring

**Status:** Core fix shipped. Browser QA pending after deploy.

## Root cause (from baseline F-3.1)

`study_sessions` table is read in 4 places (`DashboardController`, `ProgressController`, `SystemsController`, `AdminMetricsService`) but **never written anywhere in `app/`** before this fix. That's why every dashboard widget that reads it appeared empty — the queries are correct, the rows just don't exist.

## Fix

Added `recordStudySession(int $userId, ?int $systemId, string $sessionType)` to `App\Core\Controller`. It:

- de-dupes by minute (no heatmap inflation on page refresh),
- swallows errors (analytics path must never block the learner),
- also calls `User::updateStreak()` so the streak counter increments.

Wired into:

- `StudyController::lesson` → `'detail'`
- `FlashcardController::study` → `'flashcard'`
- `QuizController::take` → `'quiz'`

## Files changed

- `app/Core/Controller.php` — new `recordStudySession()` helper.
- `app/Controllers/StudyController.php` — calls it on lesson view.
- `app/Controllers/FlashcardController.php` — calls it on flashcard study.
- `app/Controllers/QuizController.php` — calls it on quiz start.

## Per-widget impact

| Widget | Status |
|---|---|
| Continue Studying | Already populated by `user_progress`. Will show real entries once a learner answers a slide gate (which writes `user_progress` via `ApiController@slideAnswer`). Dashboard's `LIMIT 3` query is correct. |
| Quiz Performance by System (chart) | Populated by `quiz_attempts WHERE status='completed'`. Will show data after Phase 8 quiz-scoring fix lands and the learner takes a quiz. |
| Study Activity (90-day heatmap) | **Now populated** by `study_sessions`. Cells will fill as the learner studies. |
| Recent Activity feed | **Now populated** by `study_sessions`. |
| Due for Review | Populated by `flashcard_reviews`. Will show entries after the learner reviews any flashcard. |
| Total study time on /progress | **Now populated** (was always 0). |
| Streak counter | **Now increments** via `User::updateStreak()` on every session insert. |

## Why I did NOT add session-end / duration tracking

A `beforeunload`-driven heartbeat would let us populate `duration_secs` accurately. But:

1. It's another moving part to debug if it breaks.
2. The dashboard widgets that need to land first (heatmap, recent activity, streak) only need `started_at`, not duration.
3. Total-study-time on /progress will under-report until duration is wired up — acceptable for now; flag for Phase 9 follow-up.

## QA checklist

- [ ] Deploy.
- [ ] Login as `samuel@q400study.local` (or any learner with no history).
- [ ] Open any lesson → return to dashboard. "Recent Activity" shows the lesson; "Study Activity" cell for today is non-zero intensity.
- [ ] Open a flashcard deck → return to dashboard. Recent Activity shows a flashcard session.
- [ ] Start a quiz → return to dashboard. Recent Activity shows a quiz session.
- [ ] Refresh the lesson page 5 times in a minute → heatmap intensity does NOT 5×; only one row inserted per minute per (user, system, type).
- [ ] Streak counter on dashboard increments by 1 after first study event of the day; doesn't double-count subsequent events on the same day.
- [ ] Visit `/progress` → "Total study time" is no longer "0m 0s" after at least one session (it'll still be small until we add duration tracking, but it'll be > 0 if duration_secs has any default value).

## Remaining gaps

- `duration_secs` column is never set (always NULL). Total-study-time on /progress underreports. Recommend a follow-up that adds `POST /api/study-sessions/{id}/end` called via `navigator.sendBeacon` on `beforeunload`.
- The streak calendar visualisation on the dashboard correctly reflects sessions, but the streak counter on `users.study_streak` is updated lazily on session insert. If a learner never re-opens a study page after a long gap, their stale streak value won't visually decrement until they study again. Cosmetic.
