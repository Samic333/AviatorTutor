# Phase 1 — Homepage Hero Visual

**Goal:** Add a premium right-side visual to the hero (per brief item P1#4 / Phase 1).
**Status:** Implemented. Browser QA pending (user testing on Chrome).

## Files changed

- `views/marketing/home.php` — wrapped existing hero content in `.hero__copy` (left column), added `.hero__visual` (right column) containing three rotating preview cards: a study-slide preview (Q400 EPGDS bus tie logic), a typed-answer flashcard, and a KPI sliver. All pure HTML/CSS — **no images, no JS, no external assets**.
- `public/assets/css/marketing.css` — added ~250 lines under a `Phase 1 — premium right-side preview stack` banner. Hidden below 980px (mobile keeps the existing single-column hero exactly).

## Why three cards instead of one screenshot

The brief's options were "(a) screenshot, (b) screenshot composite, (c) animated/looping screenshot, (d) gradient blob." Going with a hand-built mockup stack instead because:

1. **Real screenshots get stale.** Any future redesign of /dashboard or /flashcards immediately makes the hero look outdated.
2. **No image asset means no extra HTTP round-trip, no LCP regression.** The "must not slow down the homepage" rule from the brief is structurally enforced.
3. **The three cards collectively communicate the product** — slide-based learning, typed-answer flashcards (the very feature Phase 7 needs to ship), and progress KPIs — better than one screenshot would.
4. **Aviation styling is built-in** via existing CSS variables (`--accent`, `--font-mono`, `--font-head`).

## Animation contract

- 27s rotation cycle, 9s per card, CSS `@keyframes hv-rotate`.
- Pauses on hover.
- Respects `prefers-reduced-motion: reduce` (animation off, cursor blink off).
- No requestAnimationFrame, no setInterval, no JS at all.

## QA checklist (run after deploying)

- [ ] Desktop ≥1280px: text-and-CTA on left, three cards stacked & rotating on right.
- [ ] Tablet 768–979px: visual hidden, hero looks identical to before.
- [ ] Mobile ≤767px: visual hidden, hero looks identical to before.
- [ ] No console errors on `/`.
- [ ] LCP on `/` should not regress (new content is below-the-fold and CSS-only).
- [ ] CTAs `Start studying free` and `View plans` still navigate to `/register` and `/pricing`.
- [ ] hero metabar still displays.

## Known limitations

- The cards intentionally don't animate beyond opacity/translate — no morphing content, no SVG path tweens. Felt premium-but-restrained was right for an aviation-pro audience. Open to revisiting if the user wants more motion.
- Only desktop ≥980px sees the visual. The brief says "must not crowd hero text" — at narrower desktop widths the visual would force the H1 below 22ch and look tight, so we hide cleanly instead of compressing.

## Next phase

Phase 2 — Dashboard sidebar collapse + KPI cards.
