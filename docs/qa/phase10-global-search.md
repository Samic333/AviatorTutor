# Phase 10 — Global topbar search

**Date:** 2026-05-06

## What shipped

1. **Search input in the topbar** of `views/layouts/pilot.php`, right of the page title and left of the settings cog. Carries the keyboard hint `⌘K` and is always-visible (the topbar was previously mobile-only — desktop now shows it too because the search lives there).
2. **Debounced fetch** to `/api/search?q=…` (200ms quiet) renders a grouped dropdown of Systems / Lessons / Flashcards / Quizzes / Mnemonics. Match terms are `<mark>`-highlighted in titles + excerpts.
3. **Keyboard navigation** — `↑`/`↓` move the active row, `Enter` opens, `Esc` closes, `⌘K` / `Ctrl+K` focuses from anywhere.
4. **Lesson hits route to the new slide player** (`/study/{system}/lesson/{lesson}`), not the old `/systems/{id}#lesson-{id}` anchor that the API was returning. (The Phase 5 redirect would have fixed that anyway, but pointing direct is faster + bookmarkable.)
5. **Quizzes joined the `type=all` results** — were previously only returned for `type=quizzes` so the omnibox missed them.
6. **Mnemonics added** to the search index (links to `/study/{system}/mnemonics#m-{id}`).
7. **Sidebar `Search` link** kept for now per the brief — to be removed in Phase 15 if it's redundant.

## Files changed

```
app/Controllers/ApiController.php       fix lesson URL, include quizzes in 'all', add mnemonics
views/layouts/pilot.php                 +20 lines (topbar search markup, JS include)
public/assets/css/pilot.css             +90 lines (topbar always-visible, search styles)
public/assets/js/topbar-search.js       new (170 lines: debounced fetch, dropdown, kbd nav)
```

## Verification checklist

- [ ] Visit any signed-in page — the topbar shows a "Search systems, lessons, flashcards…" input on the right.
- [ ] Type "fuel" → dropdown opens within ~250ms. Sections labelled "Systems" / "Lessons" etc.
- [ ] Click a Lesson result → lands on `/study/{system_id}/lesson/{lesson_id}` (the new player), not the old long page.
- [ ] Click a Quiz result → lands on `/quiz/{id}` (Phase 13 will improve the chrome here).
- [ ] Click a Mnemonic result → lands on `/study/{system_id}/mnemonics`.
- [ ] `⌘K` / `Ctrl+K` from the dashboard focuses the search input.
- [ ] Arrow-down + Enter navigates the active row, even with the mouse parked on a different result.
- [ ] Esc closes the dropdown without navigating.
- [ ] On mobile (≤768px), the kbd hint is hidden but the search still works.

## Notes

- The legacy sidebar `Search` link is kept until Phase 15 cleanup so existing bookmarks / the `/search` page are untouched.
- Search hits Are limited to 5 per type by the API; that should be enough for an omnibox dropdown without a load-more pattern.
- No backend caching layer added — query is simple LIKE against indexed columns. If it ever gets slow, full-text indexes on `lessons.body` and `flashcards.front/back` are the obvious next move.
