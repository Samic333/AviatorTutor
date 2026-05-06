# Phase 11 — Mnemonics audit + filters

**Date:** 2026-05-06
**Flag:** `mnemonics_v2` (already true on prod)

## Audit finding (the user's complaint, root cause)

```
mysql> SELECT system_id, COUNT(*) FROM mnemonics WHERE is_published=1 GROUP BY system_id;
1	1
2	1
3	1
…
```

Every system has **exactly one** published mnemonic in the `mnemonics` table. The user's "only one mnemonic for the whole PDF" complaint is therefore a **content seeding** issue, not a render bug. The view `views/study/mnemonics.php` correctly iterates over every row passed to it, and `StudyController::mnemonics` already fetches the full `WHERE system_id = ?` set. **No code path is hiding rows.**

Action: filed as a content TODO — admins need to seed additional mnemonics via the AI generator or the admin editor. No code change can manufacture them.

## What did ship (code-side polish)

1. **`id="m-{id}"` on every `.mn-card`** — gives the topbar search (Phase 10) and any future deep-link a stable anchor. `:target` highlight added.
2. **Top-of-page filter strip** — keyword input that hides cards whose phrase / why / example don't contain the term, plus a "Jump to another system…" select listing every system with at least one mnemonic and the mnemonic count per system. Solves the brief's "filters at the top: subject, system, keyword" item.
3. **Aircraft filter** — intentionally omitted. Q400 is the only aircraft with content; the existing system jumper already implies the aircraft. Once `systems.aircraft_id` exists, the picker partial's aircraft filter pattern can be re-used.
4. **Lesson breadcrumb already links here** — confirmed via `studyChromeData(...)`. The mode switcher renders a "Mnemonics" tab that's now reachable from any lesson when `mnemonics_v2` is on (it is, in prod).

## Files changed

```
app/Controllers/StudyController.php       +20 lines (allSystems query for jumper)
views/study/mnemonics.php                 +50 lines (filter strip, anchors, JS)
```

## Verification checklist

- [ ] `/study/1/mnemonics` — page renders with filter input + system jumper + the (currently lone) mnemonic card.
- [ ] System jumper lists 9 systems (matching the prod count) with mnemonic counts displayed inline.
- [ ] Keyword filter — type "fuel" and only fuel-containing cards stay visible.
- [ ] Top-bar search → click a Mnemonic result → lands on `/study/N/mnemonics#m-X` and the matching card has the accent-colored highlight border.
- [ ] Selecting a different system in the jumper navigates to that system's mnemonics page.

## Content TODO (out-of-scope of this phase)

Each system should have **3–5 mnemonics**, not 1. The AI seed-generator probably emitted only the highest-confidence phrase. Run the admin AI batch with a higher count target, or have an instructor add manually via the admin editor (`/admin/mnemonics`). 9 systems × 4 mnemonics = ~36 rows to add.
