# Phase 2 — Sidebar Collapse + KPI Card Layout

**Status:** Implemented. Browser QA pending.

## Files changed

- `views/layouts/pilot.php` — added a collapse button (chevron icon) inside the brand row. Extended the inline script to handle toggle + localStorage persistence + ⌘\\ keyboard shortcut. Adds `data-label` to each nav link so the collapsed-mode tooltip CSS can render it.
- `public/assets/css/pilot.css`
  - `.plt-stats-grid`: bumped `minmax` from `160px` to `200px` so the 70px progress ring + 28px value + label never collide. (Root cause of the audit's "cards overlap" complaint.)
  - Added `.plt-sidebar__collapse` button styles + the entire `body.plt-sidebar-collapsed` cascade: 260px → 72px width, hides labels, centers icons, shows native-style tooltips on hover via `::after` reading `data-label`.
  - Mobile (≤768px) explicitly opts back out of the collapsed state so the existing off-canvas hamburger pattern is unchanged.

## How it works

Click the chevron in the sidebar header → body gets `.plt-sidebar-collapsed` → CSS variable `--plt-sidebar-w` flips from 260px to 72px → both the sidebar and `.plt-main { margin-left }` animate to the new width via the existing `transition: width var(--plt-transition)`.

Tooltips on hover are pure CSS (`::after { content: attr(data-label) }`) — no JS-driven popovers, no positioning library.

⌘\\ / Ctrl+\\ toggles too (familiar shortcut from VS Code / Slack / Linear).

## QA checklist

- [ ] Click the chevron in the sidebar — sidebar shrinks to 72px, content reflows.
- [ ] Click again — sidebar returns to 260px.
- [ ] Refresh the page — collapsed state persists.
- [ ] Hover an icon while collapsed — label tooltip appears to the right.
- [ ] ⌘\\ toggles the same way.
- [ ] Resize to mobile — sidebar reverts to off-canvas drawer; chevron is hidden; hamburger still works.
- [ ] Active route highlight still shows in collapsed mode.
- [ ] Dashboard KPI cards no longer visually overlap at 1280–1440px content widths.

## Known limitations

- Brief flash on first load if the user previously collapsed and reloads — the `.plt-sidebar-collapsed` class is applied via JS after `DOMContentLoaded`, so for ~50ms the sidebar paints at 260px before snapping. Acceptable trade-off vs. an inline `<head>` script that would block the first paint. Can be revisited if it's noticeable.
- Settings cog in the topbar still shows on mobile — that's intentional (it opens the reading-preferences drawer). No change.
