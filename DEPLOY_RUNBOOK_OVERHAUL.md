# AviatorTutor Overhaul — Master Deploy Runbook

Single deploy plan covering all 5 phases of the May 2026 UX/UI overhaul,
including the post-Phase-5 follow-up fixes (admin subject-requests UI,
Continue deep-link, mind-map depth, Add-Subject modal, slide-completion
analytics, idempotent migration runner, smoke tester).

Every change is gated behind a feature flag in `config/app.php`, so the
rollout is: deploy code → run migrations → flip flags one phase at a
time on prod, validating each before the next.

---

## 0. Feature flags reference

`config/app.local.php` overrides `config/app.php` features. Flags landed:

| Flag                  | Phase | What it gates |
|-----------------------|-------|---------------|
| `nav_my_subjects`     | 1     | Sidebar swap "My Aircraft" → "My Subjects" |
| `friendly_errors`     | 1     | Friendly 500 page (also auto-on in prod when `debug=false`) |
| `study_chrome_v2`     | 2     | Single-topbar study layout, drawer, slide picker |
| `theme_drawer`        | 3     | Settings drawer + 6 themes (mostly informational; drawer is always wired) |
| `flashcards_v2`       | 3     | Color-coded swipeable flashcards |
| `mnemonics_v2`        | 3     | Mnemonics study mode + DB-backed mnemonics |
| `mind_map`            | 3     | Mind map study mode |
| `deep_notes`          | 3     | Deep notes study mode |
| `dashboard_v2`        | 4     | Compact KPIs + promo panel |
| `system_picker_v2`    | 4     | Search + dropdown + grouped tile picker |
| `add_subject_flow`    | 4     | (Modal Add-Subject is already wired into the My Subjects view; this flag is currently unused — leave false unless you wire it into a CTA elsewhere) |
| `analytics_v1`        | 5     | Admin `/admin/analytics` dashboard |

---

## 1. Pre-deploy

### 1a. Backup
```bash
# On cPanel (Namecheap shared hosting):
mkdir -p ~/backups
mysqldump aviatortutor > ~/backups/aviatortutor-pre-overhaul-$(date +%F).sql
tar -czf ~/backups/aviatortutor-files-$(date +%F).tar.gz ~/aviatortutor
```

### 1b. Sanity check the local branch
```bash
cd ~/Desktop/Antigravity/AviatorTutor
git status                       # confirm only the overhaul files are dirty
git log --oneline origin/main..HEAD
```

---

## 2. Push + pull

```bash
# Local
git push origin main

# cPanel Terminal
cd ~/aviatortutor
git pull
```

---

## 3. Run migrations (one command)

The new idempotent runner reads every `database/migrations/*.sql`, tracks
applied filenames in `schema_migrations`, and re-running it is a safe no-op.

```bash
cd ~/aviatortutor

# Preview what would run:
php tools/migrate.php --dry-run

# Apply pending migrations:
php tools/migrate.php

# Confirm:
php tools/migrate.php --status
```

**Migrations applied in this overhaul** (in filename order):
1. `2026_05_03_subject_requests.sql`        — Phase 1
2. `2026_05_03_diagram_verified.sql`        — Phase 1
3. `2026_05_03_user_settings.sql`           — Phase 3
4. `2026_05_03_mnemonics.sql`               — Phase 3
5. `2026_05_03_flashcards_v2.sql`           — Phase 3
6. `2026_05_03_systems_category.sql`        — Phase 4
7. `2026_05_03_analytics_events.sql`        — Phase 5

**Optional content seed** (replaces the 4 hardcoded mnemonics with 9 explained ones across the system library):
```bash
mysql aviatortutor < database/seeds/mnemonics_seed.sql
```

---

## 4. Diagram audit (gates Phase 1 acceptance)

```bash
cd ~/aviatortutor

# Pre-fix snapshot for the audit trail:
php tools/audit_diagrams.php --out=audit-pre.csv

# Auto-fix slug/URL mismatches and mark verified rows:
php tools/audit_diagrams.php --apply

# Confirm zero mismatches:
php tools/audit_diagrams.php --summary
# Expect: audited=N ok=N mismatch=0 missing=0
```

If `missing > 0`: a slide references an SVG that doesn't exist. Either
upload the SVG to `public/assets/aircraft/q400/` or fix the URL.

---

## 5. Flag rollout (one phase at a time)

Edit `config/app.local.php` (gitignored). Start with Phase 1, validate,
then proceed.

### Phase 1 — Stop the bleeding
```php
<?php
return [
    'features' => [
        'nav_my_subjects' => true,
        'friendly_errors' => true,
    ],
];
```
**Smoke (manual):**
- `/study/1/lesson/1` returns 200 — no HTTP 500.
- Sidebar shows "My Subjects" → `/my-subjects`.
- Force a 500 (rename a controller method temporarily) → friendly error page with request id.
- `tools/smoke.php` passes (see §7).

### Phase 2 — Study chrome
```php
'study_chrome_v2' => true,
```
**Smoke:**
- `/study/1/lesson/1`: single thin topbar, no stacked headers, no double sidebars.
- Tap progress bar → 6-column slide picker grid.
- Hamburger → drawer slides in (bottom sheet on mobile).
- 3s idle → topbar hides; mouse to top edge → reappears.
- Flag back to false → old layout still renders cleanly (rollback safety).

### Phase 3 — Modes & personalization
```php
'theme_drawer'  => true,
'flashcards_v2' => true,
'mnemonics_v2'  => true,
'mind_map'      => true,
'deep_notes'    => true,
```
**Smoke:**
- Topbar settings cog → drawer; pick Sepia → page recolours immediately.
- Reload → theme persists (server + localStorage).
- `/study/1/mnemonics`, `/study/1/mind-map`, `/study/1/deep-notes` all 200.
- Mind map shows: system → lesson → Sections bucket + Key facts/Must know/Exam traps buckets → leaves.
- `/flashcards/1` shows colour-coded categories; swipe-right animates off, score increments.
- Mode switcher in topbar links live (not greyed) for Mnemonics / Mind Map / Deep Notes.

### Phase 4 — Subject management
```php
'dashboard_v2'     => true,
'system_picker_v2' => true,
```
**Smoke:**
- `/dashboard` KPIs are compact + promo panel renders right.
- 1366×768 laptop fits KPIs + Continue + Subjects + Promo above the fold.
- `/systems` shows search + dropdown + tiles grouped by category (Powerplant, Electrical, Hydraulics, Avionics, Environmental, Reference).
- `/my-subjects`:
  - Cards show progress bar + due flashcards count.
  - "Continue studying →" deep-links to the most recent lesson the user touched.
  - "Add a new subject" tile opens a centered modal (not an inline form).
  - Modal opens with the chosen slug pre-selected when reached via `/my-subjects?add=b777-pack`.
- "Request access" inserts a row into `subject_requests` and emails the admin.
- `/admin/subject-requests`:
  - Lists every request with status pills.
  - "Save quote" persists `quoted_amount_usd` + admin notes.
  - "Grant access" inserts a `purchases` row → learner immediately sees the subject in My Subjects.
  - "Decline" / "Reopen" change status correctly.

### Phase 5 — Analytics
```php
'analytics_v1' => true,
```
**Smoke:**
- `/admin/analytics` returns 200 (admin only).
- After a few mode-switch clicks and theme changes, refresh → bars populate (mode usage, theme adoption, font-size adoption, top events).
- "Slide completion by system" chart populates as soon as any user has answered a slide gate (independent of analytics events).

---

## 6. End-to-end acceptance (all flags on)

| Criterion (from brief §12) | How to verify |
|---|---|
| No HTTP 500 on lesson load | `tools/smoke.php` green; click through every system on `/systems` |
| Font-size visibly scales characters | XS / S / M / L / XL via topbar cog → settings drawer; resize is character-level not just spacing |
| Diagrams match content | `php tools/audit_diagrams.php --summary` → mismatch=0 |
| ONE top bar / ZERO permanent sidebars | DevTools confirms one `<header class="study-topbar">` |
| Login → reading content in ≤2 taps | Login → My Subjects card "Continue studying →" lands directly on the most recent slide |
| `My Subjects` stays inside the app | URL stays `aviatortutor.com/my-subjects` |
| ≥3 themes work end-to-end | Dark, Light, High-Contrast all readable in slides + flashcards |
| Add Subject creates DB row + admin notify | `SELECT * FROM subject_requests` shows the test request; admin email arrives |
| iPhone Safari / Android Chrome / desktop Chrome / Firefox / Safari | Walk every flow on real devices (post-deploy task — see §8) |
| No console errors | DevTools clean on each top-level page; toast.js logs uncaught errors to `/api/client-error` |

---

## 7. Automated smoke tester

```bash
# Public routes only (works without auth):
php tools/smoke.php --base=https://aviatortutor.com

# With a logged-in cookie for full coverage:
#   1. Sign in to aviatortutor.com in a browser
#   2. DevTools → Application → Cookies → copy the aviatortutor_session cookie
#   3. Run with --cookie="aviatortutor_session=<value>"
php tools/smoke.php \
    --base=https://aviatortutor.com \
    --cookie="aviatortutor_session=ABC123…"
```

Exits 0 on full pass, 1 on any failure. Use it after every flag flip and
as a post-deploy gate.

---

## 8. What still needs YOUR hands (out of scope for code-only)

These remain on the post-deploy task list — none block the launch but
each should land before declaring v1 "done":

- **Real-device QA**: walk every flow on iPhone Safari, Android Chrome, desktop Chrome / Firefox / Safari.
- **Lighthouse**: Performance ≥90 on `/dashboard` and a slide page.
- **Axe DevTools**: zero serious / critical violations.
- **Screen-reader walkthrough**: dashboard, study modes, settings drawer, modal.
- **Stripe / Chapa payment integration** for Add Subject (explicitly Phase-2 of the brief).
- **Mnemonics audio (TTS)** — `audio_url` column ready; admin job to generate.
- **OpenDyslexic font** — drop a licensed `OpenDyslexic.woff2` into `public/assets/fonts/` and the existing CSS @font-face rule picks it up.
- **B777 content** — when it arrives, extend `tools/audit_diagrams.php`'s slug→SVG map.

---

## 9. Rollback (per phase)

Each phase has a single flag. Flip it to `false` in `config/app.local.php`
to restore the legacy surface — no code revert needed, no migrations to
undo (every Phase migration is additive).

If something is catastrophically wrong:
```bash
git revert <hash>
git push origin main
# cPanel:
cd ~/aviatortutor && git pull
```
The DB stays forward-compatible — old code reads the new tables/columns
without issue.

---

## 10. File inventory

### New files (33)

**Services (2)**
- `app/Services/UserSettings.php`
- `app/Services/Analytics.php`

**Controllers — methods added (existing files)**
- `SubjectsController.php` *(new)*
- StudyController: `mnemonics`, `mindMap`, `deepNotes`, `studyChromeV2`, `studyChromeData`
- AdminController: `analytics`, `subjectRequests`, `subjectRequestUpdate`
- ApiController: `settingsUpdate`, `clientError`, `trackEvent`
- FlashcardController: v2 view selection + `buildStudyChromeData`

**Views — new (10)**
- `views/layouts/study.php`
- `views/partials/lesson-drawer.php`
- `views/partials/settings-drawer.php`
- `views/partials/system-picker.php`
- `views/study/mnemonics.php`
- `views/study/mind_map.php`
- `views/study/deep_notes.php`
- `views/flashcards/study_v2.php`
- `views/admin/analytics.php`
- `views/admin/subject_requests.php`
- `views/subjects/index.php`
- `views/errors/friendly-500.php`

**CSS — new (5)**
- `public/assets/css/study-chrome.css`
- `public/assets/css/themes.css`
- `public/assets/css/settings-drawer.css`
- `public/assets/css/flashcards-v2.css`
- `public/assets/css/polish.css`

**JS — new (5)**
- `public/assets/js/toast.js`
- `public/assets/js/study-chrome.js`
- `public/assets/js/settings-drawer.js`
- `public/assets/js/mind-map.js`
- `public/assets/js/flashcards-v2.js`

**Tools — new (3)**
- `tools/audit_diagrams.php`
- `tools/migrate.php`
- `tools/smoke.php`

**Migrations — new (7)**
- `database/migrations/2026_05_03_subject_requests.sql`
- `database/migrations/2026_05_03_diagram_verified.sql`
- `database/migrations/2026_05_03_user_settings.sql`
- `database/migrations/2026_05_03_mnemonics.sql`
- `database/migrations/2026_05_03_flashcards_v2.sql`
- `database/migrations/2026_05_03_systems_category.sql`
- `database/migrations/2026_05_03_analytics_events.sql`

**Seeds — new (1)**
- `database/seeds/mnemonics_seed.sql`

### Modified files (15)
- `app/Controllers/StudyController.php`
- `app/Controllers/SubjectsController.php`
- `app/Controllers/SystemsController.php`
- `app/Controllers/FlashcardController.php`
- `app/Controllers/ApiController.php`
- `app/Controllers/AdminController.php`
- `app/Controllers/AircraftController.php` (no functional change in this round, listed for completeness if your branch touched it)
- `views/layouts/pilot.php`
- `views/study/lesson_slides.php`
- `views/study/detail.php`
- `views/dashboard/index.php`
- `views/systems/index.php`
- `views/subjects/index.php`
- `public/assets/css/pilot.css`
- `public/assets/css/app.css`
- `public/assets/js/lesson_slides.js`
- `public/index.php`
- `routes/web.php`
- `config/app.php`
