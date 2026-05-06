# Phase 5 — Kill the Old Study Flow (re-prioritised earlier per baseline F-5.1)

**Status:** Implemented. Browser QA pending.

## What changed

`StudyController@detail` is no longer a renderer — it's a smart redirect.

When a user hits `/study/{id}`, they are now 302'd to the right place automatically:

1. **In-progress lesson** — most-recently-touched `user_progress.status='in_progress'` row → `last_studied DESC LIMIT 1`. Best for "Continue Studying."
2. **Earliest unfinished lesson** — first lesson by `sort_order` that the user hasn't completed yet. Best for "Start" / first visit.
3. **First published lesson at all** — the absolute first lesson, ignoring user state. Fallback for fresh systems / brand-new users.
4. **`/systems/{id}`** — the system metadata page, if the system has zero published lessons. Better than 404 because the learner still sees flashcard count, quiz count, description.

## Why this fixes nine things at once

The baseline (F-5.1) found 9 view files linking to `/study/{id}`:

- `views/dashboard/index.php:179, 255` — Continue Studying, Suggested Next
- `views/systems/index.php:106, 110`
- `views/systems/show.php:110, 123`
- `views/study/lesson_slides.php:78, 141, 338, 341, 378` — back-buttons inside the new player
- `views/study/revision.php:266`
- `views/partials/system-picker.php:51, 75`
- `views/admin/ai_job_show.php:85`

Editing all 9 was brittle (some "Back to system" links were intentionally meant to leave the player). Redirecting at the controller fixes every one of them in a single edit, and preserves SEO + bookmarks (302, not 404).

## Files changed

- `app/Controllers/StudyController.php` — `detail()` rewritten from a 90-line view-render to a 60-line redirect resolver.

## What was NOT changed (deliberate)

- `views/study/detail.php` is orphaned but **not deleted**. Per the brief: "Remove or deprecate old components only after confirming replacements work." Will delete in Phase 15 once full E2E QA confirms nothing depends on it. (Grep confirms no other `view('study/detail', …)` calls in `app/`.)
- `StudyController@revision` — `/study/{id}/revision` still renders `views/study/revision.php` (3-/5-/10-min review mode). The brief did not call this out as part of the "old long-page study" complaint, and it's a distinct feature, so it's preserved.
- The sidebar "Study" link still goes to `/systems` (the systems library). Adding a proper selector page is Phase 4's territory; leaving this alone keeps the change surgical.

## QA checklist

- [ ] Click any "Continue" link from /dashboard — should land directly on a slide, not the old long page.
- [ ] Visit `/study/1` directly in the URL bar — 302s to `/study/1/lesson/{firstLessonId}`.
- [ ] As a learner with no progress on system 5, visit `/study/5` — lands on the first lesson by sort_order.
- [ ] As a learner with progress on lesson 9 of system 1, visit `/study/1` — resumes on lesson 9.
- [ ] Open a system that has no lessons (if any exist) — lands on `/systems/{id}` not a 404.
- [ ] Old chapter-list view is no longer reachable through any user click.

## Risks I'm aware of

- If a learner had bookmarked `/study/5` expecting the chapter overview, they now get a slide instead. This is **the intended behavior** per the brief, but worth noting in the user-facing release notes if any.
- If `lesson_slides` is empty for a lesson (no slides authored), the new slide player will render its own empty state. That's lesson-author-side cleanup, not a regression caused by this change.
