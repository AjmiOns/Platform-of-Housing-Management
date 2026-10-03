<div align="center">

# 🏠 Dar Tunisie

### Property management platform — apartment, villa & studio rentals in Tunisia

A full-stack web application for a Tunisian real estate agency, built with **PHP**, **MySQL**, **Bootstrap 5** and **JavaScript**.

<br>

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

![Status](https://img.shields.io/badge/status-in%20development-yellow?style=flat-square)
![Responsive](https://img.shields.io/badge/design-responsive-success?style=flat-square)
![Tests](https://img.shields.io/badge/tests-PHPUnit-blue?style=flat-square)
![Repo size](https://img.shields.io/github/repo-size/AjmiOns/Platform-of-Housing-Management?style=flat-square&color=pink)
![Last commit](https://img.shields.io/github/last-commit/AjmiOns/Platform-of-Housing-Management?style=flat-square&color=green)

<br>

<img src="assets/screenshots/home.png" alt="Dar Tunisie home page" width="90%">

</div>

---

## 📑 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Preview](#-preview)
- [Tech Stack](#️-tech-stack)
- [Security](#-security)
- [Project Structure](#-project-structure)
- [Installation](#-installation)
- [Configuration](#️-configuration)
- [Tests](#-tests)
- [API](#-api)
- [Site Pages](#️-site-pages)
- [Roadmap](#-roadmap)
- [Contributing](#-contributing)
- [Credits](#-credits)
- [Author](#-author)

---

## 💡 About

**Dar Tunisie** is a complete property management platform for a Tunisian agency specializing in the rental of apartments, houses, villas and studios.

The project covers three distinct areas: a **public website** to search for a property and request a visit, a **client area** to manage favorites and track requests, and an **admin back-office** to manage properties, visits and messages.

> 🎯 **Goal:** to provide a clean, secure and tested full-stack foundation — not just a CRUD, but an application built with real engineering practices (validation, CSRF protection, unit tests, REST API).

---

## ✨ Features

| | Feature | Description |
|---|---|---|
| 🏠 | **Home** | Quick search, featured properties, agency statistics |
| 🔎 | **Property catalog** | Combined filters (type, governorate, city, budget, keyword), sorting (price, date, area) and pagination — all via AJAX without page reloads |
| 📄 | **Property page** | Gallery, detailed characteristics, visit request form |
| ❤️ | **Favorites** | Add/remove in one click, without reloading the page |
| 📅 | **Visit request** | Form with automatic email confirmation |
| 📬 | **Contact** | Form with email notification to the agency |
| 🔐 | **Client area** | Registration, login, profile, visit history, favorites |
| 🛠️ | **Admin back-office** | Dashboard with charts, property CRUD, visit and message management, agency settings |
| 🧪 | **Automated tests** | PHPUnit suite covering business logic (validation, availability, anti-brute-force) |
| 🔌 | **REST API** | Documented JSON endpoint for property search |
| 📱 | **Responsive** | Interface adapted for mobile, tablet and desktop |

---

## 📸 Preview

> The screenshots below should be replaced with your own once the project is running locally — place them in `assets/screenshots/`.

### 🏠 Home

<div align="center">
  <img src="assets/screenshots/home.png" alt="Home" width="100%">
</div>

<br>

### 🔎 Catalog & property page

<table>
  <tr>
    <td align="center" width="50%">
      <img src="assets/screenshots/properties.png" alt="Property catalog"><br>
      <sub><b>Property catalog (filters + sorting + pagination)</b></sub>
    </td>
    <td align="center" width="50%">
      <img src="assets/screenshots/property-details.png" alt="Property page"><br>
      <sub><b>Property page & visit request</b></sub>
    </td>
  </tr>
</table>

### 🔐 Client area & back-office

<table>
  <tr>
    <td align="center" width="33%">
      <img src="assets/screenshots/dashboard-client.png" alt="Client dashboard"><br>
      <sub><b>Client dashboard</b></sub>
    </td>
    <td align="center" width="33%">
      <img src="assets/screenshots/dashboard-admin.png" alt="Admin dashboard"><br>
      <sub><b>Admin dashboard (charts)</b></sub>
    </td>
    <td align="center" width="33%">
      <img src="assets/screenshots/admin-properties.png" alt="Property management"><br>
      <sub><b>Property management</b></sub>
    </td>
  </tr>
</table>

### 📬 Contact & favorites

<table>
  <tr>
    <td align="center" width="50%">
      <img src="assets/screenshots/contact.png" alt="Contact"><br>
      <sub><b>Contact</b></sub>
    </td>
    <td align="center" width="50%">
      <img src="assets/screenshots/favoris.png" alt="Favorites"><br>
      <sub><b>My favorites</b></sub>
    </td>
  </tr>
</table>

---

## 🛠️ Tech Stack

| Category | Technologies |
|---|---|
| **Backend** | PHP 8.1+, PDO (prepared statements) |
| **Database** | MySQL / MariaDB |
| **Frontend** | Bootstrap 5, vanilla JavaScript (fetch API, no framework) |
| **Emails** | PHPMailer (configurable SMTP) |
| **Tests** | PHPUnit 10 |
| **Dependency management** | Composer |
| **Icons & fonts** | Font Awesome, Google Fonts |
| **Local environment** | XAMPP |
| **Versioning** | Git & GitHub |

---

## 🔒 Security

| Measure | Details |
|---|---|
| **Passwords** | Hashed with `password_hash()` (bcrypt), verified with `password_verify()` |
| **CSRF** | Token required and verified on all forms |
| **XSS** | All output escaped with `htmlspecialchars()` |
| **SQL Injection** | 100% prepared statements (PDO) |
| **Image uploads** | Extension **and** actual MIME type checked (`finfo_file`), script execution blocked in the uploads folder |
| **Anti-brute-force** | Rate limiting on admin and client logins (5 attempts / 15 min) |
| **Secrets** | Database and SMTP credentials stored in `.env` (never versioned) |

---

## 📂 Project Structure

```
dar-tunisie/
├── admin/                  # Back-office (dashboard, properties, visits, messages, settings)
├── api/
│   └── properties.php      # REST JSON endpoint
├── config/                 # Configuration (constants, DB connection, .env loader)
├── database/
│   └── schema.sql          # Full schema + demo data
├── includes/                # Shared logic
│   ├── PropertyRepository.php   # Repository pattern (property CRUD)
│   ├── functions.php            # Utility functions
│   ├── mailer.php                # Emails (PHPMailer)
│   ├── rate_limiter.php          # Anti-brute-force
│   ├── auth.php / user_auth.php  # Admin / client sessions
├── public/
│   └── uploads/             # Uploaded images (protected by .htaccess)
├── tests/                    # PHPUnit unit tests
├── user/                     # Client area (dashboard, favorites, visits, profile)
├── composer.json
├── phpunit.xml
└── .env.example
```

---

## 🚀 Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any PHP + MySQL server)
- PHP ≥ 8.1 with the `pdo_mysql`, `mbstring` and `fileinfo` extensions
- [Composer](https://getcomposer.org/)

### 1. Clone the repository

```bash
git clone https://github.com/AjmiOns/Platform-of-Housing-Management.git
cd Platform-of-Housing-Management
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure the environment

```bash
copy .env.example .env
```

Edit `.env` if your setup differs from the default values (see [Configuration](#️-configuration)).

### 4. Create the database

```bash
mysql -u root -e "CREATE DATABASE tunisie_logement CHARACTER SET utf8mb4;"
mysql -u root tunisie_logement < database/schema.sql
```

### 5. Run the project

Start **Apache** and **MySQL** from the XAMPP control panel, then go to:

👉 `http://localhost/dar-tunisie/index.php`

**Default admin account** (created by `schema.sql`):
```
Email    : admin@dar-tunisie.tn
Password : admin123
```
⚠️ Change this before any production deployment.

---

## ⚙️ Configuration

Main variables in the `.env` file:

| Variable | Purpose | Default value |
|---|---|---|
| `APP_BASE` | URL prefix, must match your folder name in `htdocs` | `/projet_js` |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | Database connection | default XAMPP values |
| `MAIL_HOST` | SMTP server — leave empty to disable emails | *(empty)* |
| `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_PORT`, `MAIL_ENCRYPTION` | SMTP credentials | — |

💡 To test emails without a real mailbox, create a free inbox on [mailtrap.io](https://mailtrap.io) and paste its SMTP credentials into `.env`.

---

## 🧪 Tests

```bash
composer install
composer test
```

The test suite covers pure business logic (validation, availability, anti-brute-force) — no database required, runs in under a second:

| File | Covers |
|---|---|
| `ClientRegistrationValidationTest.php` | Registration validation rules |
| `ProfileValidationTest.php` | Profile update validation |
| `PropertyAvailabilityTest.php` | Property availability for a visit |
| `RateLimiterTest.php` | Anti-brute-force logic |

---

## 🔌 API

`GET /api/properties.php`

| Parameter | Type | Description |
|---|---|---|
| `id` | int | Returns a specific property (with its amenities) |
| `q` | string | Full-text search (title, description, address) |
| `category` | string | Category slug |
| `governorate` | string | Governorate |
| `city` | string | City (partial match) |
| `max_price` | float | Maximum budget |
| `sort` | string | `relevance` \| `newest` \| `price_asc` \| `price_desc` \| `area_desc` |
| `page`, `per_page` | int | Pagination |

**Example response:**
```json
{
  "success": true,
  "count": 9,
  "total": 34,
  "page": 1,
  "total_pages": 4,
  "data": [ /* properties */ ]
}
```

---

## 🗺️ Site Pages

| Page | File | Purpose |
|---|---|---|
| Home | `index.php` | Quick search, featured properties |
| Catalog | `properties.php` | Property list with filters/sorting/pagination |
| Property details | `property-details.php` | Full page + visit request |
| Contact | `contact.php` | Contact form |
| Admin login | `login.php` | Back-office access |
| Client registration/login | `user/register.php`, `user/login.php` | Client area access |
| Client dashboard | `user/dashboard.php` | Activity overview |
| Favorites | `user/favoris.php` | Favorite properties |
| My visits | `user/mes-visites.php` | Request history |
| Admin dashboard | `admin/dashboard.php` | KPIs and charts |
| Property management | `admin/properties.php` | Property CRUD |
| Visits | `admin/visits.php` | Visit request management |
| Messages | `admin/messages.php` | Contact inbox |
| Settings | `admin/settings.php` | Agency configuration |

---

## 🧭 Roadmap

- [x] Catalog with filters, sorting and pagination
- [x] Client area (favorites, visits, profile)
- [x] Admin back-office with graphical dashboard
- [x] Security (CSRF, hashing, secure uploads, anti-brute-force)
- [x] PHPUnit unit tests
- [x] Transactional emails (PHPMailer)
- [x] REST API for property search
- [ ] IP-based rate limiting (dedicated database table)
- [ ] Thumbnail generation for uploaded images
- [ ] Interactive map (Leaflet.js) for property locations
- [ ] Integration tests (in-memory SQLite database)
- [ ] API versioning (`/api/v1/`)

---

## 🤝 Contributing

Contributions are welcome!

1. **Fork** the project
2. Create a branch: `git checkout -b feature/my-feature`
3. Commit: `git commit -m "feat: add my feature"`
4. Push: `git push origin feature/my-feature`
5. Open a **Pull Request**

**Commit convention:** [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `docs:`, `test:`, `security:`, `refactor:`…).

---

## 🙏 Credits

- UI components: [Bootstrap](https://getbootstrap.com/)
- Icons: [Font Awesome](https://fontawesome.com/)
- Emails: [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- Tests: [PHPUnit](https://phpunit.de/)
- Property images are used for demonstration purposes only.

---

## 👩‍💻 Author

**Ons Ajmi** — [@AjmiOns](https://github.com/AjmiOns)

<div align="center">

⭐ If you like this project, feel free to give it a star!

<sub>Made with 💚 and lots of ☕</sub>

</div>
