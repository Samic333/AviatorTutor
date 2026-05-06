# Phase 0 Addendum — Re-baseline before second pass

**Date:** 2026-05-06 (second pass, evening)
**Trigger:** Captain Samic re-issued the v2.0 study-platform-overhaul brief verbatim. The earlier passes (morning + afternoon) shipped all 15 phases in source but explicitly deferred **20 of the 36 specific bugs** listed in the brief. This pass focuses on those deferrals.

---

## What was already shipped (2026-05-06 sessions 1 + 2)

Per `docs/FINAL_STUDY_PLATFORM_QA_REPORT.md` and the `docs/qa/phase*.md` set:

- **Feature flags flipped on locally** (`config/app.local.php`): `nav_my_subjects`, `friendly_errors`, `study_chrome_v2`, `theme_drawer`, `flashcards_v2`, `mnemonics_v2`, `mind_map`, `deep_notes`, `dashboard_v2`, `system_picker_v2`, `analytics_v1`. Only `add_subject_flow` remains off.
- **Phase 1** — Hero visual stack on `/`.
- **Phase 2** — Sidebar collapse button, ⌘\ shortcut, KPI minmax widening.
- **Phase 3** — `study_sessions` writer (`Controller::recordStudySession`) wired into `StudyController`, `FlashcardController`, `QuizController`. POST `/api/study-sessions/end` added with `sendBeacon` heartbeat.
- **Phase 4** — Aircraft + category dropdowns + keyword filter on system picker.
- **Phase 5** — `StudyController::detail` redirects to first lesson; old `/systems/{id}` long page is orphaned but not deleted.
- **Phase 6** — `study-edge-zone:hover { opacity: 1 }` rule removed (the "large rectangle" bug).
- **Phase 7** — Flashcard flip CSS fix (`.fcv2-face[hidden]{display:none!important}`); Phase 7b ported typed-answer + AI/offline grading from legacy view to v2.
- **Phase 8** — Quiz scoring rewritten with normaliser (sorted lower-cased trimmed string array comparison).
- **Phase 9** — Progress page blank-slate hero; session-end heartbeat.
- **Phase 10** — Topbar global search with debounced fetch, ⌘K shortcut, grouped dropdown, mnemonics indexed.
- **Phase 11** — Mnemonics page filters + system jumper + stable anchors.
- **Phase 12** — Mind map full rewrite: split layout, panel, fit-to-viewport, cursor-centered zoom, separated chevron.
- **Phase 13** — Quiz `take` + `result` switched to study layout.
- **Phase 14** — Hardcoded `$totalSystems = 22` replaced with live count; `is_admin` audit.
- **Phase 15** — Sidebar Search link removed (topbar replaces it).

---

## What remains — the deferred bug list

From `docs/FINAL_STUDY_PLATFORM_QA_REPORT.md` lines 138–166 and `docs/qa/phase6-study-system.md`:

| Bug # | Description | Why deferred | This pass plan |
|---|---|---|---|
| 16 | Slide area not "TV-like" enough | Design call (pick a max-width) | Bump `study-chrome.css` `.slide-card` max-width to 1120 px on desktop ≥1280 px; widen the lesson body padding. |
| 18 | Slide content doesn't change on Next | Intentional gate-blocking; ephemeral 2.2 s hint missed | Add a **persistent** "Answer the question to continue" banner above the gate when the slide has an unanswered gate; auto-scroll the gate into view on slide enter. |
| 19 | Slides show wrong Q&A content | Content data question | Inspect `lesson_slides` rows for slide_type mismatches; if found in seed data, fix; if author-side, document and skip. |
| 20 | Answering jumps backward / "Continue" disabled | UX inconsistency: button reads "Continue" but stays `disabled` after attempts exhausted | Re-enable submit button on attempts exhausted, change handler to call `goNext()`. |
| 21 | Question answer checking broken | "Couldn't reproduce statically" | Trace `ApiController::slideAnswer` → `user_slide_progress` end-to-end; verify the response matches the JS gate-state expectations. |
| 23 | Flashcards typed answer | Phase 7b shipped — verify in source | Read `views/flashcards/study_v2.php` + `flashcards-v2.js` to confirm. |
| 24 | Flashcards accept close answers | Phase 7b shipped via `AIContentService::gradeAnswer` offline grader | Verify by reading the grader. |
| 27 | Quiz selector dropdowns | Deferred to a follow-up that reused Phase-4 partial | Reuse `views/partials/system-picker.php` inside `views/quiz/index.php`. |
| 28 | Quiz inconsistent layout | Phase 13 shipped — verify | Read `QuizController::take/result` to confirm `layout='study'`. |
| 29 | Quiz inside unified design | Same | Same. |
| 30 | Mnemonics one-per-PDF | Content gap, not code gap | Verify the `mnemonics` table has multi-entry-per-system rows; if not, this is a content-authoring task — out of scope for code. |
| 31 | Mnemonics per concept | Same | Same; but add the optional `lesson_id` filter to `StudyController::mnemonics` if not already there. |
| 32 | Mind map redesign | Phase 12 shipped — verify | Read `mind_map.php` + `mind-map.js`. |
| 33 | Mind map node detail panel | Same | Same. |
| 36 | Consistent dashboard/study design | Phase 13 spirit; needs cross-mode QA | Walk the cross-mode chrome briefly. |

The other "deferred" entries in the prior report are content-authoring tasks (admin-side curriculum work), out of scope here.

---

## This pass — execution order

1. **Slide player UX** (bugs 16, 18, 20, 21) — biggest concrete win, all code changes in `lesson_slides.{js,php}` + `study-chrome.css` + `ApiController::slideAnswer` review.
2. **Verify typed-answer flashcards** (23, 24) — read + spot-check the source.
3. **Quiz selector** (27) — reuse system-picker partial.
4. **Verify quiz layout / scoring** (25, 26, 28, 29) — read source paths.
5. **Mnemonics** (31) — add lesson scoping if missing.
6. **Verify mind map** (32, 33) — read source.
7. **Final report** — append to `FINAL_STUDY_PLATFORM_QA_REPORT.md` with what changed in this pass, what's still content-authoring work.

No deploy in this pass. All changes land locally with the existing flag state. Per `feedback_aviatortutor_deploy.md`, Captain Samic will deploy when the consolidated bundle is ready.

---

## Methodological note

I cannot drive a browser in this session, so "verification" means careful code path inspection plus reasoning about user-facing outcomes — not screenshots. Any claim of "fixed" in the per-phase report below is a code-level claim. End-to-end browser QA must happen before deploy.
