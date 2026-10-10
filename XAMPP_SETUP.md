# Running the PHP Backend on XAMPP

The backend has been migrated from **Python Flask + SQLAlchemy** to **plain PHP + PDO**,
while keeping the **exact same SQLite database** (`instance/voting.db`).

- Python entry point (legacy): `app.py` → `http://127.0.0.1:5000`
- PHP entry point (new): `index.php` → served by Apache in XAMPP

---

## 1. Requirements

| Component | Version |
| :--- | :--- |
| XAMPP | 8.1+ (Apache + PHP 8.1 or newer) |
| PHP extension | `pdo_sqlite` (enabled by default in XAMPP) |
| PHP extension | `gd` (used for citizenship-photo validation) — enabled by default |
| Apache module | `mod_rewrite` (needed for the clean URLs) — enabled by default |

Check from a terminal:

```bash
C:/xampp/php/php.exe -m | findstr /I "pdo_sqlite gd"
```

---

## 2. Deploy the project

Copy the whole project folder into XAMPP's web root, e.g.:

```
C:\xampp\htdocs\voting\
```

### Option A — Sub-folder install (simplest)

1. Start **Apache** from the XAMPP Control Panel.
2. Open **http://localhost/voting/**

`index.php` auto-detects the `/voting` base path, so every link and asset still resolves.

> Make sure `C:\xampp\apache\conf\httpd.conf` contains `<Directory "C:/xampp/htdocs"> ... AllowOverride All`.
> XAMPP ships with this enabled.

### Option B — Virtual host (clean root URLs)

Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName voting.local
    DocumentRoot "C:/xampp/htdocs/voting"
    <Directory "C:/xampp/htdocs/voting">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then add `127.0.0.1  voting.local` to `C:\Windows\System32\drivers\etc\hosts`
and open **http://voting.local/**. phpMyAdmin remains at **http://localhost/phpmyadmin**.

---

## 3. Database

**Nothing to import.** The application reuses the existing SQLite database:

```
instance/voting.db
```

- Connection + schema creation: `includes/db.php`
- If the file is missing, the schema is created automatically and demo data is
  seeded by `includes/seed.php` (mirrors the old `init_db()` in `app.py`).
- An existing `voting.db` is **never overwritten or re-seeded**.
- To point the app at a different database file (tests, staging), set the
  `VOTING_DB_PATH` environment variable to an absolute path before Apache/PHP
  starts, e.g. `VOTING_DB_PATH=C:/path/to/other.db`.

### ⚠️ About phpMyAdmin

phpMyAdmin only manages **MySQL / MariaDB** databases — it **cannot open or browse
SQLite files**. Because this project must keep using SQLite, phpMyAdmin will not
show the `voting.db` data.

To inspect the SQLite database, use one of these instead:

| Tool | Notes |
| :--- | :--- |
| **DB Browser for SQLite** | Free desktop GUI, opens `instance/voting.db` directly |
| **VS Code + "SQLite Viewer"** extension | Click the `.db` file in the editor |
| `php -r "..."` | Quick CLI queries with PDO |

If phpMyAdmin is a hard requirement for your report/viva, the database can be
switched to MySQL/MariaDB instead (create `voting` db in phpMyAdmin, update
`includes/db.php` DSN) — ask and this can be added as an optional mode.

---

## 4. Demo credentials

| Role | Username | Password |
| :--- | :--- | :--- |
| Voter | `ranjeet` | `password123` |
| Voter | `bikash`, `pooja`, `binod`, `sitamaya` | `password123` |
| Administrator | `admin` | `admin123` |

> The demo accounts were originally hashed with Werkzeug's **scrypt** algorithm,
> which PHP cannot verify natively. On the first successful login of a demo
> account, `includes/auth.php` transparently re-hashes the password with PHP's
> `password_hash()`. Accounts registered through the old Flask app with unknown
> passwords (`ranjit`, `amit`) must be re-registered or have their password reset.

---

## 5. Project layout

```
index.php                  Front controller / router (all Flask routes)
.htaccess                  mod_rewrite rules + DB file protection
includes/
  db.php                   PDO SQLite connection + schema
  models.php               Query + mutation layer (replaces SQLAlchemy models)
  auth.php                 Sessions, login, password hashing
  helpers.php              Flash messages, url_for(), citizenship verification engine
  seed.php                 Demo data seeder (replaces init_db())
  view.php                 View + layout rendering
views/                     PHP templates (ported from templates/*.html)
  layouts/                 base.php, voter.php, admin.php
  voter/, admin/, candidates/, elections/, results/
static/                    CSS, JS, images, uploads (unchanged)
instance/voting.db         SQLite database (unchanged)
scratch/                   Development-only migration and inspection scripts
```

---

## 6. Route parity with the Flask app

All original routes are preserved:

`/`, `/about`, `/login`, `/register`, `/logout`, `/dashboard`, `/my-elections`,
`/elections`, `/elections/{id}`, `/elections/{id}/vote`, `/elections/{id}/confirm`,
`/elections/{id}/cast-vote` (POST), `/vote-success`, `/voting-status`, `/profile`,
`/candidates`, `/candidates/{id}`, `/results`, `/results/{id}`,
`/historical-elections`, `/admin/login`, `/admin/dashboard`, `/admin/elections`,
`/admin/elections/create` (POST), `/admin/elections/{id}/status` (POST),
`/admin/candidates`, `/admin/candidates/create` (POST),
`/admin/candidates/{id}/delete` (POST), `/admin/voters`,
`/admin/voters/{id}/toggle` (POST), `/admin/voters/{id}/update-details` (POST),
`/admin/voters/{id}/reupload-certificate` (POST), `/admin/results`, `/admin/reports`.

---

## 7. Troubleshooting

| Symptom | Fix |
| :--- | :--- |
| `/login` returns Apache 404 | Enable `mod_rewrite` in `httpd.conf` and set `AllowOverride All` for the project directory |
| CSS/JS missing | Confirm `static/` was copied; check the URL base path |
| `unable to open database file` | Give the process write permission to `instance/` |
| Uploads fail | Give write permission to `static/uploads/citizenship/` |
| Blank page | Check `C:\xampp\apache\logs\error.log` |
