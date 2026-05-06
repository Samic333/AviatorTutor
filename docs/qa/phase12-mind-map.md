# Phase 12 — Mind map fit-to-viewport + node detail panel

**Date:** 2026-05-06
**Flag:** `mind_map` (already true on prod)

## What shipped

The data-tree builder in `StudyController::mindMap` was already a clean 4-level structure (system → lesson → sections + facts buckets → leaves). The bugs were all in the renderer. Rewrote both pieces:

1. **`StudyController::mindMap`** — leaf nodes now carry a `detail` field with the un-truncated text (lesson sections also include the section body, stripped of HTML, capped at 600 chars). The label stays short for the tree; the panel shows the long version.
2. **`views/study/mind_map.php`** — split into a `mm-stage` grid: SVG canvas on the left, a 360px detail panel on the right. Mobile (<900px) collapses to stacked layout with the panel turning into a slide-up bottom sheet. Three toolbar buttons: `+` zoom in, `−` zoom out, `⤢` fit-to-viewport.
3. **`public/assets/js/mind-map.js`** — full rewrite:
   - **Fit on first paint** — `requestAnimationFrame(fitToViewport)` after the SVG is in the DOM picks up real dimensions and scales the content to fit with 24px padding.
   - **Every node clickable** — clicking populates the detail panel (kind chip, title, long detail text, "Open this →" link if href, plus contextual cross-links to flashcards / quiz / mnemonics for the parent system).
   - **Collapse separated from select** — the chevron `+` / `−` at the right edge of any parent node toggles its children; the rest of the node body opens the panel. Previously parents could only collapse and could never open a panel.
   - **Pan-zoom that doesn't fight the click** — the pan handler ignores pointer-downs that started inside a `.mm-node`, so click-on-node always opens the panel even on overlapping nodes.
   - **Wheel zoom centers on cursor** — zooms at the pointer position rather than the origin, so deep-tree exploration doesn't slide nodes off-screen.
4. **Re-fit on resize** — debounced 120ms; the tree never gets stranded when the user toggles the sidebar collapse or rotates a phone.

## Files changed

```
app/Controllers/StudyController.php   +6 lines (detail field on leaf nodes)
views/study/mind_map.php              rewrite (split layout, panel markup, toolbar)
public/assets/js/mind-map.js          rewrite (~280 LOC: fit, panel, separated chevron, cursor-zoom)
```

## Verification checklist

- [ ] `/study/1/mind-map` — entire tree visible on first paint, no nodes clipped at the right edge of the canvas.
- [ ] Toolbar `+` / `−` zooms; `⤢` re-fits.
- [ ] Wheel zoom centers on the cursor.
- [ ] Drag pans the tree; the cursor turns into a `grabbing` hand while dragging.
- [ ] Click any leaf → right panel shows the kind chip ("Concept"), the full label, the un-truncated detail text, and the cross-links (Flashcards / Quiz / Mnemonics).
- [ ] Click a lesson node → panel shows "Open this →" linking to `/study/{sys}/lesson/{id}`.
- [ ] Click the `−` chevron on a parent → its children collapse; chevron flips to `+`.
- [ ] Resize the window → tree re-fits to the new canvas dimensions.
- [ ] Mobile (<900px): canvas is full width, panel is hidden until tapping a node, then slides up from the bottom; close button on top-right of the sheet.

## Notes

- The `detail` field is capped at 600 chars for sections to keep the JSON payload sane on systems with long lesson bodies. Adjust if learners ask for more.
- The cross-links use `/quiz?system=N` rather than `/quiz/N` because quiz IDs differ from system IDs. The quiz index page should fall back gracefully — if `system` query param doesn't match a quiz, learners just land on the quiz catalog. (Phase 13 may polish this.)
