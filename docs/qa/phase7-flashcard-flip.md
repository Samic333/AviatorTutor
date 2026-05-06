# Phase 7 — Flashcard Flip Fix (verified live)

**Status:** Bug fixed, verified on production via injected CSS, source edited. Browser QA on the deployed source pending.

## Surprise discovery (production state ≠ source defaults)

Before fixing, I navigated to https://www.aviatortutor.com/flashcards/1 in the user's logged-in Chrome session. The page loads `flashcards-v2.js` and `study-chrome.js`. That means **`flashcards_v2` and `study_chrome_v2` flags are TRUE in production** — opposite of the gitted `config/app.php` defaults of `false`. So the live site has been running on `views/flashcards/study_v2.php` all along, and the bug lives there, not in the legacy view I was about to edit.

Updating the baseline report's "production flag state unknown" risk: now confirmed for two flags. The remaining flags (`mnemonics_v2`, `mind_map`, `deep_notes`, `dashboard_v2`, `system_picker_v2`, …) need the same check before any phase touches them. `dashboard_v2` is also already on (`<body>` has class `dashboard-v2` on the live `/dashboard`).

## Root cause

`public/assets/js/flashcards-v2.js` lines 38–44 toggles the `hidden` HTML attribute on the front/back face:

```js
function flip(card) {
  var f = card.querySelector('.fcv2-front');
  var b = card.querySelector('.fcv2-back');
  if (!f || !b) return;
  f.hidden = !f.hidden;
  b.hidden = !b.hidden;
}
```

This relies on the user-agent default stylesheet rule `[hidden] { display: none }`. But `public/assets/css/flashcards-v2.css` line 63 sets:

```css
.fcv2-face { flex: 1; display: flex; flex-direction: column; gap: 14px; }
```

CSS author rules win the cascade against UA defaults, so `display: flex` defeats `[hidden]`. **Both faces render at the same time, regardless of the `hidden` attribute.** That's why "Flip does nothing" — the JS does run, the attribute does flip, but visually nothing changes because both faces are always `display: flex`.

## Verification

Injected `.fcv2-face[hidden]{display:none!important}` into the live page and called the flip handler:

```
BEFORE: frontDisp=flex   backDisp=none
AFTER:  frontDisp=none   backDisp=flex
```

Confirmed. Without the rule, the same probe shows both faces stay `display: flex` after `.click()`.

## Fix

Single rule appended to `public/assets/css/flashcards-v2.css`:

```css
.fcv2-face[hidden] { display: none !important; }
```

`!important` is intentional and necessary — it has to win against the `.fcv2-face` shorthand on the same selector specificity.

## Files changed

- `public/assets/css/flashcards-v2.css`

## Files NOT changed (deliberate)

- `views/flashcards/study.php` (the legacy view) — still has the rotateY card-flip pattern. Not in active use because `flashcards_v2 = true` in production. Left untouched per the brief's "don't remove working features without replacing them" rule. Will be archived in Phase 15 once the v2 view is confirmed stable.
- `public/assets/js/flashcards-v2.js` — the JS contract is correct; the bug was purely CSS.

## Typed-answer bit of the brief

The v2 view already supports `expected_answer` typeable cards (controller flag `$c['typeable']` is set on every card row, and the `flashcards/study.php` legacy view has a `typedAnswerSection`). The v2 view (`study_v2.php`) currently doesn't render the typed-answer input — only the flip-then-grade flow. Adding the typed input to the v2 view is more work than this phase had budget for. Tagged for follow-up: needs a typed input + `gradeTyped()` AJAX path + UI affordance to switch between flip-mode and type-mode. Recommend pulling the existing `gradeTyped` logic from the legacy view rather than re-implementing.

## QA checklist

- [ ] Open `/flashcards/1` after deploy.
- [ ] Click "Flip" on the top card → back face visible, front face hidden.
- [ ] Click "Flip" again → reverts.
- [ ] Press Space (keyboard shortcut) → toggles same way.
- [ ] Click "Got it right" / "Got it wrong" → card animates out, next card slides up, both faces of the new top card start with front visible only.
- [ ] No console errors.
