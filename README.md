# Contact Manager (custom PHP MVC)

A small contact manager built with a **hand-written MVC structure** (no frameworks). It supports full CRUD on contacts and a single admin login; only logged-in users can add, edit or delete.

## Features

- Contacts table: `id, name, email, phone, created_at` with list / add / edit / delete
- Front controller (`index.php`) with a whitelist router
- Models, views and controllers in separate folders
- One admin user; password stored as a `password_hash()` hash
- Public read-only list; all changes require login

## Requirements

- PHP 8.1 or newer with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache (with `mod_rewrite`) or PHP's built-in server for local testing

## Setup

```bash
# 1. Create the database and tables (also inserts 3 sample contacts)
mysql -u root -p < database/schema.sql

# 2. (Recommended) create a limited DB user - see the commented block at the bottom of database/schema.sql

# 3. Configure the app
cp config/config.example.php config/config.php
#    then edit config/config.php with your database user / password

# 4. Create the admin user (password is typed at a prompt, min 10 characters)
php scripts/create_admin.php admin

# 5. Run it locally
php -S 127.0.0.1:8000
#    open http://127.0.0.1:8000/index.php
```

No default password exists. The admin account is only created by step 4, so there are no shipped credentials.

## Project structure

```
index.php              Front controller + route whitelist
core/                  Framework-free plumbing: bootstrap, Database (PDO), View, Auth, Csrf, Flash, Config, helpers
models/                Contact.php, User.php  (all SQL lives here, prepared statements only)
controllers/           Controller.php (base), ContactController.php, AuthController.php
views/                 layout.php, contacts/, auth/, errors/
assets/style.css       Styling (no inline CSS or JavaScript, so a strict CSP works)
config/                config.example.php (config.php is git-ignored)
database/schema.sql    Tables + sample data
scripts/create_admin.php   CLI-only admin creation
```

### Routes

| Method | Route (`index.php?r=`) | Login required | Purpose |
|---|---|---|---|
| GET | `contacts` (default) | no | List contacts |
| GET | `contacts/create` | yes | Add form |
| POST | `contacts/store` | yes + CSRF | Save new contact |
| GET | `contacts/edit&id=N` | yes | Edit form |
| POST | `contacts/update` | yes + CSRF | Save changes |
| GET | `contacts/delete&id=N` | yes | Delete confirmation page |
| POST | `contacts/delete` | yes + CSRF | Delete contact |
| GET / POST | `login` | no | Login form / authenticate |
| POST | `logout` | CSRF | Log out |

Any other method/route combination returns 404.

## Security list

| # | Measure | Where |
|---|---|---|
| 1 | **Prepared statements** with bound parameters for every query; PDO emulation off so the driver binds values; no user input is concatenated into SQL | `models/*.php`, `core/Database.php` |
| 2 | **Output escaping**: every dynamic value is printed through `e()` (`htmlspecialchars`, `ENT_QUOTES`, UTF-8) to prevent XSS | `core/helpers.php`, `views/**` |
| 3 | **Password hashing** with `password_hash()` / `password_verify()` (PHP default, currently bcrypt); no plain-text passwords anywhere; hash upgraded automatically when needed | `models/User.php`, `AuthController` |
| 4 | **Server-side validation** of name (length, valid UTF-8, no control characters), email (`FILTER_VALIDATE_EMAIL`, max 255) and phone (allowed characters, 5-15 digits); IDs validated as positive integers | `Contact::validate()`, `Controller::intParam()` |
| 5 | **CSRF tokens** (random 256-bit, compared with `hash_equals`) required on every POST, including login and logout | `core/Csrf.php`, `Controller::requireCsrf()` |
| 6 | **Access control**: add / edit / delete / confirm-delete all call `requireAuth()`; state-changing actions are POST-only | `ContactController`, `index.php` |
| 7 | **Session hardening**: `HttpOnly`, `SameSite=Lax`, `Secure` on HTTPS, strict mode, cookies only, ID regenerated on login and logout, 30-minute idle timeout | `core/bootstrap.php`, `core/Auth.php` |
| 8 | **Brute-force protection**: account locks for 15 minutes after 5 failed logins | `User::registerFailure()`, `AuthController` |
| 9 | **No username enumeration**: same generic error for unknown user and wrong password, with a dummy hash check to equalise timing | `AuthController` |
| 10 | **Route whitelist**: only listed method + route pairs are dispatched; no dynamic class or file names from user input; redirects use fixed internal routes only | `index.php`, `Controller::redirect()` |
| 11 | **Safe error handling**: `display_errors` off, exceptions logged server-side, visitors see a generic 500 page | `core/bootstrap.php`, `index.php` |
| 12 | **Security headers**: Content-Security-Policy (`default-src 'self'`, no inline scripts), `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: no-referrer`, `Cache-Control: no-store` | `core/bootstrap.php` |
| 13 | **Secrets out of Git**: `config/config.php` is git-ignored; only `config.example.php` is committed | `.gitignore` |
| 14 | **Least privilege DB account** (SELECT/INSERT/UPDATE/DELETE only) documented in the schema | `database/schema.sql` |
| 15 | **Direct access blocked** to code and SQL folders (Apache) and admin script is CLI-only | `.htaccess`, `scripts/create_admin.php` |

### Manual checks performed

Tested against a running copy of the app (using SQLite for the quick local check; the MySQL schema is in `database/schema.sql`): invalid and unknown routes return 404; POST without a CSRF token returns 403; anonymous access to add/edit/delete redirects to login; a SQL-injection string saved as a name is stored literally and the table survives; HTML in a name is shown escaped; lockout triggers after repeated failures (even with the correct password); logout ends access.

### Known limitations (honest list)

- Login lockout is per account, so an attacker can deliberately lock the admin out for 15 minutes. A per-IP limit or CAPTCHA would be a next step.
- No HTTPS is configured by this project; deploy behind HTTPS in production (the session cookie becomes `Secure` automatically).
- Contacts have no unique-email rule and no pagination or search.
- Only one admin account, created from the command line; there is no password-change screen.
- The sample data and local test setup are for demonstration only.

## Version control

Work is committed in small steps (see `git log`). `config/config.php` is never committed.

## Integrity statement

> Fill in the bracketed parts yourself before submitting - this section must reflect what you actually did.

**Student:** [full name] **ID:** [student ID] **Course/Unit:** [course code]

I confirm that this submission is my own responsibility and that I understand the code I am submitting.

**AI assistance disclosure:** The initial code, SQL script and README of this project were generated with the help of an AI assistant (Claude, by Anthropic) in a session on 18 November 2026. I [reviewed / ran / modified] the result myself; changes I made after that are visible in the Git history. [Describe in one or two sentences what you personally checked or changed.]

**Sources consulted:**

- PHP Manual - PDO and prepared statements: https://www.php.net/manual/en/pdo.prepared-statements.php
- PHP Manual - `password_hash()`: https://www.php.net/manual/en/function.password-hash.php
- PHP Manual - `htmlspecialchars()`: https://www.php.net/manual/en/function.htmlspecialchars.php
- PHP Manual - session configuration: https://www.php.net/manual/en/session.security.php
- OWASP SQL Injection Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html
- OWASP Password Storage Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html
- OWASP CSRF Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
- OWASP Session Management Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html
- [Add any lecture notes, tutorials or other sources you actually used]

**Week-5 progress:** [Describe what you had working by week 5 and link the commit(s) that show it, e.g. `git log --oneline` hash and date.]

**Signature:** [your name] **Date:** [date]
