# Phase 0 — cPanel Deploy Unblock Runbook

**Goal:** get `https://aviatortutor.com/` returning HTTP 200 (currently 404).

**Audience:** Captain Samic (you). Some steps run on your **laptop**, others in **cPanel → Terminal** (or SSH). Each step is labelled.

**Time:** ~30 min if no surprises.

**Why this exists:** the AviatorTutor code has been pushed to `main` on GitHub, and cPanel pulls (via Git Version Control "Update from Remote") into `/home/fruinxrj/aviatortutor` — which IS the live doc root for aviatortutor.com (it's an addon domain, not the main `public_html`). The server-side env files (`config/database.local.php`, `config/app.local.php`) and the schema install (`tools/install_db.php`) were never completed, so every route 404s. This runbook finishes those `DEPLOY.md` Steps 3–5 in a clean order.

**Server account:** `fruinxrj@business100.web-hosting.com` | cPanel base: `https://business100.web-hosting.com:2083/` | Repo name in cPanel: `aviatortutor-ant`.

---

## Pre-flight verified by Claude (laptop)

| Check | Result |
|---|---|
| Git remote `aviatortutor.com` | ✅ → `github.com/Samic333/aviatortutor-ant.git` |
| PHP lint `app/` + project | ✅ clean, no syntax errors |
| Local branch | ⚠️ on `feat/self-study-platform` — **must merge to `main` before pushing** |
| Branch divergence | `feat` is 25 ahead of `aviatortutor.com/main`, 0 behind — fast-forward merge OK |
| `config/database.local.php.example` | ✅ exists |
| `config/app.local.php.example` | ✅ exists |
| `tools/install_db.php` | ✅ exists |
| `tools/create_admin.php` | ✅ exists |
| `tools/generate_codes.php` | ✅ exists |
| Live homepage | ❌ HTTP 404 (LiteSpeed responds but routes 404 → confirms env gap) |

---

## Step 1 — [LAPTOP] Backup the live DB & files

Even though the live site is 404, the database may still hold previous test data and the existing `public_html` may have files we don't want to lose. Take backups before anything destructive.

**Option A — via cPanel UI (simplest):**
1. cPanel → **phpMyAdmin** → select the live database → **Export** → Custom → check `Save as file`, gzip, → Go. Save the `.sql.gz` somewhere safe.
2. cPanel → **File Manager** → right-click `public_html` → **Compress** → ZIP. Download the zip.

**Option B — via cPanel Terminal:**
```bash
mkdir -p ~/backups
DT=$(date +%Y%m%d-%H%M)

# DB dump — replace DBNAME / DBUSER with your real Namecheap values; you'll be prompted for password
mysqldump -h localhost -u DBUSER -p DBNAME | gzip > ~/backups/aviatortutor-pre-phase0-$DT.sql.gz

# Files tarball (skip storage caches)
tar --exclude='aviatortutor/storage/sessions/*' \
    --exclude='aviatortutor/storage/cache/*' \
    -czf ~/backups/aviatortutor-pre-phase0-$DT.tar.gz \
    aviatortutor/

ls -lah ~/backups/
```

Both backup files must be **> 0 bytes** before continuing.

---

## Step 2 — [LAPTOP] Merge feat → main and push to GitHub

Currently your active branch is `feat/self-study-platform`. The server pulls from `main`, so we need main to point at the latest feat commit.

> **Note:** the local remote named `aviatortutor.com` is just an alias for `github.com/Samic333/aviatortutor-ant.git`. Pushing here puts the code on GitHub; it does **not** auto-deploy to the server. The server pull happens in Step 2b.

```bash
cd /Users/samic/Desktop/Antigravity/AviatorTutor

# Make sure local main is up to date with remote main
git fetch aviatortutor.com
git checkout main
git reset --hard aviatortutor.com/main   # only if local main is stale; safe because feat has all commits

# Fast-forward main to include all 25 feat commits
git merge --ff-only feat/self-study-platform

# Push to GitHub
git push aviatortutor.com main
```

### Step 2b — [cPanel] Pull the new code onto the server

Two ways to do this. Either works; pick whichever is easier:

**Option A — cPanel UI (clicky):**
1. cPanel → **Git Version Control**
2. Find the row for `aviatortutor-ant` → click **Manage**
3. Click the **Pull or Deploy** tab → **Update from Remote**
4. Wait for "Last Update" timestamp to refresh

**Option B — cPanel Terminal (faster):**
```bash
cd ~/aviatortutor && git pull
```

Either way, expect the live site to return **HTTP 500** between this pull and the end of Step 4 — that's normal (no `database.local.php` yet).

---

## Step 3 — [cPanel TERMINAL] Configure production env

Open cPanel → **Terminal** (under "Advanced"). All commands below run **on the server**.

```bash
cd ~/aviatortutor

# Confirm Step 2b actually pulled the latest commit
git log -1 --oneline
# Should match the commit you pushed in Step 2.
```

### 3a. Database credentials

```bash
cp config/database.local.php.example config/database.local.php
nano config/database.local.php
```

Fill in **your real Namecheap MySQL values** (find them in cPanel → MySQL Databases):

```php
return [
    'host'     => 'localhost',
    'database' => 'fruinxrj_aviatortutor',     // your actual DB name (likely fruinxrj_<something>)
    'username' => 'fruinxrj_avtutor',          // your actual DB user
    'password' => 'YOUR_REAL_PASSWORD_HERE',
];
```

Save: `Ctrl+O` → Enter → `Ctrl+X`.

### 3b. App config (base URL + encryption key)

Generate a 32+ char random key first:

```bash
openssl rand -base64 48
```

Copy the output. Then:

```bash
cp config/app.local.php.example config/app.local.php
nano config/app.local.php
```

Edit the file so it has at minimum:

```php
return [
    'debug'          => false,
    'base_url'       => 'https://aviatortutor.com',
    'encryption_key' => 'PASTE_THE_OPENSSL_OUTPUT_HERE',

    'require_email_verification' => true,
];
```

(You can leave the Stripe / AI keys blank — they're optional for Phase 0.)

Save: `Ctrl+O` → Enter → `Ctrl+X`.

### 3c. Verify config resolves

```bash
php -r "require 'config/database.php'; \$c = require 'config/database.php'; echo 'DB: ' . \$c['database'] . PHP_EOL . 'User: ' . \$c['username'] . PHP_EOL;"
```

Should print your DB name + user (no errors). If it errors, the file has a typo — re-edit.

```bash
php -r "\$c = require 'config/app.php'; echo 'base_url: ' . \$c['base_url'] . PHP_EOL . 'debug: ' . var_export(\$c['debug'], true) . PHP_EOL;"
```

Should print `base_url: https://aviatortutor.com` and `debug: false`.

---

## Step 4 — [cPanel TERMINAL] Install schema, create admin, generate codes

```bash
cd ~/aviatortutor

# 4a. Install fresh schema (drops any existing tables)
php tools/install_db.php
```

Expected output:
```
Target DB: fruinxrj_aviatortutor
Schema imported.
Done.
```

If you get `SQLSTATE[42000]: ... DROP command denied to user`: the DB user lacks `DROP` privilege.
Fix: cPanel → MySQL Databases → "Add User to Database" → grant **All Privileges**, then re-run.

```bash
# 4b. Create your admin account
php tools/create_admin.php samickenya@gmail.com 'PickAStrongPassword2026' "Captain Samic"
```

Expected: `Admin created: samickenya@gmail.com (id=1)` or similar.

```bash
# 4c. Generate 5 testing codes
php tools/generate_codes.php --count=5 --days=30 --plan=monthly
```

**Save the 5 codes that print to stdout** — copy them to a note. You'll use one in Step 5e.

---

## Step 5 — [LAPTOP] Live verification

Run each curl from your laptop. **All must pass.**

```bash
# 5a. Homepage 200
curl -sI https://aviatortutor.com/ | head -1
# Expect: HTTP/2 200

# 5b. Pricing 200
curl -sI https://aviatortutor.com/pricing | head -1
# Expect: HTTP/2 200

# 5c. Login 200
curl -sI https://aviatortutor.com/login | head -1
# Expect: HTTP/2 200

# 5d. Sitemap renders XML
curl -s https://aviatortutor.com/sitemap.xml | head -5
# Expect: <?xml version="1.0" ...

# 5e. /systems while logged out → 302 redirect to /login
curl -sI https://aviatortutor.com/systems | grep -i location
# Expect: location: /login

# 5f. Old marketplace URL → 301 redirect
curl -sI https://aviatortutor.com/instructors | grep -iE 'HTTP|location'
# Expect: HTTP/2 301, location: /
```

If all six pass: **homepage in a private browser window**, register a new account with one of the codes from Step 4c, log in, confirm you land on `/dashboard`. Visit `/systems/hydraulic-power` — Hydraulics existing seeds should render.

---

## Step 6 — Commit this runbook & report back

Once everything is green:

**On laptop:**
```bash
cd /Users/samic/Desktop/Antigravity/AviatorTutor
git add DEPLOY_RUNBOOK_PHASE_0.md
git commit -m "docs(deploy): Phase 0 cPanel unblock runbook"
git push aviatortutor.com main
```

Tell Claude: **"Phase 0 done — site is live"**. Claude will then start Phase 1 (Aeroplane General — ATA 21).

---

## Rollback (only if something breaks badly)

```bash
# On server
cd ~
mv public_html public_html_broken
mkdir public_html
tar -xzf ~/backups/aviatortutor-pre-phase0-YYYYMMDD-HHMM.tar.gz \
    --strip-components=1 -C public_html

# Restore DB if 4a corrupted it
gunzip -c ~/backups/aviatortutor-pre-phase0-YYYYMMDD-HHMM.sql.gz \
  | mysql -h localhost -u DBUSER -p DBNAME

# Reset cPanel Git pointer in cPanel → Git Version Control → Manage → revert to previous commit
```

Then ping Claude with the error output and we'll diagnose before attempting again.

---

## Common gotchas

| Symptom | Likely cause | Fix |
|---|---|---|
| Step 4a: `Access denied for user` | Wrong DB password in `database.local.php` | Re-check cPanel → MySQL Databases → user → password |
| Step 4a: `DROP command denied` | DB user lacks DROP privilege | cPanel → MySQL Databases → Add user → All Privileges |
| Step 5a: still 404 | `.htaccess` missing or AllowOverride off | `ls -la ~/aviatortutor/.htaccess` — should exist; if missing, the deploy didn't include it (rare). Check `~/aviatortutor/public/.htaccess` too. |
| Step 5a: 500 | PHP error — check `~/aviatortutor/storage/logs/php-error.log` | Most likely typo in `app.local.php` or `database.local.php` |
| Browser redirects loop | `base_url` mismatch | Confirm `app.local.php` has `https://aviatortutor.com` exactly (no trailing slash, no `http`) |
| Blank page, no error | `display_errors` off + log not writable | `chmod -R 775 storage/` |

---

## What Claude prepared on the laptop side

- ✅ PHP lint passed across all `app/` files
- ✅ Confirmed git remote + branch divergence (clean ff-merge available)
- ✅ Verified config example templates exist with all required keys
- ✅ Verified `tools/install_db.php`, `create_admin.php`, `generate_codes.php` are all present
- ✅ This runbook (committable so re-deploys are repeatable)

After you confirm Phase 0 is green, Claude will start Phase 1 (ATA 21 — Aeroplane General) following the per-phase template in `~/.claude/plans/work-on-aviator-tutor-abstract-thompson.md`.
