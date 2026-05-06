# Phase 9 — Progress page polish + empty state

**Date:** 2026-05-06

## What shipped

1. **`POST /api/study-sessions/end`** — closes the most recent open `study_sessions` row for the (user, system, type) tuple, stamping `ended_at = NOW()` and `duration_secs = LEAST(GREATEST(NOW()-started_at, 0), 14400)` (4h hard cap so a forgotten tab can't inflate the total). Skips CSRF because `navigator.sendBeacon` can't set custom headers; auth is still required.
2. **`views/partials/study-session-heartbeat.php`** — small inline `<script>` partial that emits a `sendBeacon` to that endpoint on `pagehide` / `beforeunload` / `visibilitychange:hidden`. Set `$sessionType` + `$sessionSystemId` and include — the rest is wiring.
3. **Wired into the four player views** — `views/study/lesson_slides.php` (detail), `views/flashcards/study.php` + `study_v2.php` (flashcard), `views/quiz/take.php` (quiz). These mirror the `recordStudySession()` start-side calls from Phase 3, so the heatmap is now end-to-end accurate.
4. **Brand-new-account empty state on `/progress`** — when a learner has no completed/in-progress systems AND zero study time AND no study history, render a hero card with "Browse systems" / "Try a flashcard deck" CTAs. Seasoned learners never see it.

The "Total study time" stat now actually accumulates instead of always showing 0 hours. No need to hide the stat.

## Files changed

```
routes/web.php                                 +1 route
app/Controllers/ApiController.php              +60 lines (studySessionEnd)
views/partials/study-session-heartbeat.php     new (sendBeacon helper)
views/study/lesson_slides.php                  +5 lines (include heartbeat)
views/flashcards/study_v2.php                  +5 lines (include heartbeat)
views/flashcards/study.php                     +5 lines (include heartbeat)
views/quiz/take.php                            +5 lines (include heartbeat)
views/progress/index.php                       +25 lines (blank-slate hero + CSS)
```

## Verification checklist

- [ ] Brand-new account: visit `/progress` → blank-slate hero appears with the two CTA buttons. Cards below still render but read 0% / 0 hrs / 0%.
- [ ] After completing one lesson session: refresh `/progress` → hero is gone. "Study Time" climbs each visit.
- [ ] DevTools Network tab on `/study/{id}/lesson/{lid}`: navigate away → see a `sendBeacon` POST to `/api/study-sessions/end` (look in the "All" filter; sendBeacons appear as `text/plain` with status 204/200 and `keepalive`).
- [ ] Same on `/flashcards/{id}` and `/quiz/{id}`.
- [ ] Open a tab, leave it open for 10 minutes, close → `study_sessions.duration_secs` for that row should be ~600.
- [ ] Open a tab, leave it open for 6 hours, close → `duration_secs` clamps to `14400` (4h cap).
- [ ] If the user has any completed/in-progress system OR any logged study time, the blank-slate hero stays hidden.

## Trade-offs

- The duration cap is generous (4h) so a long real session isn't truncated; learners who genuinely study 6 straight hours just have one row split across multiple sessions if they navigate at all.
- We rely on `pagehide` rather than just `beforeunload` because iOS Safari only reliably fires `pagehide`. Triple coverage (`pagehide`, `beforeunload`, `visibilitychange:hidden`) is the cross-platform safe move.
- Sessions abandoned without any unload (browser crash, hard kill) stay open with `ended_at IS NULL`. Consider a nightly cleanup job that closes old rows with a default duration — out of scope for this phase.
