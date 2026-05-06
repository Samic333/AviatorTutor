# Phase 15 — Final E2E QA + cleanup

**Date:** 2026-05-06

## What shipped

1. **Sidebar Search link removed.** `pilot.php`'s `$navOptional` no longer includes the `Search` entry. The topbar search added in Phase 10 lives on every signed-in page and replaces it. The `/search` page itself still works for direct visits and bookmarks.
2. **Updated rolled-up report.** `docs/FINAL_STUDY_PLATFORM_QA_REPORT.md` now reflects all 15 phases shipped, with the Session-1/Session-2 file lists separated.
3. **No file deletions** — per the brief's rule "Don't delete files until their replacement is confirmed live for ≥1 deploy cycle":
   - `views/study/detail.php` is still present. Phase 5's redirect just landed; one deploy cycle hasn't passed. Remove in a follow-up after the redirect has been live for a week.
   - `views/flashcards/study.php` is still present and still picked up as the fallback when `flashcards_v2=false`. Prod has the flag on, but deletion is risky until at least one rollback dry-run confirms the v2 view handles every legacy-view scenario. Same follow-up window.
4. **Lint sweep.** All 17 PHP files modified across both sessions pass `php -l`. All 3 JS files modified pass `node -c`. No syntax errors anywhere.

## Final E2E QA flow (manual — for prod after deploy)

Run as `samuel@q400study.local` on production with all feature flags as listed in prod `config/app.local.php`:

1. **Sign in** → land on `/dashboard`. Topbar search visible right of the title. Sidebar shows Dashboard / My Subjects / Study / Flashcards / Quizzes / Progress (no Search). Settings cog still works.
2. **Type "fuel" in topbar search** → grouped dropdown opens within 250ms. Click a Lesson result → lands on `/study/{sys}/lesson/{lid}` (the new player), not the old long page.
3. **`/systems`** → picker renders with four controls: search, aircraft, category, jump-to-system. Filtering works without page reload. Click an enrolled subject tile → lands on `/study/{sys}/lesson/{firstLessonId}` via Phase 5 redirect.
4. **Inside a lesson** (`/study/{sys}/lesson/{lid}`) → study chrome topbar visible (breadcrumb + 6 tabs). Slide hover near "Next" shows no blue rectangle. Open DevTools Network tab, navigate away → `sendBeacon` POST to `/api/study-sessions/end` fires (`type=detail`, `system_id=...`).
5. **`/flashcards/{sys}`** → v2 deck renders. Cards with `expected_answer` show textarea + Submit/Skip buttons. Submit a near-match → green CORRECT verdict + auto-flip. Plain cards still flip via the Flip button.
6. **`/quiz/{id}`** → renders with study chrome (Quiz tab active). Mode switcher takes you to Slides / Flashcards / Mnemonics for the parent system. Answer a quiz, submit → score is non-zero on correct answers. Result page lives in study chrome too.
7. **`/study/{sys}/mnemonics`** → top filter strip (keyword + system jumper). Each card has a stable `id="m-{id}"` anchor. Topbar search → click Mnemonic result → lands on the right card with accent border.
8. **`/study/{sys}/mind-map`** → tree fits viewport on first paint. Click any node → right-side detail panel populates. Toolbar `+` / `−` / `⤢` work. Wheel zooms at the cursor. On mobile, panel slides up as a bottom sheet.
9. **`/progress`** (brand-new account) → blank-slate hero with "Browse systems" / "Try a flashcard deck" CTAs. After a few minutes of use, refresh → Study Time stat is non-zero (Phase 9 heartbeat).
10. **`/dashboard`** (after some study) → Continue Studying populated. Recent Activity populated. Study Activity heatmap has cells with intensity.
11. **`⌘K` from any page** → focuses the topbar search.

## Carry-overs (out of scope, filed as follow-ups)

- **Mnemonics content** — only one mnemonic per system in production (verified via SSH+SQL in Phase 11). Admins should add 3–5 per system via the AI generator or `/admin/mnemonics`. ~36 rows total to add for Q400's 9 systems with content.
- **Dashboard streak heatmap** — 90 separate `queryOne` calls per page load. Phase 14 flagged but did not refactor. Replace with a single grouped query when `study_sessions` volume warrants the optimisation.
- **`views/study/detail.php` + `views/flashcards/study.php` deletion** — wait one full deploy cycle (≥1 week) after this batch ships, then delete.
- **`add_subject_flow` flag** — false in prod. The modal is wired but the workflow isn't validated end-to-end.
- **Aircraft scoping on `systems`** — the `systems` table has no `aircraft_id` column; everything is implicitly Q400. Once a second aircraft ships content, add the column + migration so the picker's aircraft filter does real work.

## Sign-off

All 15 phases of `Prompts/v2.0-study-platform-overhaul.md` shipped in source. Production feature flags verified live and mirrored locally. PHP + JS lint clean. Per-phase QA notes in `docs/qa/phase*.md`. Recommend: deploy as one batch (single git commit per phase or one bundled), test the E2E flow above, then delete the two orphaned views after ≥1 week.
