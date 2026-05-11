# SKSU Isulan — Graduate-to-Alumni Integrated Tracking System

A complete web-based platform for **Sultan Kudarat State University – Isulan Campus**
that streamlines the entire graduate lifecycle from student graduation to alumni
tracer monitoring.

Built with **non-OOP procedural PHP, Tailwind CSS, JavaScript, and MySQL** in a
clean modular architecture.

---

## 🎯 Modules

| Module | Pages |
|---|---|
| **Authentication** | login, register, logout |
| **Student** | dashboard, profile, requirements, schedules, payments, announcements |
| **Registrar** | dashboard, verify students, validate requirements, payment review, graduates, masterlist (with CSV export) |
| **Alumni** | dashboard, profile, tracer survey, events, announcements |
| **Admin** | dashboard, users, students, alumni list, events, announcements, reports & analytics |

---

## 📁 Project Structure

```
alumni/
├── index.php                     # Landing page / role-based redirect
├── login.php                     # Login form
├── register.php                  # Student registration
├── logout.php                    # Sign out
├── setup.php                     # ONE-TIME database installer
│
├── includes/                     # Reusable PHP (configs, helpers)
│   ├── config.php                # DB credentials, paths, constants
│   ├── db.php                    # db_select / db_execute helpers
│   ├── auth.php                  # session + role-guarding + flash
│   └── helpers.php               # formatting, status badges, file uploads
│
├── templates/                    # Layout fragments
│   ├── header.php                # <head>, top navbar, flash messages
│   ├── sidebar.php               # role-aware navigation menu
│   └── footer.php                # closing tags, JS bundle
│
├── pages/                        # UI pages (organized by role)
│   ├── student/                  # 6 pages
│   ├── registrar/                # 6 pages
│   ├── alumni/                   # 5 pages
│   └── admin/                    # 8 pages
│
├── actions/                      # Form processors (POST/GET handlers)
│   ├── student_*.php             # 4 student actions
│   ├── registrar_*.php           # 4 registrar actions
│   ├── alumni_*.php              # 3 alumni actions
│   ├── admin_*.php               # 6 admin actions
│   ├── export_*.php              # 4 CSV exports
│   └── receipt.php               # printable digital receipt
│
├── assets/
│   ├── css/app.css               # custom styles atop Tailwind
│   ├── js/app.js                 # confirm dialogs, mobile sidebar
│   └── uploads/                  # student requirement uploads
│
└── database/
    └── schema.sql                # 11-table MySQL schema + seed data
```

---

## ⚙️ Requirements

- XAMPP / MAMP / LAMP / WAMP (PHP 7.4+ and MySQL 5.7+)
- A modern browser
- No Composer, no npm — Tailwind loads via CDN

---

## 🚀 Setup (XAMPP, Windows/Mac/Linux)

1. Place this folder under `htdocs/` (e.g. `C:\xampp\htdocs\alumni`).
2. Start **Apache** and **MySQL** from XAMPP control panel.
3. Open: **http://localhost/alumni/setup.php**
   This will:
   - Drop & create database `sksu_alumni`
   - Apply the 11-table schema with seed data
   - Insert default admin & registrar accounts
4. Visit **http://localhost/alumni/** — you'll be redirected to login.

### Default Credentials

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sksu.edu.ph` | `Admin@123` |
| Registrar | `registrar@sksu.edu.ph` | `Registrar@123` |
| Student | *(register via the form)* | — |
| Alumni | *(auto-created on approval)* | inherits from student |

> 💡 Delete `setup.php` after first run for production use.

---

## 🧪 Feature Test Procedure (browser-based)

### TEST 1 — Student Registration

1. Go to `http://localhost/alumni/register.php`
2. Fill the form (use `juan@test.com` / `student@12345`)
3. **Expected:** redirected to login with "Account created" success.

### TEST 2 — Student Submits Requirements & Payment

1. Login as the student you just created.
2. Sidebar → **Requirements** → upload a PDF (e.g. clearance form).
3. Sidebar → **Payments** → record `Yearbook Fee = 500.00`.
4. Sidebar → **Schedules** → book a Photobooth slot.
5. **Expected:** Each item appears in the table; payment status starts as pending until registrar review.

### TEST 3 — Schedule Conflict Prevention

1. As the same or another student, try booking the **same Photobooth date+time**.
2. **Expected:** Red flash message: "That slot is already booked."

### TEST 4 — Registrar Validation & Approval

1. Logout. Login as **registrar@sksu.edu.ph**.
2. Sidebar → **Payments** → approve or disapprove the submitted payment.
3. Sidebar → **Verify Students** → click *Approve* on Juan's row.
4. **Expected:** Confirmation, Graduate ID issued, alumni account auto-created, user role flipped to `alumni`.
5. Sidebar → **Requirements** → click ✓ to approve the uploaded file.
6. Sidebar → **Graduates** → confirm the new Graduate ID is listed.
7. Sidebar → **Masterlist** → filter & **Export CSV** to download the list.

### TEST 5 — Alumni Tracer + Event Registration

1. Logout. Login again as `juan@test.com` (now an alumni).
2. **Profile** → set Employment to *Employed*, fill in company info.
3. **Tracer Survey** → submit a Q1 2026 report.
4. **Events** → click *Register* on "Alumni Homecoming 2026".
5. **Expected:** Each action persists; "✓ Registered" badge appears.

### TEST 6 — Admin Reports & Analytics

1. Logout. Login as **admin@sksu.edu.ph**.
2. Sidebar → **Reports**.
3. **Expected:** Live dashboards show:
   - Total graduates, alumni count, employment rate %
   - Employment distribution (animated bars)
   - Per-course employment table
   - Per-academic-year graduate counts
   - Payment summary by approved type
4. Click **Export Alumni CSV** → downloads `alumni_export_<date>.csv`.

### TEST 7 — Admin User Management

1. Sidebar → **Users**.
2. Filter by role, search, then **+ New User** to create a registrar.
3. Toggle status to *inactive*; verify the user can no longer log in.

### TEST 8 — Receipt Generation

1. As a registrar, approve a pending payment.
2. As the student, go to **Payments** → click the *Receipt* link on the approved row.
3. **Expected:** A printable receipt page opens with reference number, amount, payer info.
4. Click **Print** to verify clean print view.

---

## 🧪 CLI Test Procedure (smoke test)

Run from the project directory:

```bash
php -r "require 'includes/auth.php'; var_dump(authenticate('admin@sksu.edu.ph','Admin@123') !== false);"
```

**Expected:** `bool(true)` — confirms DB + bcrypt + session bootstrap work.

Run the schema test directly:

```bash
mysql -u root sksu_alumni -e "SHOW TABLES;"
```

**Expected:** 11 tables — `users, students, requirements, schedules, payments, graduates, alumni, tracer_reports, events, event_registrations, announcements`.

---

## 🔒 Security Notes

- **Passwords** stored with `password_hash()` (bcrypt).
- **Prepared statements** for every query — no string-concat SQL.
- **Output escaping** (`htmlspecialchars` via `e()`) on every dynamic value.
- **Role-based access** enforced server-side with `require_role()` on every page.
- **CSRF tokens** issued and checked on profile/upload/booking forms.
- **File uploads** restricted by extension + size (10 MB max) + randomized filenames.
- **`.htaccess`** denies direct access to `includes/` and `*.sql` files.

---

## 🎨 Design

- **Theme:** Crimson Red (#991b1b) and White
- **Tailwind CSS** loaded via CDN with custom `crimson` palette extension
- **Mobile-first** responsive layout (collapsible sidebar on phones)
- **Accessible forms** — labels, focus rings, semantic HTML

---

## 🗑️ Clean Removal of a Feature

Each module is fully isolated. To remove (e.g.) the Events module:

1. Delete `pages/{alumni,admin}/events.php` and `pages/admin/event_*.php`.
2. Delete `actions/admin_*_event.php` and `actions/alumni_join_event.php`.
3. Drop tables `events` and `event_registrations` in MySQL.
4. Remove the *Events* nav entry from `templates/sidebar.php`.
5. Remove any links from dashboards.

Because the architecture is procedural and modular, no cross-module coupling needs unwinding.

---

## 👥 Roles & Their Capabilities

| Capability | Student | Registrar | Alumni | Admin |
|---|:-:|:-:|:-:|:-:|
| Register & login | ✅ | ✅ | ✅ | ✅ |
| Upload requirements | ✅ | — | — | — |
| Book schedules | ✅ | — | — | — |
| Submit payment for review / digital receipt | ✅ | — | — | — |
| Approve or disapprove payments | — | ✅ | — | — |
| Verify students & validate documents | — | ✅ | — | ✅ |
| Generate Graduate ID | (auto) | ✅ | — | ✅ |
| Generate masterlist + CSV | — | ✅ | — | ✅ |
| Update alumni profile | — | — | ✅ | — |
| Submit tracer survey | — | — | ✅ | — |
| Join alumni events | — | — | ✅ | — |
| Manage users | — | — | — | ✅ |
| Post announcements / events | — | — | — | ✅ |
| Reports & analytics | — | — | — | ✅ |
| CSV exports (masterlist / payments) | — | ✅ | — | — |
| CSV exports (alumni / tracer) | — | — | — | ✅ |

---

© 2026 Sultan Kudarat State University — Isulan Campus
