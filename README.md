<div align="center">

# 🏠 Dar Tunisie

### Plateforme de gestion immobilière — location d'appartements, villas & studios en Tunisie

Application web full-stack pour une agence immobilière tunisienne, construite avec **PHP**, **MySQL**, **Bootstrap 5** et **JavaScript**.

<br>

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

![Status](https://img.shields.io/badge/statut-en%20d%C3%A9veloppement-yellow?style=flat-square)
![Responsive](https://img.shields.io/badge/design-responsive-success?style=flat-square)
![Tests](https://img.shields.io/badge/tests-PHPUnit-blue?style=flat-square)
![Repo size](https://img.shields.io/github/repo-size/AjmiOns/Platform-of-Housing-Management?style=flat-square&color=pink)
![Last commit](https://img.shields.io/github/last-commit/AjmiOns/Platform-of-Housing-Management?style=flat-square&color=green)

<br>

<img src="assets/screenshots/home.png" alt="Page d'accueil Dar Tunisie" width="90%">

</div>

---

## 📑 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Aperçu](#-aperçu)
- [Stack technique](#-stack-technique)
- [Sécurité](#-sécurité)
- [Structure du projet](#-structure-du-projet)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Tests](#-tests)
- [API](#-api)
- [Pages du site](#-pages-du-site)
- [Roadmap](#-roadmap)
- [Contribuer](#-contribuer)
- [Crédits](#-crédits)
- [Auteur](#-auteur)

---

## 💡 À propos

**Dar Tunisie** est une plateforme complète de gestion immobilière pour une agence tunisienne spécialisée dans la location d'appartements, maisons, villas et studios.

Le projet couvre trois espaces distincts : un **site public** pour rechercher un bien et demander une visite, un **espace client** pour gérer ses favoris et suivre ses demandes, et un **back-office admin** pour gérer les biens, les visites et les messages.

> 🎯 **Objectif :** proposer une base full-stack propre, sécurisée et testée — pas juste un CRUD, mais une application pensée avec de vraies pratiques d'ingénierie (validation, protection CSRF, tests unitaires, API REST).

---

## ✨ Fonctionnalités

| | Fonctionnalité | Description |
|---|---|---|
| 🏠 | **Accueil** | Recherche rapide, biens mis en avant, statistiques de l'agence |
| 🔎 | **Catalogue de biens** | Filtres combinés (type, gouvernorat, ville, budget, mot-clé), tri (prix, date, surface) et pagination — le tout en AJAX sans rechargement de page |
| 📄 | **Fiche bien** | Galerie, caractéristiques détaillées, formulaire de demande de visite |
| ❤️ | **Favoris** | Ajout/retrait en un clic, sans rechargement de page |
| 📅 | **Demande de visite** | Formulaire avec confirmation automatique par email |
| 📬 | **Contact** | Formulaire avec notification email à l'agence |
| 🔐 | **Espace client** | Inscription, connexion, profil, historique des visites, favoris |
| 🛠️ | **Back-office admin** | Dashboard avec graphiques, CRUD des biens, gestion des visites et messages, paramètres de l'agence |
| 🧪 | **Tests automatisés** | Suite PHPUnit sur la logique métier (validation, disponibilité, anti brute-force) |
| 🔌 | **API REST** | Endpoint JSON documenté pour la recherche de biens |
| 📱 | **Responsive** | Interface adaptée mobile, tablette et desktop |

---

## 📸 Aperçu

> Les captures ci-dessous sont à remplacer par vos propres captures d'écran une fois le projet lancé localement — placez-les dans `assets/screenshots/`.

### 🏠 Accueil

<div align="center">
  <img src="assets/screenshots/home.png" alt="Accueil" width="100%">
</div>

<br>

### 🔎 Catalogue & fiche bien

<table>
  <tr>
    <td align="center" width="50%">
      <img src="assets/screenshots/properties.png" alt="Catalogue de biens"><br>
      <sub><b>Catalogue de biens (filtres + tri + pagination)</b></sub>
    </td>
    <td align="center" width="50%">
      <img src="assets/screenshots/property-details.png" alt="Fiche bien"><br>
      <sub><b>Fiche bien & demande de visite</b></sub>
    </td>
  </tr>
</table>

### 🔐 Espace client & back-office

<table>
  <tr>
    <td align="center" width="33%">
      <img src="assets/screenshots/dashboard-client.png" alt="Dashboard client"><br>
      <sub><b>Dashboard client</b></sub>
    </td>
    <td align="center" width="33%">
      <img src="assets/screenshots/dashboard-admin.png" alt="Dashboard admin"><br>
      <sub><b>Dashboard admin (graphiques)</b></sub>
    </td>
    <td align="center" width="33%">
      <img src="assets/screenshots/admin-properties.png" alt="Gestion des biens"><br>
      <sub><b>Gestion des biens</b></sub>
    </td>
  </tr>
</table>

### 📬 Contact & favoris

<table>
  <tr>
    <td align="center" width="50%">
      <img src="assets/screenshots/contact.png" alt="Contact"><br>
      <sub><b>Contact</b></sub>
    </td>
    <td align="center" width="50%">
      <img src="assets/screenshots/favoris.png" alt="Favoris"><br>
      <sub><b>Mes favoris</b></sub>
    </td>
  </tr>
</table>

---

## 🛠️ Stack technique

| Catégorie | Technologies |
|---|---|
| **Backend** | PHP 8.1+, PDO (requêtes préparées) |
| **Base de données** | MySQL / MariaDB |
| **Frontend** | Bootstrap 5, JavaScript vanilla (fetch API, pas de framework) |
| **Emails** | PHPMailer (SMTP configurable) |
| **Tests** | PHPUnit 10 |
| **Gestion des dépendances** | Composer |
| **Icônes & polices** | Font Awesome, Google Fonts |
| **Environnement local** | XAMPP |
| **Versioning** | Git & GitHub |

---

## 🔒 Sécurité

| Mesure | Détail |
|---|---|
| **Mots de passe** | Hashés avec `password_hash()` (bcrypt), vérifiés avec `password_verify()` |
| **CSRF** | Token requis et vérifié sur tous les formulaires |
| **XSS** | Toutes les sorties échappées avec `htmlspecialchars()` |
| **SQL Injection** | 100% requêtes préparées (PDO) |
| **Upload d'images** | Extension **et** type MIME réel vérifiés (`finfo_file`), exécution de scripts bloquée dans le dossier uploads |
| **Anti brute-force** | Rate limiting sur les connexions admin et client (5 tentatives / 15 min) |
| **Secrets** | Identifiants base de données et SMTP dans `.env` (jamais versionnés) |

---

## 📂 Structure du projet

```
dar-tunisie/
├── admin/                  # Back-office (dashboard, biens, visites, messages, paramètres)
├── api/
│   └── properties.php      # Endpoint JSON REST
├── config/                 # Configuration (constantes, connexion DB, loader .env)
├── database/
│   └── schema.sql          # Schéma complet + données de démo
├── includes/                # Logique partagée
│   ├── PropertyRepository.php   # Pattern Repository (CRUD biens)
│   ├── functions.php            # Fonctions utilitaires
│   ├── mailer.php                # Emails (PHPMailer)
│   ├── rate_limiter.php          # Anti brute-force
│   ├── auth.php / user_auth.php  # Sessions admin / client
├── public/
│   └── uploads/             # Images uploadées (protégées par .htaccess)
├── tests/                    # Tests unitaires PHPUnit
├── user/                     # Espace client (dashboard, favoris, visites, profil)
├── composer.json
├── phpunit.xml
└── .env.example
```

---

## 🚀 Installation

### Prérequis

- [XAMPP](https://www.apachefriends.org/) (ou tout serveur PHP + MySQL)
- PHP ≥ 8.1 avec les extensions `pdo_mysql`, `mbstring`, `fileinfo`
- [Composer](https://getcomposer.org/)

### 1. Cloner le dépôt

```bash
git clone https://github.com/AjmiOns/Platform-of-Housing-Management.git
cd Platform-of-Housing-Management
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
copy .env.example .env
```

Éditez `.env` si votre configuration diffère des valeurs par défaut (voir [Configuration](#-configuration)).

### 4. Créer la base de données

```bash
mysql -u root -e "CREATE DATABASE tunisie_logement CHARACTER SET utf8mb4;"
mysql -u root tunisie_logement < database/schema.sql
```

### 5. Lancer le projet

Démarrez **Apache** et **MySQL** depuis le panneau XAMPP, puis :

👉 `http://localhost/dar-tunisie/index.php`

**Compte admin par défaut** (créé par `schema.sql`) :
```
Email    : admin@dar-tunisie.tn
Password : admin123
```
⚠️ À changer avant toute mise en production.

---

## ⚙️ Configuration

Variables principales du fichier `.env` :

| Variable | Rôle | Valeur par défaut |
|---|---|---|
| `APP_BASE` | Préfixe d'URL, doit correspondre au nom de votre dossier dans `htdocs` | `/projet_js` |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | Connexion à la base de données | valeurs XAMPP par défaut |
| `MAIL_HOST` | Serveur SMTP — laissez vide pour désactiver les emails | *(vide)* |
| `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_PORT`, `MAIL_ENCRYPTION` | Identifiants SMTP | — |

💡 Pour tester les emails sans vraie boîte mail, créez une boîte gratuite sur [mailtrap.io](https://mailtrap.io) et collez ses identifiants SMTP dans `.env`.

---

## 🧪 Tests

```bash
composer install
composer test
```

La suite de tests couvre la logique métier pure (validation, disponibilité, anti brute-force) — aucune base de données requise, exécution en moins d'une seconde :

| Fichier | Couvre |
|---|---|
| `ClientRegistrationValidationTest.php` | Règles de validation de l'inscription |
| `ProfileValidationTest.php` | Validation de la mise à jour du profil |
| `PropertyAvailabilityTest.php` | Disponibilité d'un bien pour une visite |
| `RateLimiterTest.php` | Logique anti brute-force |

---

## 🔌 API

`GET /api/properties.php`

| Paramètre | Type | Description |
|---|---|---|
| `id` | int | Retourne un bien précis (avec ses équipements) |
| `q` | string | Recherche full-text (titre, description, adresse) |
| `category` | string | Slug de catégorie |
| `governorate` | string | Gouvernorat |
| `city` | string | Ville (recherche partielle) |
| `max_price` | float | Budget maximum |
| `sort` | string | `relevance` \| `newest` \| `price_asc` \| `price_desc` \| `area_desc` |
| `page`, `per_page` | int | Pagination |

**Exemple de réponse :**
```json
{
  "success": true,
  "count": 9,
  "total": 34,
  "page": 1,
  "total_pages": 4,
  "data": [ /* biens */ ]
}
```

---

## 🗺️ Pages du site

| Page | Fichier | Rôle |
|---|---|---|
| Accueil | `index.php` | Recherche rapide, biens à la une |
| Catalogue | `properties.php` | Liste des biens avec filtres/tri/pagination |
| Détail bien | `property-details.php` | Fiche complète + demande de visite |
| Contact | `contact.php` | Formulaire de contact |
| Connexion admin | `login.php` | Accès au back-office |
| Inscription/connexion client | `user/register.php`, `user/login.php` | Accès à l'espace client |
| Dashboard client | `user/dashboard.php` | Vue d'ensemble de l'activité |
| Favoris | `user/favoris.php` | Biens favoris |
| Mes visites | `user/mes-visites.php` | Historique des demandes |
| Dashboard admin | `admin/dashboard.php` | KPIs et graphiques |
| Gestion des biens | `admin/properties.php` | CRUD des biens |
| Visites | `admin/visits.php` | Gestion des demandes de visite |
| Messages | `admin/messages.php` | Boîte de réception contact |
| Paramètres | `admin/settings.php` | Configuration de l'agence |

---

## 🧭 Roadmap

- [x] Catalogue avec filtres, tri et pagination
- [x] Espace client (favoris, visites, profil)
- [x] Back-office admin avec dashboard graphique
- [x] Sécurité (CSRF, hashage, upload sécurisé, anti brute-force)
- [x] Tests unitaires PHPUnit
- [x] Emails transactionnels (PHPMailer)
- [x] API REST pour la recherche de biens
- [ ] Rate limiting basé sur l'IP (table dédiée en base)
- [ ] Génération de miniatures pour les images uploadées
- [ ] Carte interactive (Leaflet.js) pour la localisation des biens
- [ ] Tests d'intégration (base SQLite en mémoire)
- [ ] Versionnage de l'API (`/api/v1/`)

---

## 🤝 Contribuer

Les contributions sont les bienvenues !

1. **Forkez** le projet
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`
3. Commitez : `git commit -m "feat: ajout de ma fonctionnalité"`
4. Poussez : `git push origin feature/ma-fonctionnalite`
5. Ouvrez une **Pull Request**

**Convention de commits** : [Conventional Commits](https://www.conventionalcommits.org/fr/) (`feat:`, `fix:`, `docs:`, `test:`, `security:`, `refactor:`…).

---

## 🙏 Crédits

- Composants UI : [Bootstrap](https://getbootstrap.com/)
- Icônes : [Font Awesome](https://fontawesome.com/)
- Emails : [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- Tests : [PHPUnit](https://phpunit.de/)
- Les visuels de biens immobiliers sont utilisés à des fins de démonstration uniquement.

---

## 👩‍💻 Auteur

**Ons Ajmi** — [@AjmiOns](https://github.com/AjmiOns)

<div align="center">

⭐ Si ce projet vous plaît, n'hésitez pas à lui laisser une étoile !

<sub>Fait avec 💚 et beaucoup de ☕</sub>

</div>
