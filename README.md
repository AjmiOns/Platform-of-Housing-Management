# Dar Tunisie — Real Estate Management Platform

A full-stack PHP/MySQL platform for a real estate agency operating in Tunisia, covering the public listing site, a client area (accounts, favorites, visit requests) and an admin back-office (property CRUD, visit/message management, agency settings).

Built as an academic project and hardened toward production-grade practices: input validation, CSRF protection, upload security, rate limiting, automated tests, and a documented API.

---

## Table of contents

- [Features](#features)
- [Architecture](#architecture)
- [Tech stack](#tech-stack)
- [Security](#security)
- [Getting started](#getting-started)
- [Configuration](#configuration)
- [Running tests](#running-tests)
- [Project structure](#project-structure)
- [API reference](#api-reference)
- [Known limitations](#known-limitations)
- [Roadmap](#roadmap)

---

## Features

**Public site**
- Property listing with combined filters (type, governorate, city, budget, keyword), sorting (price, date, area) and pagination — server-rendered by default, progressively enhanced with AJAX (no page reload, browser back/forward supported)
- Property detail page with a visit-request form, gated by real-time availability (`is_property_bookable()`)
- Contact form with server-side validation

**Client area**
- Registration / login, session-based auth, rate-limited against brute force
- Favorites (add/remove via AJAX, optimistic UI)
- Visit request history
- Profile management (info + password change)

**Admin back-office**
- Dashboard with KPIs and charts (properties by status/category/governorate, monthly trend)
- Property CRUD with image upload
- Visit request management (status workflow: new → confirmed/cancelled/done), triggers a confirmation email to the client
- Contact message inbox
- Agency settings (branding, contact info, map embed)

**Cross-cutting**
- Transactional emails (visit confirmation, new-message notification) via PHPMailer, SMTP-configurable, fails gracefully if unconfigured
- REST JSON API for property search (used by the AJAX filters, reusable for a future mobile client)

---

## Architecture

```
Browser
  │
  ├── Public pages (index, properties, property-details, contact)
  │      → render server-side on first load
  │      → progressively enhanced with fetch() against api/properties.php
  │
  ├── Client area (user/*)          ── session-based auth (includes/user_auth.php)
  ├── Admin back-office (admin/*)   ── session-based auth (includes/auth.php)
  │
  └── api/properties.php            ── stateless JSON endpoint
             │
             ▼
     PropertyRepository            ── Repository pattern: all property SQL lives here
             │
             ▼
        Database (Singleton)       ── one PDO connection per request
             │
             ▼
           MySQL
```

**Design decisions, and why:**

| Decision | Rationale |
|---|---|
| `Database` as a Singleton | One PDO connection per request lifecycle; avoids passing `$pdo` through every function signature while keeping a single, testable access point (`Database::getInstance()`). |
| `PropertyRepository` as a Repository | Isolates SQL from presentation. `properties.php`, `api/properties.php` and the admin CRUD pages all share the same query logic (filters, sorting, pagination) — one place to fix a bug or add an index hint. |
| Business rules extracted as pure functions | `validate_client_registration()`, `is_property_bookable()`, `rate_limit_check()` take plain arrays/scalars in, return plain arrays/booleans out — no session, no DB. That's what makes them unit-testable in milliseconds without a database fixture (see [Running tests](#running-tests)). |
| AJAX with server-rendered fallback | Every enhanced page (`properties.php`, `favoris.php`, `property-details.php`) still works with JavaScript disabled — the filter form submits as a normal GET, the favorite/visit forms submit as a normal POST. Progressive enhancement, not a JS-only SPA. |
| `.env`-based configuration | Database and SMTP credentials never live in version control. `config/config.php` reads from `.env` via a small dependency-free loader (`config/env.php`), falling back to sane XAMPP defaults so the project still boots without one. |

---

## Tech stack

- **Backend:** PHP 8.1+, PDO (prepared statements throughout)
- **Database:** MySQL / MariaDB
- **Frontend:** Server-rendered PHP views, Bootstrap 5, vanilla JS (`fetch`, no framework)
- **Email:** PHPMailer (SMTP)
- **Testing:** PHPUnit 10
- **Dependency management:** Composer

No frontend build step, no framework lock-in — deliberate, given the deployment target (shared/XAMPP-style hosting typical for a small agency).

---

## Security

- **Auth:** passwords hashed with `password_hash()` (bcrypt, cost 12), verified with `password_verify()`. Two separate auth domains (`users` = admin, `clients` = public), never conflated.
- **CSRF:** token required and verified on every state-changing form (`verify_csrf()`).
- **XSS:** all output escaped through `h()` (`htmlspecialchars`); no raw `$_POST`/`$_GET` echoed into HTML.
- **SQL injection:** 100% prepared statements; the one place a "raw" value is used (the `sort` API parameter) is resolved through a whitelist array (`PropertyRepository::SORT_OPTIONS`), never interpolated.
- **File upload:** extension whitelist **and** real MIME-type check (`finfo_file` + `getimagesize`) — a script renamed to `.jpg` is rejected. `public/uploads/.htaccess` also disables script execution in that folder as defense in depth.
- **Brute-force mitigation:** session-scoped rate limiting on both login forms (5 failed attempts / 15 min). See [Known limitations](#known-limitations) for its scope.
- **Secrets:** database and SMTP credentials live in `.env` (git-ignored); `.env.example` documents every variable without exposing real values.

---

## Getting started

### Prerequisites
- PHP ≥ 8.1 with `pdo_mysql`, `mbstring`, `fileinfo` extensions
- MySQL / MariaDB
- [Composer](https://getcomposer.org/) (for PHPMailer and PHPUnit)
- XAMPP (or equivalent) for local development

### Installation

```bash
# 1. Clone or copy the project into htdocs
cp -r projet_js C:\xampp\htdocs\dar-tunisie

# 2. Install PHP dependencies
cd C:\xampp\htdocs\dar-tunisie
composer install

# 3. Configure environment
copy .env.example .env
# edit .env if your setup differs from the defaults (see Configuration below)

# 4. Create the database and import the schema
mysql -u root -e "CREATE DATABASE tunisie_logement CHARACTER SET utf8mb4;"
mysql -u root tunisie_logement < database/schema.sql

# 5. Start Apache + MySQL (via XAMPP Control Panel), then visit:
#    http://localhost/dar-tunisie/index.php
```

**Default admin account** (seeded by `schema.sql`):
```
Email:    admin@dar-tunisie.tn
Password: admin123
```
Change this before any real deployment.

---

## Configuration

All configuration lives in `.env` (copy from `.env.example`). Key variables:

| Variable | Purpose | Default |
|---|---|---|
| `APP_BASE` | URL path prefix, must match your folder name under `htdocs` | `/projet_js` |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | Database connection | XAMPP defaults (`root`, no password) |
| `MAIL_HOST` | SMTP host — **leave empty to disable emails entirely** | _(empty)_ |
| `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_PORT`, `MAIL_ENCRYPTION` | SMTP credentials | — |

To test email notifications without a real mailbox, create a free sandbox inbox at [mailtrap.io](https://mailtrap.io) and paste its SMTP credentials into `.env` — every email the app sends lands there instead of a real inbox.

---

## Running tests

```bash
composer install   # pulls in phpunit/phpunit as a dev dependency
composer test       # or: vendor/bin/phpunit
```

The suite (`tests/`) covers pure business-logic functions only — no database or session bootstrap required, so it runs in well under a second:

| File | Covers |
|---|---|
| `ClientRegistrationValidationTest.php` | Public registration form validation rules |
| `ProfileValidationTest.php` | Client profile update validation |
| `PropertyAvailabilityTest.php` | `is_property_bookable()`, status label/class mapping |
| `RateLimiterTest.php` | Brute-force throttling logic, including time-window edge cases |

> Writing these tests surfaced a real bug: the visit-request `INSERT` was missing three `NOT NULL` columns (`full_name`, `phone`, `visit_time`), which meant every visit request submitted through the public site was silently failing. Fixed in `property-details.php`.

---

## Project structure

```
├── admin/              Admin back-office (dashboard, property CRUD, visits, messages, settings)
├── api/                JSON REST endpoint (properties.php)
├── config/              config.php (constants), database.php (Singleton), env.php (.env loader)
├── database/           schema.sql
├── includes/           Shared logic: functions.php, PropertyRepository.php,
│                        auth.php / user_auth.php (admin vs client sessions),
│                        mailer.php, rate_limiter.php
├── public/uploads/      User-uploaded images (+ .htaccess denying script execution)
├── tests/               PHPUnit unit tests + bootstrap.php
├── user/                Client area (dashboard, favorites, visits, profile)
├── composer.json
├── phpunit.xml
└── .env.example
```

---

## API reference

`GET /api/properties.php`

| Param | Type | Description |
|---|---|---|
| `id` | int | Return a single property (with features) instead of a list |
| `q` | string | Full-text search across title, description, address |
| `category` | string | Category slug |
| `governorate` | string | Exact match |
| `city` | string | Partial match |
| `max_price` | float | Upper bound on `rent_price` |
| `sort` | string | `relevance` (default) \| `newest` \| `oldest` \| `price_asc` \| `price_desc` \| `area_desc` |
| `page`, `per_page` | int | Pagination (`per_page` capped at 50) |

Response:
```json
{
  "success": true,
  "count": 9,
  "total": 34,
  "page": 1,
  "per_page": 9,
  "total_pages": 4,
  "data": [ /* property objects */ ]
}
```

---

## Known limitations

Documented deliberately — these are the trade-offs a 5-minute review should surface, so they're surfaced here instead:

- **Rate limiting is session-scoped**, not IP- or account-scoped at the storage layer. An attacker who clears cookies (or uses a private window) resets their attempt count. Adequate as a deterrent against casual brute-forcing and as a demonstration of the pattern; a production deployment should back it with a database table or Redis, keyed by IP + account.
- **Seed data (`database/schema.sql`) is in French.** All application code, comments and UI strings are English; the demo property listings were left as-is since they're content, not code.
- **No image resizing/optimization** on upload — files are stored as-is (capped at 3 MB). A production version would generate thumbnails server-side.
- **Single-server deployment assumption** — sessions are stored on local disk (PHP default), which doesn't horizontally scale without a shared session store.

---

## Roadmap

- [ ] IP-based rate limiting backed by a dedicated `login_attempts` table
- [ ] Image thumbnail generation on upload
- [ ] Integration tests (SQLite in-memory) alongside the current pure-function unit tests
- [ ] Interactive map (Leaflet.js) for property location
- [ ] API versioning (`/api/v1/`) if a mobile client is built against it

---

## License

Academic project — no license specified. All rights reserved by the author unless stated otherwise.
