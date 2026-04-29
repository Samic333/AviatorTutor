# Deploy guide — aviatortutor.com cutover

> **Read this end-to-end before pushing `main`.** Pushing `main` triggers the cPanel Git auto-deploy, which replaces `public_html/` with the new self-study platform code. The old marketplace site goes down at that moment, and stays down until you complete the post-deploy steps below.

---

## What's changing

| | Before | After |
|---|---|---|
| Concept | Instructor marketplace | Premium aviation self-study, $10 / month |
| Homepage | "Master Aviation Skills With World-Class Instructors" | Premium dark-navy marketing site |
| Database schema | Marketplace tables (users, classes, bookings…) | Q400 study schema (25 tables) + 7 new (subscriptions, activation_codes, …) |
| Subscription | n/a | Activation codes (admin issues, user redeems) |
| Auth | Marketplace logins | Same `users` table, fresh schema |
| SEO | No `robots.txt` / `sitemap.xml` | Both present + 301 redirects from old `/instructors`, `/classes`, `/student/*`, `/instructor/*` |

The cutover is **destructive to the live database** by design — this is the user-approved decision (no marketplace user / class / booking data preserved).

---

## Step 1 — Back up the live site (required, do not skip)

SSH into Namecheap (or use cPanel → Terminal):

```bash
# Replace USER, DBHOST, DBNAME, DBUSER with your real Namecheap values.
mkdir -p ~/backups
DT=$(date +%Y%m%d-%H%M)

# 1a. Database dump
mysqldump -h DBHOST -u DBUSER -p DBNAME \
  | gzip > ~/backups/aviatortutor-pre-cutover-$DT.sql.gz

# 1b. public_html tarball (excluding heavy caches)
tar --exclude='public_html/storage/sessions/*' \
    --exclude='public_html/storage/cache/*' \
    -czf ~/backups/public_html-pre-cutover-$DT.tar.gz \
    public_html/

ls -lah ~/backups/  # confirm both files are >0 bytes
```

If you don't have shell SSH:
- **DB:** cPanel → phpMyAdmin → select the live DB → Export → Custom → "Save as file" + gzip → download.
- **Files:** cPanel → File Manager → select `public_html` → Compress → download the `.zip` to your laptop.

**Don't push `main` until both backup files exist and are sized > 0.**

---

## Step 2 — Push to `main` (this is the moment of cutover)

On your laptop, in `/Users/samic/Desktop/Antigravity/AviatorTutor/`:

```bash
git checkout main
git merge --ff-only feat/self-study-platform
git push aviatortutor.com main
```

cPanel's Git deploy hook pulls `main` into `public_html/`. The site will return **HTTP 500** until Step 3 finishes — this is expected. The old marketplace is gone the instant the push lands.

---

## Step 3 — Configure production env

SSH into the server, in `~/public_html/`:

```bash
# 3a. Create the local DB override (gitignored, never overwritten by deploy)
cp config/database.local.php.example config/database.local.php
nano config/database.local.php
# Fill in: host, database, username, password from your Namecheap MySQL.

# 3b. Create the local app override
cp config/app.local.php.example config/app.local.php
nano config/app.local.php
# Set debug=false, base_url='https://aviatortutor.com',
# encryption_key=<32+ random chars, e.g. `openssl rand -base64 32`>.

# 3c. Verify the connection string resolves
php -r "require 'config/database.php'; print_r(require 'config/database.php');" \
  | grep -E 'database|username'
```

---

## Step 4 — Install fresh schema and seed

```bash
# 4a. Wipe + re-import the schema (this DROPS the old marketplace tables)
php tools/install_db.php

# 4b. Create the first admin (use your real email + a strong password)
php tools/create_admin.php samickenya@gmail.com 'YourStrongPassword2026' "Captain Samic"

# 4c. Generate 5 activation codes for testing
php tools/generate_codes.php --count=5 --days=30 --plan=monthly
# Save the codes printed to stdout — you'll use one to test the full flow.
```

If `install_db.php` errors, the most likely cause is the DB user lacking `DROP TABLE` privilege. Either grant it in cPanel → MySQL Databases → Add User to Database → All Privileges, or run the schema in phpMyAdmin manually.

---

## Step 5 — Live QA on `https://aviatortutor.com`

Open in a private window (no stale session cookie):

| Check | Expected |
|---|---|
| `https://aviatortutor.com/` | Premium marketing homepage, no "instructor marketplace" text |
| `https://aviatortutor.com/pricing` | $10 / month card, "Start studying" CTA |
| `https://aviatortutor.com/about`, `/contact`, `/faq`, `/privacy`, `/terms` | All 200, marketing layout |
| `https://aviatortutor.com/sitemap.xml` | Valid XML, 15 URLs |
| `https://aviatortutor.com/robots.txt` | `Allow: /` + disallows for `/admin/`, `/api/`, `/redeem`, `/account`, `/dashboard` |
| `curl -I https://aviatortutor.com/instructors` | `301` → `/` |
| `curl -I https://aviatortutor.com/instructor/profile` | `301` → `/dashboard` |
| Register a new account | Lands on dashboard paywall |
| `/redeem` + valid code | Dashboard unlocks, study routes accessible |
| `/systems` while logged out | `302` → `/login` |
| Mobile (375px) | Hero, pricing, FAQ all readable |
| DevTools console | No errors |

---

## Step 6 — Post-cutover

- **Submit sitemap** to Google Search Console: `https://aviatortutor.com/sitemap.xml`.
- **Submit removed-URLs** in Search Console for `/instructors`, `/classes`, `/student/*`, `/instructor/*` (optional — the 301s do most of the work).
- **Generate batch of codes** for any beta users: `php tools/generate_codes.php --count=20 --days=30`.
- **Check `storage/logs/php-error.log`** after 24 hours — no entries means a clean cutover.

---

## Rollback (if anything goes wrong)

If the new site is broken and you need the old marketplace back fast:

```bash
# 1. Restore public_html
cd ~ && rm -rf public_html_broken && mv public_html public_html_broken
mkdir public_html
tar -xzf ~/backups/public_html-pre-cutover-YYYYMMDD-HHMM.tar.gz \
    --strip-components=1 -C public_html

# 2. Restore the DB (assuming the new schema was already imported)
gunzip -c ~/backups/aviatortutor-pre-cutover-YYYYMMDD-HHMM.sql.gz \
  | mysql -h DBHOST -u DBUSER -p DBNAME

# 3. Reset the cPanel Git pointer back to the previous commit so the next deploy
#    doesn't re-apply the new code — easiest done in cPanel → Git Version Control
#    → Manage → "Pull or Deploy" → revert.
```

Original `main` HEAD before cutover: `25dab48` (`fix: restore hero image and clean up production environment settings`).

---

## Files NEVER committed

- `config/database.local.php` — production DB credentials
- `config/app.local.php` — production base URL + encryption key
- `storage/logs/*`, `storage/sessions/*`, `storage/cache/*`
- `.env`, `.env.backup`

If any of the above show up in `git status`, do **not** stage them.

---

## SMTP setup (Phase 5 admin reply, registration verify)

The codebase uses PHP's built-in `mail()` for outbound email — verification
links, password resets, admin replies to contact-form inquiries.
`mail()` works on most cPanel hosts (Namecheap included) when the From
address belongs to the same domain. If delivery is unreliable, switch to
SMTP-via-PHPMailer.

### Quick SMTP enablement (PHPMailer)

1. SSH or open cPanel Terminal in the project root, then:

   ```bash
   composer require phpmailer/phpmailer
   ```

   (Composer is **not** otherwise required by AviatorTutor, but it's
   acceptable as an optional production dependency for SMTP.)

2. Update `app/Services/EmailService::send()` to detect PHPMailer when
   present, falling back to `mail()` when it isn't:

   ```php
   if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
       // ... configure PHPMailer with $cfg['smtp_*'] keys ...
   } else {
       $sent = @mail(...);
   }
   ```

3. Add the SMTP credentials to `config/app.local.php` (gitignored):

   ```php
   'smtp_host'     => 'mail.aviatortutor.com',  // or smtp.gmail.com etc.
   'smtp_port'     => 587,
   'smtp_username' => 'no-reply@aviatortutor.com',
   'smtp_password' => '<from cPanel email accounts>',
   'smtp_auth'     => true,
   'smtp_secure'   => 'tls',
   ```

4. Verify by submitting a public `/contact` form, then sending a reply
   from `/admin/contacts/{id}` and confirming arrival in the user's inbox.
   `storage/logs/mail.log` records every attempt with `sent=OK|FAIL`.

### Without PHPMailer

If you stay on bare `mail()`, ensure cPanel → Email Accounts has
`no-reply@aviatortutor.com` (or the configured `mail_from`) created so
SPF/DKIM aligns. Check SPF in cPanel → Email Deliverability: it should
show `Status: Valid` for the domain.

The AviatorTutor mail flow is already fault-tolerant — every send is
logged to `storage/logs/mail.log`, and on failure the full HTML body is
also dumped to `storage/logs/mail-failures.log` so you can manually
recover the verification link or admin reply if SMTP is down.
