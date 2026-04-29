# AviatorTutor Content Inventory — 2026-04-29

Generated during Phase 4 of the premium-platform upgrade. Lists every
content asset known to the project so future phases (additional aircraft
libraries, subject packs, instructor-published modules) can plan against
real state instead of guessing.

## Q400 system content (database/seeds/content_*.json)

22 ATA-organised system files, each containing structured lesson seed data
(title, body, key_facts, must_know, exam_traps). Run
`scripts/seed_database.php` to import.

| File | ATA | Notes |
|---|---|---|
| content_aeroplane_general.json | ATA 06 | Aeroplane general & dimensions |
| content_air_conditioning_and_pressurization.json | ATA 21 | Pack flow, pressurisation logic |
| content_autoflight.json | ATA 22 | Autopilot, FD, autothrottle |
| content_communications.json | ATA 23 | VHF/HF, ATC, ACARS |
| content_electrical_power.json | ATA 24 | AC/DC buses, generators, batteries |
| content_fire_protection.json | ATA 26 | Engine/APU/cargo/lavatory fire |
| content_flight_controls.json | ATA 27 | Primary, secondary, trim, spoilers |
| content_fuel.json | ATA 28 | Tanks, pumps, crossfeed, dump |
| content_hydraulic_power.json | ATA 29 | **Phase 3 sample lesson** — full slide deck seeded |
| content_ice_and_rain_protection.json | ATA 30 | De-ice / anti-ice, rain repellent |
| content_indicating_and_recording.json | ATA 31 | EFIS, EICAS, recorders |
| content_landing_gear.json | ATA 32 | Gear, brakes, anti-skid, NWS |
| content_lighting.json | ATA 33 | Cockpit, cabin, exterior |
| content_navigation.json | ATA 34 | INS, GPS, RNAV/RNP, radios |
| content_oxygen.json | ATA 35 | Crew & pax oxygen |
| content_pneumatics.json | ATA 36 | Bleed, ducts, isolation |
| content_powerplant.json | ATA 71-80 | PW150A engine groups |
| content_propeller.json | ATA 61 | Dowty R408 prop |
| content_fms.json | ATA 22/34 | FMS performance & nav |
| content_caution_and_warning_messages.json | — | EICAS messages catalog |
| content_du_messages.json | — | Display unit message catalog |
| content_quick_reference_handbook.json | — | **Full Q400 QRH text (293 KB)** — extracted body, ready for QRH cross-references |

## Slide-based lessons (database/seeds/slides_*.sql)

| File | Lesson | Slides | Question gates | QRH-linked |
|---|---|---|---|---|
| slides_hydraulic_power.sql | Q400 Hydraulic Power (ATA 29) | 12 | 3 (slides 4, 7, 11) | Yes — see Phase 4 seed |

## QRH cross-references (Phase 4)

| File | Lesson | Sections linked |
|---|---|---|
| lesson_qrh_links_hydraulic.sql | Q400 Hydraulic Power | HYD 1 PRESS LO, HYD 2 PRESS LO, HYD 1+2 dual loss (memory item) |

## Image / diagram assets

### public/assets/aircraft/q400/
- `cockpit.webp` — main Q400 cockpit hero
- `cockpit-2x.webp` — 2× retina variant
- `cockpit-poster.webp` — video poster

### views/diagrams/
- `electrical.php` — hardcoded interactive electrical schematic (one-off)
- `show.php` — generic diagram renderer (DB-driven via `diagrams` table)

### public/assets/uploads/, storage/uploads/
**Empty.** Hydraulic slide media URLs reference
`/assets/uploads/hydraulics/*.png|jpg|mp4|json` but none of those files
exist yet. The slide player gracefully shows "Visual content coming soon"
placeholders when an image fails to load — see
`views/study/lesson_slides.php` lines 145–181.

**Manual follow-up:** drop diagrams under `public/assets/uploads/hydraulics/`
matching the seeded `media_url` paths (or use the admin slide editor at
`/admin/slides` to point each slide at a different `media_url`):

| Path expected | Slide |
|---|---|
| `/assets/uploads/hydraulics/q400_hydraulics_overview.png` | 1 — Why Hydraulics Matter |
| `/assets/uploads/hydraulics/three_main_systems.png` | 2 — Three mains + hand pump |
| `/assets/uploads/hydraulics/no1_no2_systems.png` | 3 — No.1 / No.2 mains |
| `/assets/uploads/hydraulics/no3_failover_animation.json` | 4 — No.3 standby (animation) |
| `/assets/uploads/hydraulics/cockpit_hyd_panel.png` | 5 — Pressure panel |
| `/assets/uploads/hydraulics/fluid_warning.png` | 6 — Phosphate ester |
| `/assets/uploads/hydraulics/single_failure_matrix.png` | 7 — Single-failure matrix |
| `/assets/uploads/hydraulics/dual_failure_brief.mp4` | 8 — Dual-failure brief (video) |
| `/assets/uploads/hydraulics/scenario_map.png` | 9 — Lake Turkana scenario |
| `/assets/uploads/hydraulics/qrh_hyd1_press_lo.png` | 10 — QRH page screenshot |
| `/assets/uploads/hydraulics/dual_failure_approach.json` | 11 — Approach animation |

Slide 12 is a recap with no media — already correct.

## Database tables relevant to study content

| Table | Purpose | Phase added |
|---|---|---|
| `systems` | Aircraft systems (22 Q400 systems seeded) | Phase 1 schema |
| `lessons` | Lessons within a system | Phase 1 schema |
| `lesson_sections` | Legacy per-section breakdown (overview/components/...) | Phase 1 schema |
| `lesson_slides` | Slide-based lesson player | Phase 3 (`2026_04_29_lesson_slides.sql`) |
| `user_slide_progress` | Per-slide progress per user | Phase 3 |
| `lesson_slides.show_beginner / _intermediate / _advanced` | Difficulty visibility | **Phase 2** (`2026_04_29_slide_difficulty.sql`) |
| `lesson_qrh_links` | Lesson↔QRH cross-references with memory-item flags | **Phase 4** (`2026_04_29_lesson_qrh_links.sql`) |
| `flashcards` | Q&A pairs with SM-2 scheduling | Phase 1 schema |
| `flashcard_reviews` | Per-card user reviews / ease factor | Phase 1 schema |
| `quizzes`, `quiz_questions`, `quiz_attempts`, `quiz_answers` | Quiz framework | Phase 1 schema |
| `diagrams`, `diagram_hotspots`, `diagram_states` | Interactive system diagrams | Phase 1 schema |
| `study_assets` | PDFs / images attached to lessons | Phase 1 schema |
| `contact_messages.subject` | Inquiry subject column | **Phase 1** (`2026_04_29_contact_subject.sql`) |

## Migrations to apply (in order)

```
database/migrations/
  2026_04_28_subjects_and_purchases.sql      # existing
  2026_04_29_contact_messages.sql            # existing
  2026_04_29_contact_subject.sql             # NEW (Phase 1)
  2026_04_29_lesson_slides.sql               # existing
  2026_04_29_phase2_columns.sql              # existing
  2026_04_29_slide_difficulty.sql            # NEW (Phase 2)
  2026_04_29_lesson_qrh_links.sql            # NEW (Phase 4)
```

Apply with:

```
mysql -u root aviatortutor < database/migrations/<file>.sql
```

Then reseed the hydraulic content if needed:

```
mysql -u root aviatortutor < database/seeds/slides_hydraulic_power.sql
mysql -u root aviatortutor < database/seeds/lesson_qrh_links_hydraulic.sql
```

## What's still needed for full Q400 polish

1. **Diagram assets** — drop images under `public/assets/uploads/hydraulics/`
   (paths above), or update `media_url` per slide via `/admin/slides`.
2. **Slide decks for the other 21 systems** — each needs its own
   `slides_<system>.sql` seed mirroring the hydraulic pattern (12 slides,
   3+ question gates, memory hooks, scenario decision).
3. **QRH cross-references for the other 21 systems** — extend
   `lesson_qrh_links` with rows for each lesson (1–3 QRH sections per
   system; mark dual/triple-failure procedures as memory items).
4. **Difficulty tagging** — for each new deck, set
   `show_beginner/_intermediate/_advanced` per the Phase 2 convention
   (foundations visible to beginners; abnormals/QRH/scenarios hidden until
   intermediate; "captain decision" scenarios advanced-only).
5. **Quick Revision JSON enrichment** — populate `lessons.key_facts`,
   `must_know`, `exam_traps` with 8–12 entries per system so
   `views/study/revision.php` renders dense content automatically (the
   view itself is content-driven and needs no code change).
