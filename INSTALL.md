# Q400 Study App — Installation Guide

## 🌐 Your Local URL: http://localhost:8080

---

## Option A — One-Click Setup (macOS with Homebrew) ⭐ Recommended

This installs everything automatically: PHP, MySQL, creates the database, and opens the app.

**Step 1:** Open Terminal (press `Cmd + Space`, type "Terminal", press Enter)

**Step 2:** Run this command:

```bash
cd ~/Desktop  # or wherever your "Q400 System Study" folder is
cd "Q400 System Study/q400-study"
chmod +x setup.sh
./setup.sh
```

That's it. The script will:
- Install Homebrew (if needed)
- Install PHP 8.2 + MySQL
- Create the `q400_study` database and import all data
- Start a local web server on port 8080
- Automatically open http://localhost:8080 in your browser

**Next time** you just need to run `./start.sh` — no reinstalling needed.

---

## Option B — Docker Desktop

If you have [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed:

```bash
cd "Q400 System Study/q400-study"
docker compose up -d
```

Open http://localhost:8080 — done. Stop with `docker compose down`.

---

## Option C — XAMPP / MAMP (Manual)

1. **Start XAMPP/MAMP** and make sure Apache and MySQL are running.

2. **Copy the project** into your web root:
   - XAMPP: `C:\xampp\htdocs\q400-study\` (Windows) or `/Applications/XAMPP/htdocs/q400-study/` (Mac)
   - MAMP: `/Applications/MAMP/htdocs/q400-study/`

3. **Create the database** — open phpMyAdmin (http://localhost/phpmyadmin):
   - Import `database/schema.sql`
   - Import `database/seed_data.sql`

4. **Open** http://localhost/q400-study/public/

---

## Login Credentials

| Role    | Email                      | Password   |
|---------|----------------------------|------------|
| Admin   | admin@q400study.local      | `password` |
| Learner | samuel@q400study.local     | `password` |
| Learner | alice@q400study.local      | `password` |

---

## Troubleshooting

**"MySQL refused connection"** — MySQL isn't running. Run:
```bash
brew services start mysql   # macOS Homebrew
```

**"Port 8080 already in use"** — edit `start.sh` and change `PORT=8080` to `PORT=8888`.

**Blank page / 500 error** — open `config/app.php` and set `'debug' => true` to see the exact error.

**PHP extension missing** — the app needs `pdo_mysql`. Check with:
```bash
php -m | grep pdo_mysql
```
If missing, run `brew install php` to get the full Homebrew PHP build.
