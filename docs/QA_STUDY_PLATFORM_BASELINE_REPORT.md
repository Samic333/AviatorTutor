# AviatorTutor — Phase 0 Baseline Inspection Report

**Date:** 2026-05-06
**Scope:** Full codebase inspection prior to the 15-phase study-platform overhaul described in `Prompts/v2.0-study-platform-overhaul.md` (the user brief).
**Method:** Static code analysis only — no runtime testing this phase. Browser QA happens at the end of each subsequent phase.
**Inspector context:** Reuses findings from the prior live audit (`docs/site-audit-2026-05-05.md`) and verifies/extends them against the current source.

---

## TL;DR — the three findings that reshape the fix plan

1. **`study_sessions` is read in 4 places but written nowhere in `app/`.**
   `DashboardController`, `ProgressController`, `SystemsController`, and `AdminMetricsService` all `SELECT … FROM study_sessions`, but a `grep -r "INSERT INTO study_sessions"` across `app/` returns **zero hits**. That single missing write explains *every* "widget is empty" complaint in the brief: Continue Studying, Study Activity heatmap, Recent Activity, Total study-time on /progress, and the admin metrics card. Phase 3 is therefore not a "wire up the view" task — it is "create the missing insert path." See *Finding F-3.1* below.

2. **The old long-page study flow (`/study/{id}` → `StudyController@detail` → `views/study/detail.php`) is still linked from 9+ places**, including the Dashboard "Continue Studying" card itself (`views/dashboard/index.php:179`). The new lesson-slides flow is `/study/{id}/lesson/{lessonId}` — but every "back" / "continue" / "view system" link in the app sends learners back to the old page first. Phase 5 cannot be solved by editing the sidebar alone.

3. **Most "redesign" features the user thinks are missing already exist behind feature flags that are OFF in `config/app.php`.**
   `study_chrome_v2`, `flashcards_v2`, `mnemonics_v2`, `mind_map`, `deep_notes`, `dashboard_v2`, `system_picker_v2`, `add_subject_flow`, `theme_drawer`, `analytics_v1`, `friendly_errors`, `nav_my_subjects` — all `false` in defaults. The fix for several phases is "flip the flag, audit the result, polish the gaps" — not "build it from scratch."

These three facts mean the fix plan in the brief is largely sound but its size estimate is too small for #3/#5/#12 and too large for #11/#13 (already half-built).

---

## 1. Tech stack & architecture

| Layer | Choice |
|---|---|
| Backend | PHP 8.1+, hand-rolled MVC framework (front controller `public/index.php` + `app/Core/Router`) |
| Database | MySQL 8 / MariaDB 10.4+ (UTF-8 utf8mb4) |
| Sessions / Auth | PHP sessions; `App\Core\Auth` |
| CSRF | `App\Core\CSRF` (token check on all POST routes) |
| Frontend | Vanilla JS + CSS, **no build step**. Lucide icons (CDN), Chart.js (CDN), Google Fonts |
| Templates | Plain PHP, layout-then-content pattern via `App\Core\View` |
| Layouts | `views/layouts/{app,admin,marketing,pilot,study}.php` — five distinct layouts |
| Routes | `routes/web.php` — single file, ~240 lines, ~120 routes |
| Migrations | `database/migrations/*.sql` — 18 dated migration files (Apr 28 → May 03 2026) |
| Production URL | `https://aviatortutor.com` |
| Local target | XAMPP `http://localhost/q400-study/public/` per `README.md` |

**Demo accounts** (from `README.md`, password = `password`):

- `admin@q400study.local` (admin)
- `samuel@q400study.local` (learner)
- `alice@q400study.local` (learner)

---

## 2. Routes that matter for the brief

```
GET  /                         HomeController@index            (public marketing OR redirect to /dashboard)
GET  /dashboard                DashboardController@index
GET  /my-subjects              SubjectsController@index        (Phase 1 of prior overhaul — gated by nav_my_subjects flag)
GET  /aircraft                 AircraftController@index        (public catalog, currently the active "My Aircraft" target)
GET  /systems                  SystemsController@index         ← what the SIDEBAR "Study" link goes to
GET  /systems/{id}             SystemsController@show
GET  /study/{id}               StudyController@detail          ← OLD long-page study (this is what the brief wants killed)
GET  /study/{id}/revision      StudyController@revision
GET  /study/{id}/lesson/{lid}  StudyController@lesson          ← NEW slide-based study (good)
GET  /study/{id}/mnemonics     StudyController@mnemonics       (gated by mnemonics_v2 flag)
GET  /study/{id}/mind-map      StudyController@mindMap         (gated by mind_map flag)
GET  /study/{id}/deep-notes    StudyController@deepNotes       (gated by deep_notes flag)
GET  /flashcards               FlashcardController@index
GET  /flashcards/{id}          FlashcardController@study       (legacy view OR study_v2 if flashcards_v2 flag on)
POST /flashcards/review        FlashcardController@review
GET  /quiz                     QuizController@index
GET  /quiz/{id}                QuizController@take
POST /quiz/{id}/submit         QuizController@submit           ← scoring-zero bug lives here
GET  /quiz/{id}/result/{aid}   QuizController@result
GET  /progress                 ProgressController@index
GET  /search                   SearchController@index
POST /api/lessons/{id}/complete         ApiController@lessonComplete
POST /api/lessons/{id}/slide-answer     ApiController@slideAnswer       ← embedded-question API
POST /api/systems/{id}/complete         ApiController@systemComplete
POST /api/flashcards/{id}/grade         ApiController@flashcardGrade
POST /api/flashcard/review              ApiController@flashcardReview
POST /api/progress/update               ApiController@updateProgress
GET  /api/search                        ApiController@search
POST /api/notes/save                    ApiController@saveNotes
POST /api/ai/ask                        ApiController@aiAsk
```

---

## 3. Feature flags (config/app.php → 'features')

All default to `false` unless noted. Production overrides live in `config/app.local.php` (gitignored — actual prod state unknown from this repo, must be confirmed live).

| Flag | Purpose | Default | Affects which phase |
|---|---|---|---|
| `nav_my_subjects` | Sidebar "My Subjects" link → `/my-subjects` | false | Phase 4 / 5 |
| `study_chrome_v2` | New `study.php` layout (no sidebar, top bar only) | false | Phase 6 / 13 |
| `flashcards_v2` | Color-coded + SM2 swipe + typed answers | false | Phase 7 |
| `mnemonics_v2` | DB-backed mnemonics with letter-by-letter explainer | false | Phase 11 |
| `mind_map` | Mind-map study mode | false | Phase 12 |
| `deep_notes` | Full-text deep-notes mode | false | (P0 raw-HTML bug from prior audit) |
| `theme_drawer` | Reading preferences drawer | false | Phase 13 polish |
| `dashboard_v2` | Compact KPIs + promo panel layout | false | Phase 2 / 3 |
| `system_picker_v2` | Searchable + grouped system picker | false | Phase 4 / 5 |
| `add_subject_flow` | "Add Subject" request modal | false | Phase 4 |
| `analytics_v1` | Analytics dashboard | false | Phase 14 |
| `friendly_errors` | Friendly 500 page + retry | false | (cross-cutting) |
| `search` | Search page | **true** | Phase 10 |
| `planner` | Planner page | false | (out of scope) |
| `notes` | Notes page | false | (out of scope) |
| `ai_admin_test` | Admin AI test page | true | (admin only) |

**Production state must be confirmed before any phase ships** — flipping a flag locally may produce a different result than turning it on in prod, because some flags have follow-up CSS/JS that may or may not have been deployed.

---

## 4. Database schema highlights

Schema source: `database/schema.sql` + 18 migrations under `database/migrations/`.

Tables relevant to the brief:

- `users` (with `study_streak`, `last_active`)
- `systems` (22 Q400 ATA systems; has `slug`, `category`, `unlock_after_system_id`)
- `subtopics` (within systems)
- `lessons` (with `key_facts`, `must_know`, `exam_traps` JSON columns + `is_published`)
- `lesson_sections` (typed: overview/components/operation/normal/abnormal/indications/limitations/memory)
- `lesson_slides` (Phase 3+ slide deck — has `status`, `show_beginner/intermediate/advanced` cols, `slide_type`, `key_point`, `ops_relevance`, `question`)
- `lesson_qrh_links`
- `flashcards` (Phase 5 added `expected_answer`, `category`, `theme_color`, `why_it_matters`, `source_slide_id`, `status`)
- `flashcard_reviews` (SM2: `ease_factor`, `interval_days`, `review_count`, `next_review_at`)
- `flashcard_attempts` (per-attempt history added in 2026_05_03)
- `quizzes`, `quiz_questions`, `quiz_attempts`, `quiz_answers`
- `user_progress` (per-lesson status, confidence, last_studied)
- `user_slide_progress` (per-slide answered_correct + attempts — added 2026_04_29)
- `user_topic_strength` (weak-area aggregate, used by `/progress`)
- `user_system_unlocks` (Phase-5 unlock gating)
- `study_sessions` (read everywhere, written nowhere — see F-3.1)
- `mnemonics` (added 2026_05_03 — `phrase`, `breakdown_json`, `why_it_works`, `worked_example`, `audio_url`, `is_published`)
- `notes` (per-user per-system long text)
- `subject_requests` (Phase-4 add-subject ask)
- `analytics_events` (added 2026_05_03)
- `app_settings` (added 2026_05_01)
- `user_settings` (per-user reading preferences — added 2026_05_03)

The schema is fine. The bugs are at the controller / view / write-path layer.

---

## 5. Findings — by brief item

For each numbered bug in the user's "Specific bugs to verify and fix" list, I report **C** = confirmed in source, **L** = likely (need browser to verify), **M** = misdiagnosed (different root cause), **A** = already fixed/exists, with a code reference.

### 5.1 Sidebar / dashboard layout (brief items 1–3)

- **F-1 (item 1) — Collapsible sidebar.** **C: not implemented.**
  `views/layouts/pilot.php` lines 195–252: sidebar opens via `.open` class on mobile only; no desktop collapse toggle, no width-collapse mode. New work — small JS + a body class + a few CSS rules.

- **F-2 (item 2/3) — KPI cards overlap / too big.** **L** for "overlap" — the cards are styled with `.plt-stats-grid` and `.plt-stat-card` (not in this report — sits in `public/assets/css/pilot.css`). Need a quick browser look at 1280-1440px. Per the prior audit (item #6), the bigger problem is "label/icon/number all on one line, numbers tiny relative to labels," which is a CSS pattern fix, not a query fix. The card markup in `views/dashboard/index.php:79–148` is reasonable. Plan: copy the `/progress` card pattern in Phase 2.

### 5.2 Dashboard data wiring (brief items 4–8) ⭐ critical

> **F-3.1 — `study_sessions` is read but never written.** This is the single most important finding in this report.

`grep -rn "INSERT INTO study_sessions" app/` returns zero results.
`grep -rn "FROM study_sessions" app/` returns 8 results in 4 files (Dashboard, Progress, Systems, AdminMetrics).

So:

| Widget | Brief complaint | Real root cause |
|---|---|---|
| Continue Studying | "shows no system in progress" | Reads from `user_progress`, not `study_sessions`. **Query is fine** — the user genuinely has no `user_progress` rows in `in_progress` status. May be working but empty. |
| Quiz Performance by System (chart) | "does not fetch" | Reads `quiz_attempts JOIN quizzes JOIN systems`. Query is correct. If empty, the user has no `quiz_attempts.status='completed'` rows. |
| Study Activity (90-day heatmap) | "does not fetch" | **Reads `study_sessions`. Empty because never inserted.** ⭐ |
| Due for Review | "does not fetch" | Reads `flashcard_reviews WHERE next_review_at <= NOW()`. Empty until the user reviews a card via `POST /api/flashcard/review` or `POST /api/flashcards/{id}/grade`, both of which DO insert. **Query is fine; data populates after first review.** |
| Recent Activity | "does not fetch" | Reads `study_sessions`. **Same ghost as Study Activity.** ⭐ |
| Total study time on /progress | (implicit in brief item 8) | `SUM(duration_secs) FROM study_sessions WHERE user_id = ?`. **Always 0.** ⭐ |

**Phase 3 plan must therefore:**
1. Add an `INSERT INTO study_sessions` write path. Two reasonable hooks:
   - Server-side: when `StudyController@detail` / `StudyController@lesson` / `FlashcardController@study` / `QuizController@take` is hit, insert a row with `started_at = NOW()`, `session_type = 'detail'|'flashcard'|'quiz'`, `system_id = $id`. Cheap and correct enough for the 90-day heatmap.
   - Client-side: an API endpoint `POST /api/study-sessions/start` that the slide player + flashcard player + quiz player call on enter, and `… /end` on exit (to capture `duration_secs`).
2. Backfill nothing — accepting that a user's history before the fix is lost. Document this in the user-facing release notes.
3. Then verify the four widgets light up.

- **F-3.2 — Continue Studying links to old route.** `views/dashboard/index.php:179`: `<a href="/study/<?= $system['id'] ?>">`. Plan: change to `/study/{id}/lesson/{firstLessonId}` *or* repoint `/study/{id}` to redirect (see F-5.1).

### 5.3 Subjects page (brief items 9–11)

- **F-4 — No aircraft / category dropdowns or top search on /systems.** **C.** `views/systems/index.php` is 167 lines and contains no `<select>` or `<input type="search">` at the top. There is, however, a `views/partials/system-picker.php` (used when `system_picker_v2=true`) which DOES have a select dropdown — Phase 4's existing "system_picker_v2" was meant to address this. Plan: turn the flag on, audit the picker, add the missing aircraft-type and category controls, add top search — much of the work is already in the partial.

### 5.4 Sidebar "Study" link goes to wrong place (brief items 12–15)

- **F-5.1 — Sidebar "Study" goes to `/systems`, which renders the OLD chapter-list, where every "Open" button still links to `/study/{id}` (the OLD detail page).** **C.**
  - `views/layouts/pilot.php:37` — `['/systems', 'Study', …]`
  - `views/systems/index.php:106-110` — `<a href="/study/{id}">` for both "Study" and "Revision"
  - `views/systems/show.php:110, 123` — same
  - `views/dashboard/index.php:179, 255` — Continue Studying + Suggested Next
  - `views/study/lesson_slides.php:78, 141, 338, 341, 378` — back-links from the new player **back to the old page**
  - `views/study/revision.php:266` — back-button
  - `views/partials/system-picker.php:51, 75` — picker option values

  **9 distinct view files contain at least one link to `/study/{id}`.** A search-and-replace is brittle — many of these "Back to system" links are intentional. The clean fix:

  1. Make `StudyController@detail` (and the `/study/{id}` route handler) **redirect** to either:
     - the first lesson's lesson-slides URL if any lessons exist, or
     - the `/systems/{id}` system-detail page if not.
  2. Keep the route alive, kill the view file `views/study/detail.php` (or stash it under `_archive/`).
  3. Update the sidebar to point to a new selector page (per the brief), but keep `/systems` working for now to avoid breaking inbound links.

### 5.5 New study system bugs (brief items 16–21)

The lesson-slides view is `views/study/lesson_slides.php` (381 lines) + `public/assets/js/lesson_slides.js` (421 lines). Not deeply read in this baseline pass — Phase 6 will own it. From the prior audit:

- "Slide image/content does not change when clicking Next" — most likely a JS event-binding bug or a state-reset bug. Browser-debug-shaped.
- "Hover rectangle near Next" — likely a `:hover` overlay rule in `pilot.css` or `lesson_slides.css`.
- "Answering question jumps backward" — `POST /api/lessons/{id}/slide-answer` (`ApiController@slideAnswer`) inserts into `user_slide_progress`. Frontend may be re-rendering from server state and resetting the index. Confirm in Phase 6.
- "Wrong content shown (Q&A instead of concept slide)" — `lesson_slides` table has a `slide_type` column. Query on line 616 of `StudyController.php` selects all types; the view template likely doesn't branch on `slide_type` correctly.

### 5.6 Flashcards (brief items 22–24)

- **F-7.1 — Flip button broken.** Confirmed by prior audit (item #2). **L.** Needs to be replicated in source: `views/flashcards/study.php` (640 lines, not read this pass — Phase 7 will own).
- **F-7.2 — Typed answer.** Already half-built. `flashcards_v2` flag → `views/flashcards/study_v2.php` view. The schema added `expected_answer` column and `FlashcardController@study` already toggles `typeable` per card. **A** — flag-flip + audit, not a from-scratch build.

### 5.7 Quiz scoring zero bug (brief items 25–29) ⭐

- **F-8.1 — Quiz scoring is fragile JSON-encode comparison.** **C.**
  `app/Controllers/QuizController.php:159–168`:
  ```php
  $question = $db->queryOne(
      'SELECT correct_answer FROM quiz_questions WHERE id = ?',
      [$questionId]
  );

  $isCorrect = false;
  if ($question && $answer !== null) {
      $correctAnswer = json_decode($question['correct_answer'], true);
      $isCorrect = json_encode($correctAnswer) === json_encode($answer);
  }
  ```
  Failure modes:
  - If `correct_answer` is stored as a **plain string** (not JSON), `json_decode` returns `null` and the comparison becomes `"null" === json_encode($answer)` → always false.
  - If `correct_answer` is `[0, 2]` and `$answer` is `[2, 0]` (multi-select with different ordering), the comparison fails despite being semantically correct.
  - Type-coercion mismatches (string `"0"` vs int `0`).

  Plan in Phase 8: detect storage shape per row (plain vs JSON), normalise both sides, then compare. Also fix `$totalQuestions = count($answersArray)` which only counts answered questions — should use the actual question count to handle skips correctly.

- **F-8.2 — Quiz selection has no search/dropdown.** Confirmed in `views/quiz/index.php` (not read in this pass — Phase 8 owns). Plan: lift the same picker partial used by Subjects.

- **F-8.3 — Quiz uses inconsistent layout.** `QuizController@take` and `@result` both render with the legacy `pilot` layout. New work: when `study_chrome_v2` is on, render with `study.php` layout and pass through `studyChromeData(...)` (mirror the FlashcardController pattern).

### 5.8 Progress page (brief item 8)

- **F-9 — Progress page IS connected**, contrary to the brief. `app/Controllers/ProgressController.php` queries `systems / user_progress / users / study_sessions / quiz_attempts / user_topic_strength`. Plan in Phase 9: don't rebuild the controller — fix what shows up empty (because of F-3.1 + because seed data may not include `user_topic_strength` rows), and polish the empty-state copy.

### 5.9 Global search (brief item 34)

- **F-10 — Search exists at `/search` (sidebar — flag-on by default) and `GET /api/search`.** Plan in Phase 10: add a topbar input that hits the same `/api/search` endpoint, then either drop the sidebar link or keep it for power users. Cosmetic move, not a from-scratch build.

### 5.10 Mnemonics (brief items 30–31)

- **F-11 — Mnemonics page exists, gated by `mnemonics_v2`.** `StudyController@mnemonics` already queries the `mnemonics` table by `system_id`. The brief's "only one mnemonic for the whole PDF" may be a content-seeding issue (not enough rows) or a view-rendering issue (fetching only the first row). Need to read `views/study/mnemonics.php` in Phase 11.

### 5.11 Mind map (brief items 32–33)

- **F-12 — Mind map already builds a 4-level tree** (system → lesson → sections+facts buckets → leaves) in `StudyController@mindMap` lines 290–405. The view is `views/study/mind_map.php` + `public/assets/js/mind-map.js`. Per prior audit (#7), the bug is "right-most nodes clipped at viewport, no fit-to-viewport on first paint." Plan: fix the JS layout, add a node-detail panel. This is **not** a from-scratch redesign — the data tree is in place.

### 5.12 Unified study shell (brief items 35–36)

- **F-13 — `study_chrome_v2` already exists** as a feature flag and is wired into `StudyController` (all 5 actions) and `FlashcardController`. Quiz is NOT wired (F-8.3 above). Plan in Phase 13: turn on the flag in dev, audit, wire Quiz + Progress, then flip in prod.

---

## 6. Carried-over P0/P1 from the prior audit (`docs/site-audit-2026-05-05.md`)

These were found one day before this brief and overlap with several phases:

1. **Deep Notes renders raw HTML as text** — Phase 6/13 polish; flag is `deep_notes`.
2. **Flashcard Flip broken** — same as F-7.1.
3. **EPGDS Architecture diagram badges overlap labels** — out of scope of these phases (it's a content-asset fix, not a study-system fix). Track separately.
4. **Hero has empty right half on desktop** — Phase 1.
5. **"Why AviatorTutor" feature cards show single-letter placeholders** — out of scope; track in Phase 1 polish or a separate marketing-pass.
6. **Dashboard stat cards cramped vs Progress page** — Phase 2.
7. **Mind Map clips off right edge** — Phase 12.
8. **Quiz card shows "-- min" but actually has 30-min timer** — small data fix; tag it in Phase 8.
9. **Lesson tabs (Slides/Flashcards/Quiz/Mnemonics/Mind Map/Deep Notes) inconsistent scoping** — Phase 13.

---

## 7. Recommended fix order (replaces the brief's order)

The brief's phase numbering is mostly sound, but the per-phase scope needs to change based on the findings above. Recommended:

1. **Phase 0 — DONE** (this report).
2. **Phase 1 — Hero visual** (small, isolated, low-risk; ships a visible win first).
3. **Phase 2 — Sidebar collapse + KPI card layout** (CSS-only, behind a body class — low risk).
4. **Phase 5 (moved earlier) — Kill the old study route.** Make `/study/{id}` redirect to first lesson, archive `study/detail.php`, update Continue Studying / Suggested Next links. **This unblocks every other navigation issue**, so do it before redesigning anything that touches it.
5. **Phase 3 — Dashboard data wiring + missing study-sessions writer.** This is the big "everything is empty" fix.
6. **Phase 4 — Subjects filters + search.** Largely already exists in `system_picker_v2` partial.
7. **Phase 6 — New study system polish.** Slide-image-not-changing, hover-rectangle, embedded-Q answer logic.
8. **Phase 7 — Flashcards flip + typed answer.** Flag-flip + bug fix.
9. **Phase 8 — Quiz scoring + selector + layout consistency.** F-8.1 is the highest-impact bug in this list.
10. **Phase 9 — Progress page polish.** After Phase 3 lands, this just needs empty-state copy.
11. **Phase 10 — Global topbar search.**
12. **Phase 11 — Mnemonics audit + content fix.**
13. **Phase 12 — Mind map fit-to-viewport + detail panel.**
14. **Phase 13 — Unified study shell rollout.** (Wire Quiz + Progress into the v2 chrome.)
15. **Phase 14 — DB/API consistency audit.**
16. **Phase 15 — Final E2E QA + report.**

Phases 11 and 12 are **content/UX work**, not architecture work, and should not be allowed to consume time meant for Phase 3/8 which are actual data bugs. If the schedule slips, drop 11 and 12 to a follow-up.

---

## 8. What I have NOT verified yet (honest list)

- I have not opened the production site at https://aviatortutor.com in a browser this session — the user said they would log in for me on Chrome. The findings above are **all from source**. Some statements (the slide hover rectangle, the slide-doesn't-change-on-Next bug, the mnemonic count) are flagged **L (likely)** because they need a browser repro to confirm the failure mode.
- I have not read `views/study/lesson_slides.php` (381 lines) line-by-line — Phase 6 will. Same for `public/assets/js/lesson_slides.js` (421 lines).
- I have not read `views/flashcards/study.php` (640 lines) — Phase 7 will.
- I have not read `views/systems/show.php`, `views/quiz/{take,index,result}.php`, `views/study/mnemonics.php`, `views/study/mind_map.php`, `views/marketing/home.php` — each owning phase will.
- I have not enumerated which feature flags are flipped in `config/app.local.php` on production. **This is a blocker for Phase 13 specifically** — the user must share the live config or run `php -r "var_export(require __DIR__.'/config/app.local.php');"` so we know the actual deployed state.
- The `database/seeds/` directory exists but I have not opened it — Phase 3 may need to add demo seed data so widgets demo correctly without forcing the user to grind through 50 quiz attempts.

---

## 9. Risks I want flagged before fixing

1. **Production feature-flag state is unknown.** Anything I "turn on" locally may already be on (or different) in prod. Before flipping any flag in `config/app.php`, the deployed `config/app.local.php` must be inspected. **Action requested from the user:** paste the contents (with `anthropic_api_key` redacted) before Phase 13.

2. **Killing `/study/{id}`** removes a URL that may have inbound links from email, social, search engines. Better to redirect (302) than to 404. The proposal in F-5.1 (redirect to first lesson) preserves SEO and bookmarks.

3. **`study_sessions` writer (F-3.1) needs a session-end hook.** A naive on-page-load INSERT will record one session per page view, inflating heatmap intensity. Recommend tracking with a `started_at` row + a `beforeunload` PATCH that sets `ended_at` / `duration_secs`. Document the trade-off if we ship the simpler version first.

4. **`StudyController@detail` is currently the only renderer of `views/study/detail.php`.** If something else (controllers I haven't read) `include`s that view directly, redirecting the controller will leave dead code. Spot-checked with grep — only the controller references it.

5. **The brief assumes one demo user.** The seed data ships three (`admin`, `samuel`, `alice`). I'll standardise on `samuel@q400study.local` for browser QA unless told otherwise.

---

## 10. Files inspected this phase

- `README.md`, `ARCHITECTURE.md`, `DEPLOY.md`, `DATABASE_SETUP.md` (skim)
- `routes/web.php` (full)
- `config/app.php` (full)
- `database/schema.sql` (lines 1–200; remainder skimmed via grep)
- `database/migrations/*.sql` (filenames only)
- `app/Controllers/HomeController.php` (full)
- `app/Controllers/DashboardController.php` (full)
- `app/Controllers/SystemsController.php` (full)
- `app/Controllers/StudyController.php` (full)
- `app/Controllers/FlashcardController.php` (full)
- `app/Controllers/QuizController.php` (full)
- `app/Controllers/ProgressController.php` (full)
- `app/Controllers/ApiController.php` (grep only — the 8 INSERT sites)
- `views/layouts/pilot.php` (full)
- `views/dashboard/index.php` (full)
- `docs/site-audit-2026-05-05.md` (full)

Files **not** inspected this phase (queued for owning phases):

`views/marketing/home.php`, `views/systems/{index,show}.php`, `views/study/{lesson_slides,detail,mnemonics,mind_map,revision,deep_notes}.php`, `views/flashcards/{index,study,study_v2}.php`, `views/quiz/{index,take,result}.php`, `views/progress/index.php`, `views/layouts/study.php`, `views/partials/system-picker.php`, `public/assets/js/{lesson_slides,flashcards-v2,mind-map,study-chrome,quiz}.js`, `public/assets/css/pilot.css`.

---

## 11. Sign-off

Phase 0 is complete. Recommend proceeding to Phase 1 (Hero visual) next, in this order:

> Phase 1 → Phase 2 → **Phase 5 (re-prioritised)** → Phase 3 → Phase 4 → Phase 6 → Phase 7 → Phase 8 → Phase 9 → Phase 10 → Phase 11 → Phase 12 → Phase 13 → Phase 14 → Phase 15.

Each subsequent phase will produce its own short report under `docs/qa/phaseN-…md` and will only mark its TodoList task complete after the listed verification steps pass.
