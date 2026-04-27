# Q400 Aircraft Systems Study App

A professional, local-first web application for studying Dash 8 Q400 aircraft systems. Built in PHP 8.x + MySQL with a clean MVC architecture.

---

## Quick Start (5 minutes)

### Prerequisites

- **PHP 8.1+** with PDO and pdo_mysql extensions enabled
- **MySQL 8.0+** (or MariaDB 10.4+)
- **Apache** with `mod_rewrite` enabled (or Nginx equivalent)
- **XAMPP / WAMP / Laragon** on Windows work perfectly

---

## Step-by-Step Setup

### 1. Place the App

Copy the entire `q400-study/` folder into your web server document root.

**XAMPP example:**
```
C:\xampp\htdocs\q400-study\
```

**macOS/Linux Apache example:**
```
/var/www/html/q400-study/
```

### 2. Create the MySQL Database

Open **phpMyAdmin** (or a MySQL terminal) and run:

```sql
CREATE DATABASE q400_study CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then import the schema and seed data:

```bash
# Via terminal:
mysql -u root q400_study < database/schema.sql
mysql -u root q400_study < database/seed_data.sql

# Or via phpMyAdmin:
# Select the q400_study database → Import → choose database/schema.sql → Go
# Then Import again → choose database/seed_data.sql → Go
```

### 3. Configure Database Credentials

Edit `config/database.php` if your MySQL setup differs from defaults:

```php
'username' => 'root',    // Your MySQL username
'password' => '',        // Your MySQL password (empty for XAMPP default)
'database' => 'q400_study',
'host'     => 'localhost',
```

Alternatively, set environment variables: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.

### 4. Enable Apache mod_rewrite

Make sure `.htaccess` is respected. In XAMPP:
- Open `httpd.conf` and ensure `AllowOverride All` is set for your htdocs directory
- Make sure `mod_rewrite` is enabled (uncomment `LoadModule rewrite_module`)

### 5. Open the App

Navigate to:
```
http://localhost/q400-study/public/
```

You should see the login page.

---

## Default Login Accounts

| Role    | Email                        | Password   |
|---------|------------------------------|------------|
| Admin   | admin@q400study.local        | password   |
| Learner | samuel@q400study.local       | password   |
| Learner | alice@q400study.local        | password   |

> The password hash in the seed data corresponds to `password`.

---

## App Structure

```
q400-study/
├── public/                   ← Document root (point Apache here)
│   ├── index.php             ← Front controller (all requests go here)
│   ├── .htaccess             ← URL rewriting rules
│   └── assets/
│       ├── css/
│       │   ├── app.css       ← Main stylesheet (dark aviation theme)
│       │   └── auth.css      ← Login/register styles
│       ├── js/
│       │   ├── app.js        ← Core JavaScript (toast, theme, global)
│       │   ├── diagram.js    ← Interactive diagram engine
│       │   ├── flashcard.js  ← Flashcard flip + spaced repetition
│       │   └── quiz.js       ← Quiz timer + navigation
│       └── uploads/          ← User-uploaded files (PDFs, images)
│
├── app/
│   ├── Core/                 ← Framework core (Router, DB, Auth, CSRF, View)
│   ├── Controllers/          ← All route handlers
│   └── Models/               ← Data models
│
├── views/                    ← PHP templates (no HTML boilerplate – injected by layout)
│   ├── layouts/app.php       ← Main layout (sidebar + topbar)
│   ├── auth/                 ← Login, register
│   ├── dashboard/            ← Home dashboard
│   ├── systems/              ← Systems library + detail pages
│   ├── study/                ← Detailed study + quick revision
│   ├── flashcards/           ← Flashcard browser + study session
│   ├── quiz/                 ← Quiz list + take + results
│   ├── progress/             ← Progress analytics
│   ├── planner/              ← Study planner
│   ├── search/               ← Full-text search
│   ├── diagrams/             ← Interactive system diagrams
│   ├── admin/                ← Content management
│   └── errors/               ← 404 page
│
├── routes/web.php            ← All URL route definitions
│
├── config/
│   ├── app.php               ← App settings
│   └── database.php          ← DB credentials
│
└── database/
    ├── schema.sql            ← Full database schema (22 tables)
    └── seed_data.sql         ← Sample users, systems, flashcards, quizzes
```

---

## Features

| Feature | Status |
|---------|--------|
| User login / register / logout | ✅ Complete |
| Learner dashboard with stats | ✅ Complete |
| Systems library (all 22 Q400 systems) | ✅ Complete |
| Detailed study mode | ✅ Complete |
| Quick revision mode (3/5/10 min) | ✅ Complete |
| Flashcards with spaced repetition (SM2) | ✅ Complete |
| Quiz engine (MCQ, true/false, timed) | ✅ Complete |
| Quiz results with explanations | ✅ Complete |
| Progress tracking & analytics | ✅ Complete |
| Study planner | ✅ Complete |
| Full-text search | ✅ Complete |
| Interactive diagram engine | ✅ Complete |
| Electrical system SVG diagram | ✅ Complete |
| Admin content manager | ✅ Complete |
| Admin flashcard editor | ✅ Complete |
| Admin quiz builder | ✅ Complete |
| PDF / image import tool | ✅ Complete |
| Roles: admin + learner | ✅ Complete |
| CSRF protection on all forms | ✅ Complete |
| SQL injection prevention (PDO prepared statements) | ✅ Complete |

---

## Q400 Systems Included

1. Electrical Power (ATA 24)
2. Hydraulic Power (ATA 29)
3. Fuel (ATA 28)
4. Powerplant – PT6A engines (ATA 71)
5. Propeller (ATA 61)
6. Flight Controls (ATA 27)
7. Landing Gear (ATA 32)
8. Air Conditioning & Pressurization (ATA 21)
9. Pneumatics (ATA 36)
10. Ice & Rain Protection (ATA 30)
11. Fire Protection (ATA 26)
12. Autoflight (ATA 22)
13. Navigation (ATA 34)
14. Communications (ATA 23)
15. Indicating & Recording – EFIS/EICAS (ATA 31)
16. Oxygen (ATA 35)
17. Lighting (ATA 33)
18. Aeroplane General
19. FMS
20. Caution & Warning
21. DU Messages
22. Quick Reference Handbook (QRH)

---

## Troubleshooting

**"Database connection failed"**
- Check credentials in `config/database.php`
- Ensure MySQL is running (check XAMPP control panel)
- Ensure the `q400_study` database exists

**"404 Not Found" on all pages**
- Ensure `mod_rewrite` is enabled in Apache
- Ensure `.htaccess` in `public/` is being read (`AllowOverride All`)

**"Session not working" / keeps logging out**
- Check PHP session directory is writable
- Try a different browser / clear cookies

**Blank white page**
- Enable PHP error display: set `display_errors = On` in `php.ini`
- Or check `config/app.php` → `'debug' => true`

**Login shows "Invalid email or password"**
- Make sure `seed_data.sql` was imported successfully
- The test password is `password` for all seed accounts

---

## Adding Content

### Via Admin Panel (recommended)
1. Login as `admin@q400study.local` / `password`
2. Go to **Content Manager** in the sidebar
3. Create lessons, flashcards, and quizzes from the UI

### Via Database (for bulk imports)
Use `phpMyAdmin` to insert directly into:
- `lessons` + `lesson_sections` for study content
- `flashcards` for review cards
- `quiz_questions` for test questions

---

## Security Notes

- Change `encryption_key` in `config/app.php` before deploying online
- All SQL uses PDO prepared statements – no raw query injection risk
- CSRF tokens protect all POST forms
- Passwords hashed with `PASSWORD_BCRYPT`

---

## Tech Stack

- **Backend**: PHP 8.1+ (no framework, hand-rolled MVC)
- **Database**: MySQL 8.0 / MariaDB 10.4+
- **Frontend**: Vanilla JS + CSS (no build step required)
- **Icons**: Lucide (CDN)
- **Charts**: Chart.js (CDN)
- **Diagrams**: SVG with DiagramEngine (custom vanilla JS)

---

*Q400 Aircraft Systems Study App — For training and educational purposes only.*
