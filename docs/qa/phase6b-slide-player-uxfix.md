# Phase 6 follow-up — slide player UX fixes

**Date:** 2026-05-06 (evening)
**Targets:** Bugs 16, 18, 20, 21 from the v2.0 brief — all flagged "deferred" in `docs/qa/phase6-study-system.md`.

## What shipped

### Bug 16 — slide area too small ("not TV-like")

`public/assets/css/study-chrome.css` `.study-content` container — added two breakpoints:

- `min-width: 1280px` → `max-width: 1120px` + 32 px horizontal padding.
- `min-width: 1600px` → `max-width: 1280px` + 40 px horizontal padding.

The default 920 px stays for laptops <1280 px so the line length doesn't blow out on shorter screens. Slide images and diagrams inherit this width because the `.slide-card` is a flow child of `.study-content`.

### Bug 18 — "Slide content doesn't change on Next"

This was a UX visibility issue, not a navigation bug. When a slide has an unanswered question gate, `goNext()` blocks advance and flashes the gate for 2.2 s — learners on long slides missed the flash and reported "Next does nothing." Two fixes:

1. **Persistent banner** above the gate in `views/study/lesson_slides.php`: a small sky-blue alert reading "Answer the check question below to continue." with a down-arrow icon. Stays visible the whole time the gate is unanswered. CSS hides it once the card has class `gate-passed` (added by JS post-answer) or `gate-already-passed` (added server-side when the slide was already passed in a prior session).
2. **Auto-scroll the gate into view** in `lesson_slides.js` `showSlide()` — a 450 ms timeout after activating a card with `data-has-gate="1"` that scrolls `.slide-gate` to the center of the viewport, so the gate is impossible to miss even on long-content slides.

### Bug 20 — "Continue" button does nothing after attempts exhausted

In `lesson_slides.js` `handleGateSubmit`, when `canProceed && !isCorrect` (out of attempts but allowed to proceed), the submit button previously had its text swapped to "Continue" but `disabled = true` was kept — so the button looked active but did nothing. Fix:

- Re-enable the submit button.
- Replace the click handler with one bound to `goNext()` (using `cloneNode(true)` to discard the previous `handleGateSubmit` listener cleanly).
- Add a `slide-gate-submit--continue` class for a distinct teal-gradient "active" appearance that visually separates "Continue →" from the disabled "Locked in" state on a correct answer.

### Bug 21 — "Question answer checking broken"

Verified by code inspection only — no fix needed. Round-trip:

- Frontend `postSlideAnswer(slideId, selectedIndex)` → POST `/api/lessons/{id}/slide-answer` with `slide_id`, `selected_index`.
- `ApiController::slideAnswer` (lines 350–442) reads `selected_index`, upserts `user_slide_progress` with `answered_correct = (selected_index === correct_index ? 1 : 0)`, returns `is_correct / attempts / can_proceed / explanation` — exactly the field shape `lesson_slides.js` expects.

The earlier session's "couldn't reproduce statically" stands; the code path is sound. If a user reports it again, look at the `lesson_slides.question` JSON for that specific slide for a malformed `correct_index`.

### Bug 19 — "Slides show wrong Q&A content"

Not a code bug — the slide template (`lesson_slides.php`) renders all slide types identically apart from the type pill (label + icon). The "Q&A" surface is always the gate at the bottom, which renders only when `lesson_slides.question` JSON is non-empty. If a learner sees a question on what should be a pure concept slide, the row was authored with a question payload that shouldn't be there. **Action item for the admin pass:** audit `lesson_slides` rows for `slide_type = 'concept' AND question IS NOT NULL` and delete the spurious `question` value.

## Files changed

```
public/assets/css/study-chrome.css       +6 lines  (1280/1600 breakpoints for .study-content)
public/assets/css/app.css                +20 lines (.slide-gate-banner + Continue→ button)
views/study/lesson_slides.php            +4 lines  (banner markup above gate)
public/assets/js/lesson_slides.js        ~30 lines (auto-scroll gate, Continue→ handler)
```

No DB changes. No new endpoints. No flag changes.

## QA checklist (browser, before deploy)

- [ ] Open `/study/1/lesson/9` on a ≥1280 px viewport — slide content visibly wider than the prior 920 px (~1120 px).
- [ ] On a slide with a question gate: banner reads "Answer the check question below to continue." and stays visible until answered. Gate scrolls into view automatically ~450 ms after the slide loads.
- [ ] Pick the correct option → submit shows "Locked in" (greyed). Banner hides. Click global Next → advances.
- [ ] Pick wrong twice on a slide → submit cycles through "Try Again". On the 3rd wrong, submit becomes a glowing "Continue →" button — clicking it advances the deck (was the bug).
- [ ] On a previously-correct-answered slide, banner is hidden from the start (via `gate-already-passed` class).

## Risks / not-fixed

- **Bug 19** intentionally not fixed in code — see above; needs admin-side content audit.
- The `setTimeout(450)` for auto-scrolling the gate could feel sluggish on short slides; if Captain Samic prefers instant, drop it to 150–200 ms.
- The banner uses `:has()` selector for the post-answer hide path on top of the JS class fallback; modern Safari/Chrome/Firefox all support `:has()` now (Chrome 105+, Safari 15.4+, Firefox 121+) but if a learner reports seeing the banner after answering on an old browser, confirm the JS-applied `gate-passed` class is the actual hide path that's working.
