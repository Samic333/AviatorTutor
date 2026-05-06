# AviatorTutor — Site Audit (live deployment)

**Date:** 2026-05-05
**URL audited:** https://aviatortutor.com/
**Scope:** Marketing site + signed-in study app (Q400 / Electrical Power)
**Account used for app testing:** Captain Samic (PRO + ADMIN)
**Browser:** Chrome, desktop 1568×900 + mobile 390×800

---

## Executive summary

The site is structurally healthy: all pages return 200, no JS errors on initial loads, fonts and CSS deliver fast, and the core study flows (Slides → Flashcards → Quiz → Mnemonics → Mind Map → Deep Notes) all reach a working screen.

That said, there are **three P0 bugs** that are blocking on the study experience itself and **several visual issues** that make the product feel less polished than the marketing copy promises. None require infrastructure work — most are CSS/template fixes.

---

## P0 — Critical (study-experience-breaking)

### 1. Deep Notes renders raw HTML as text
**Where:** `https://aviatortutor.com/study/1/deep-notes` (and presumably every system's Deep Notes view).
**What:** Literal `<p>...</p>` tags appear in the rendered output. The body content has HTML that is being escaped/printed instead of rendered.
**Impact:** Looks broken to a paying user. This is the most visually damaging bug on the site.
**Likely fix:** The template is using `e()` / `htmlspecialchars()` / `{{ }}` (escaped) instead of `{!! !!}` / `v-html` / `dangerouslySetInnerHTML` — switch to the unescaped output for trusted DB content. Make sure the source content is sanitized server-side.

### 2. Flashcard "Flip" button does nothing
**Where:** `https://aviatortutor.com/flashcards/1` (and presumably all flashcard sets).
**What:** Both the question and the answer are visible at the same time. Clicking "Flip" produces no visible change. Card advancement on Got it right / Got it wrong DOES work (counter updates, next card loads).
**Impact:** Defeats the spaced-repetition method the marketing site is built around. Users can't self-test.
**Likely fix:** The Flip button's click handler is missing or the `.flipped` CSS class isn't being toggled / isn't hiding the back face. Inspect the click binding and the front/back face CSS (probably needs `display: none` on the back until flipped).

### 3. Q400 EPGDS Architecture diagram — numbered hotspots overlap labels
**Where:** Slide 1 and 2 (and likely more) of `https://aviatortutor.com/study/1/lesson/9` Electrical Power.
**What:** The numbered legend badges (1–14) are positioned dead-center over each box's title text. As a result, almost every label is partially obscured: "AC GEN 1", "TRU 1", "EPCU", "DC BUS 1", "ESS BUS", "ESS STANDBY", "MAIN BATT", "STBY BATT", "APU STARTER/GEN", "EXTERNAL POWER" all have characters covered by the colored circles.
**Impact:** This is the flagship lesson on the only LIVE aircraft library. It's the slide a prospective customer is most likely to land on.
**Likely fix:** Position the badges in the top-right corner of each box (e.g. `position: absolute; top: -8px; right: -8px;`) instead of overlapping the title. Or shrink them and put them inline before the title with proper spacing.

---

## P1 — Visual / UX issues

### 4. Hero on desktop has a giant empty right half
**Where:** Homepage `https://aviatortutor.com/` at ≥1280px viewport.
**What:** The headline + intro paragraph + CTAs sit in the left ~50% of the screen. The right ~50% is dead space — clearly meant for a screenshot, illustration, or aircraft image, but currently nothing is there.
**Impact:** First impression. Mobile is fine because content fills the column. Desktop looks unfinished.
**Suggestion:** Drop in either (a) a single Q400 cockpit/dashboard photo, (b) a stacked screenshot composite of dashboard + flashcard + quiz, or (c) an animated/looping screenshot of the slide flow. Even a tasteful gradient-blob illustration would be better than empty.

### 5. "Why AviatorTutor" feature cards show single-letter placeholders instead of icons
**Where:** Homepage feature grid (Aircraft-specific modules / Airline interview prep / Aviation subject packs / Spaced-repetition flashcards / Self-paced learning / Mobile-friendly).
**What:** Each card has a small badge with just the first letter of the feature name (A, I, S, F, P, M). Looks like icon placeholder fallback.
**Impact:** Reads as unfinished.
**Suggestion:** Replace with lucide / heroicons SVGs (plane, briefcase, book, layers, calendar, smartphone). The site already uses iconography correctly elsewhere (settings gear, home, etc.).

### 6. Dashboard stat cards layout is cramped & inconsistent with Progress page
**Where:** `https://aviatortutor.com/dashboard` (top stats row).
**What:** All four cards (Systems Studied / Study Streak / Flashcards Due / Avg Quiz Score) cram label + icon + number + secondary label all on a single line. The numbers are tiny relative to the labels. On mobile (390px) it gets worse — the icon sits next to the number rather than the label.
**Impact:** Looks unprofessional; numbers should be the visual hero.
**Suggestion:** The Progress page (`/progress`) already does this correctly — large number, label on top. Match the Dashboard cards to that pattern.

### 7. Mind Map clips off the right edge of the viewport
**Where:** `https://aviatortutor.com/study/1/mind-map`.
**What:** The right-most node column ("Overview — EPGDS, Brain, and Bus To...", "Components — Generators, TRUs, Ba...", ...) is truncated at the viewport edge. Header says "Drag to pan / Wheel to zoom" — but on first load the nodes are clipped, no horizontal scroll, and nothing tells the user to drag.
**Impact:** Looks broken on first paint.
**Suggestion:** On render, fit-to-viewport (zoom-out) so all nodes are visible by default. Or center the root and lay out children radially instead of linearly to the right.

### 8. Quiz card shows "-- min" when there is actually a 30-min timer
**Where:** `/quiz` listing → "FMS — Practice" card says **-- min** ("No time limit") but starting the quiz reveals a 29:58 countdown.
**Impact:** Sets wrong expectation; could surprise a user mid-quiz.
**Suggestion:** Either (a) honour the data on the card and remove the timer, or (b) display the actual time limit on the card. Check the quiz data source.

### 9. Lesson tabs (Slides / Flashcards / Quiz / Mnemonics / Mind Map / Deep Notes) are inconsistent
**Where:** Lesson page header.
**What:** Some links are subject-scoped (`/study/1/mnemonics`, `/study/1/mind-map`, `/study/1/deep-notes`), Flashcards is its own subject route (`/flashcards/1`), and Quiz goes to a completely different un-scoped route (`/quiz` — the global quiz listing, not Electrical-Power-specific). Clicking a tab from inside a lesson sometimes appears to highlight without navigating until you click again.
**Impact:** Confusing IA. From the Electrical Power lesson, "Quiz" should take you to Electrical Power quizzes — not the global list.
**Suggestion:** Normalise to subject + system scope for all six tabs (e.g. `/study/1/lesson/9/quiz`, `/study/1/lesson/9/flashcards`). Make the highlighted-but-not-navigated state actually navigate.

---

## P2 — Polish

### 10. Hero copy repeats itself
The H1 says "**one premium aviation learning platform**" and the paragraph immediately below opens with "AviatorTutor is a **premium aviation learning platform** built by working pilots…". Tighten the paragraph.

### 11. FAQ — mixed typography
Some apostrophes are curly (`you're`) and some are straight (`you're`) within the same answers. Easy find-and-replace pass.

### 12. "Best score: --%" / "Attempts: 0"
The empty state for un-attempted quizzes shows a literal `--%`. Cleaner to hide the stat row when there are 0 attempts, or show "Not yet attempted".

### 13. Pricing tiers — three of four say "Coming soon"
Intentional pre-launch, but the "Join the waitlist" CTAs should ideally trigger an email-capture modal so you can warm a launch list.

### 14. Sign-in form
Page has very little above-the-fold content other than the form — the AviatorTutor logo+name are repeated inside the card, while the page header ALSO shows them. Consider removing the in-card branding or adding a value-prop line ("Sign in to continue your Q400 study", with a small streak/feature ribbon).

---

## Healthy / nothing to do

- All static assets (CSS, fonts) load 200, no broken images, no 404s on initial paint.
- No JS errors detected in the console for the pages tested (homepage, login, dashboard, my-subjects, flashcards, quiz listing, quiz interior, mnemonics, mind map, deep notes, progress, search).
- Mobile homepage reflows correctly — hamburger nav, full-width CTAs.
- Mnemonics page (GRAB acronym) is the cleanest screen in the app — use it as the visual reference for the rest.
- Search page is cleanly built (search box + Quick Links grid).
- Progress page stat cards are the correct pattern — copy that pattern to Dashboard.
- Quiz interior (radio MCQs, "Question 1 of 25", timer) works as expected.

---

## Suggested order of work

1. **Deep Notes HTML escape fix** (#1) — fastest to ship, biggest perception win, ~15 min.
2. **Flashcard Flip toggle** (#2) — core study mechanic, must work before any paid tier launches.
3. **EPGDS diagram badge positioning** (#3) — flagship lesson polish.
4. **Dashboard stat cards → match Progress layout** (#6) — easy CSS, high visibility.
5. **Hero illustration / screenshot for desktop** (#4) — bigger lift, but biggest first-impression win.
6. **Replace placeholder feature icons** (#5) — drop in a lucide icon set.
7. **Mind Map fit-to-viewport** (#7).
8. **Quiz time-limit data consistency** (#8) — small but trust-affecting.
9. **Lesson-tab IA normalization** (#9).
10. P2 polish pass (#10–14).

---

*Generated from a live walk-through of the production site. No source code was inspected for this audit — every issue above is reproducible by clicking through the URLs listed.*
