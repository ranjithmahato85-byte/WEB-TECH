# Nepal Online Voting System (नेपाल अनलाइन मतदान प्रणाली)

> **Academic Project** — Bachelor of Computer Applications (BCA)  
> Backend: **PHP + PDO (XAMPP/Apache)** — migrated from Python Flask + SQLAlchemy.  
> Database: **SQLite** (`instance/voting.db`) — unchanged by the migration.

---

## 🏔️ Project Overview

The **Nepal Online Voting System** is a realistic, multi-page, state-based electronic balloting and election administration platform. It preserves the authentic visual identity of Nepal—including national colors (Crimson Red `#dc2626`, Royal Navy `#003893`), snow-capped Himalayan themes, pagoda architecture motifs, and national party symbols—while cleanly decomposing the application into dedicated, modular pages and workflows.

Unlike single-page mockups, this system strictly enforces **Constitutional Ballot Secrecy (Article 84 of the Constitution of Nepal)**, prevents double voting at the database level, and provides separate authenticated spaces for **Citizens/Voters** and **Election Commission Administrators**.

---

## 🚀 How to Run the Application

The backend is now **PHP** and runs on **XAMPP (Apache)**.

```bash
# 1. Copy the project into XAMPP's web root
#    -> C:\xampp\htdocs\voting

# 2. Start "Apache" from the XAMPP Control Panel

# 3. Open the app
#    http://localhost/voting/
```

No database import is needed — the app reuses `instance/voting.db` as-is.

> Full setup, virtual-host configuration, the phpMyAdmin/SQLite caveat and a
> troubleshooting table are documented in **[XAMPP_SETUP.md](XAMPP_SETUP.md)**.

### Demo Login Credentials for Viva Presentation:
- **Verified Voter:**
  - Username: `ranjeet`
  - Password: `password123`
  - Voter ID: `NV-2026-8891` (Bagmati Province, Ward 10)
- **Election Commission Administrator:**
  - Username: `admin`
  - Password: `admin123`

---

## 🗺️ Complete Route & Page Directory

The route table below documents the original Flask routes. Every one of them is
preserved by the PHP front controller (`index.php`) and renders the ported PHP
views in `views/`.

| Section | Route | Template | Description |
| :--- | :--- | :--- | :--- |
| **Public** | `/` | `views/index.php` | Clean civic homepage with hero, features, how it works, and upcoming elections preview |
| **Public** | `/about` | `views/about.php` | Technical architecture, ballot secrecy guarantee, and BCA project details |
| **Public** | `/candidates` | `views/candidates/index.php` | Public candidate directory with live search and party filtering |
| **Public** | `/candidates/<id>` | `views/candidates/detail.php` | Candidate profile with academic demo disclaimer and party symbols |
| **Public** | `/results` | `views/results/index.php` | Directory of completed and certified elections |
| **Public** | `/results/<id>` | `views/results/detail.php` | Audited election results with turnout metrics, ranking table, and donut chart |
| **Public** | `/historical-elections` | `views/` | Read-only archive of the 2079 BS (2022) House of Representatives election |
| **Auth** | `/login` | `views/login.php` | Dedicated split login page with Nepal artwork on left and login form on right |
| **Auth** | `/register` | `views/register.php` | Dedicated two-column registration page with province, district, and ward fields |
| **Auth** | `/logout` | *Redirects* | Clears active session securely |
| **Voter** | `/dashboard` | `views/voter/dashboard.php` | Voter dashboard with dark navy sidebar, stat cards, upcoming and available contests |
| **Voter** | `/my-elections` | `templates/voter/my_elections.html` | Tabbed election browser (All, Active, Upcoming, Completed) with action buttons |
| **Voter** | `/elections/<id>` | `templates/voter/election_detail.html` | Full contest details, dates, constituency, and nominated candidate cards |
| **Voting Flow** | `/elections/<id>/vote` | `templates/voter/vote.html` | Dedicated ballot screen with candidate selection radio cards and symbols |
| **Voting Flow** | `/elections/<id>/confirm`| `templates/voter/vote_confirm.html` | Confirmation screen with selected candidate recap and irreversible warning |
| **Voting Flow** | `/elections/<id>/cast-vote`| *POST Backend* | Decouples ballot choice, stores anonymous vote, and issues confirmation receipt |
| **Voting Flow** | `/vote-success` | `templates/voter/vote_success.html` | Success screen with checkmark, confetti, and confirmation code (`NV...`) |
| **Voter** | `/voting-status` | `templates/voter/voting_status.html` | Audit log verifying participation while strictly keeping candidate choice hidden |
| **Voter** | `/profile` | `templates/voter/profile.html` | Citizen registration record and constituency details |
| **Admin** | `/admin/login` | `templates/admin/login.html` | Dedicated Election Commission admin login |
| **Admin** | `/admin/dashboard` | `templates/admin/dashboard.html` | Admin oversight panel with 4 stat cards, voting activity graph, and recent events |
| **Admin** | `/admin/elections` | `templates/admin/elections.html` | Create, activate, deactivate, and close electoral contests |
| **Admin** | `/admin/candidates`| `templates/admin/candidates.html` | Add candidate profiles, upload symbols, and assign constituencies |
| **Admin** | `/admin/voters` | `templates/admin/voters.html` | Voter registry with search, verification, and deactivation controls (secrecy preserved) |
| **Admin** | `/admin/results` | `templates/admin/results.html` | Live tabulation telemetry across all contests |
| **Admin** | `/admin/reports` | `templates/admin/reports.html` | Cryptographic audit logs and participation receipts |

---

## 🔄 User Navigation & Workflow

### 1. Citizen / Voter Journey:
```
1. Public Homepage (/)
   ↓ (Click "Login" or "Vote Now")
2. Login Page (/login)
   ↓ (Authenticate as ranjeet / password123)
3. Voter Dashboard (/dashboard)
   ↓ (Click "My Elections" or "Cast Your Vote")
4. My Elections (/my-elections)
   ↓ (Click "Vote Now" on active Federal Parliamentary Election)
5. Voting Page (/elections/1/vote)
   ↓ (Select candidate card, click "Continue to Confirmation")
6. Vote Confirmation (/elections/1/confirm)
   ↓ (Review candidate, read warning, click "Confirm & Cast Vote")
7. Vote Success (/vote-success)
   ↓ (View Confirmation Number NV2026..., click "Go to Dashboard" or "Voting Status")
8. Voting Status (/voting-status)
   ↓ (See participation status: "✓ Voted" with receipt code; secret ballot preserved!)
```

### 2. Administrator Journey:
```
1. Admin Login (/admin/login)
   ↓ (Authenticate as admin / admin123)
2. Admin Dashboard (/admin/dashboard)
   ↓ (Review turnout graph, top 4 metrics, and recent events)
3. Manage Elections (/admin/elections)
   ↓ (Create new contests, toggle between Upcoming, Active, and Completed)
4. Manage Candidates (/admin/candidates)
   ↓ (Add candidate nominations with party symbols)
5. Manage Voters (/admin/voters)
   ↓ (Audit citizen registration status, verify/deactivate accounts without seeing their votes)
6. Results & Reports (/admin/results & /admin/reports)
   ↓ (Audit certified vote counts and cryptographic participation receipts)
```

---

## 🛡️ Key Technical Features for Viva Defense

1. **Ballot Secrecy Decoupling**:  
   Individual candidate selections are decoupled from voter identities. The `VoterParticipation` table records *who* voted and issues a receipt code, while `AnonymousBallot` stores *what* was voted without any foreign key linking back to the user.
2. **Double-Voting Prevention**:  
   A database unique constraint on `(user_id, election_id)` guarantees atomic idempotency.
3. **Federal Governance Integration**:  
   Supports Nepal's three tiers of governance: Federal Parliament, 7 Provincial Assemblies, and 753 Local Governments.
4. **Offline High Fidelity**:  
   All national symbols, avatars, flags, and Himalayan visuals are rendered in pure vector SVG, ensuring instant loading without external CDN dependencies.

---

## 🐘 PHP Migration Notes

| Flask | PHP equivalent |
| :--- | :--- |
| `app.py` routes | `index.php` (front controller + `.htaccess` rewrite) |
| SQLAlchemy models | `includes/models.php` (PDO prepared statements) |
| `db.create_all()` | `includes/db.php` → `init_db_schema()` |
| `init_db()` seed data | `includes/seed.php` → `ensure_demo_data()` |
| Flask sessions + `flash()` | `includes/auth.php` + `flash()` in `includes/helpers.php` |
| Jinja2 templates | `views/*.php` + `views/layouts/*.php` |
| `url_for()` / context processors | `url_for()`, `render_view()` in `includes/` |
| Werkzeug password hashing | `password_hash()` / `password_verify()` (BCRYPT) |

SQLite column names, table names and the seeded rows are identical, so the
same `voting.db` works with both backends.
