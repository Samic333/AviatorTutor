# Phase 4 — Subjects page filters + search

**Date:** 2026-05-06
**Flag:** `system_picker_v2` (already true on prod, mirrored in `config/app.local.php`)

## What shipped

The `system_picker_v2` partial was already on prod with: search input + jump-to-system dropdown + category-grouped tiles. Phase 4 adds the two missing controls per the v2.0 brief items 9–11:

- **Aircraft-type dropdown** — populated from `aircrafts` table (`status IN ('live','beta','coming_soon')`), live entries first, others tagged `data-coming-soon`. Selecting a non-live aircraft hides every tile and shows a "coming soon — join the waitlist" banner linking to `/aircraft`.
- **Category dropdown** — explicit filter mirroring the existing group sections. "All categories" leaves grouping intact; selecting one collapses the others.
- **Search** — already worked; now coexists with the new dropdowns (filters compose: aircraft AND category AND search-text).

All filtering is client-side, no page reload. Debounced 60ms on the search input; immediate on dropdown change.

## Files changed

```
app/Controllers/SystemsController.php   +12 lines  (fetch aircrafts list when picker_v2 on)
views/partials/system-picker.php        rewrite    (added aircraft/category controls + JS)
```

## Verification checklist (run locally with XAMPP, or on prod after deploy)

- [ ] Visit `/systems` while logged in — picker renders with **four** controls in the top row: search, aircraft, category, jump-to-system.
- [ ] Type "fuel" in search → only the Fuel tile remains visible; other groups collapse to empty.
- [ ] Select category "Powerplant" → only the Powerplant group is visible.
- [ ] Combine search "elec" + category "Electrical" → Electrical group, Electrical tile shown.
- [ ] Select an aircraft other than Q400 (e.g. B737) → all tiles hidden, yellow "coming soon" banner appears with link to `/aircraft`.
- [ ] Switch back to "All aircraft" → tiles return.
- [ ] Click an enrolled subject tile → lands on `/study/{id}/lesson/{firstLessonId}` via the Phase 5 redirect (not the old long page).

## Notes / trade-offs

- The `systems` table doesn't have an `aircraft_id` column yet (every system is implicitly Q400). The aircraft filter therefore acts as a forward-compatible UI — Q400 always shows everything; non-Q400 always shows nothing. Once `systems.aircraft_id` is added (a future migration), the JS can be amended to filter on a `data-aircraft` attribute on each tile.
- The fallback option (`<option value="q400">Q400</option>`) renders if the `aircrafts` query fails or returns nothing, so the dropdown never shows up empty.
- Mobile layout collapses to one column at <600px, two columns at <900px.
