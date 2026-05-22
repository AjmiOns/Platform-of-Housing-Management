# Dar Tunisie - Plateforme de gestion immobiliere

Projet PHP/MySQL from scratch pour une agence immobiliere en Tunisie specialisee dans la location de maisons, appartements, villas, studios et bureaux.

## Fonctionnalites

- Page d'accueil dynamique
- Liste des biens avec filtres: type, gouvernorat, ville, budget
- Details d'un bien avec prix en TND
- Formulaire de demande de visite
- Formulaire de contact
- Back-office admin
- CRUD des biens immobiliers
- Upload d'image ou URL image
- Gestion des demandes de visite
- Gestion des messages
- Parametres personnalisables de l'agence

## Installation avec XAMPP

1. Copiez le dossier `tunisie-logement` dans:

```txt
C:\xampp\htdocs\tunisie-logement
```

2. Lancez XAMPP puis demarrez:

```txt
Apache
MySQL
```

3. Ouvrez phpMyAdmin:

```txt
http://localhost/phpmyadmin
```

4. Importez le fichier:

```txt
database/schema.sql
```

5. Ouvrez le site:

```txt
http://localhost/tunisie-logement/index.php
```

6. Espace admin:

```txt
http://localhost/tunisie-logement/login.php
```

Compte par defaut:

```txt
Email: admin@dar-tunisie.tn
Mot de passe: admin123
```

## Personnalisation

### Changer le nom du dossier

Si vous renommez le dossier, modifiez:

```php
config/config.php
```

Puis changez:

```php
define('APP_BASE', '/tunisie-logement');
```

Exemple:

```php
define('APP_BASE', '/mon-agence');
```

### Changer les informations de l'agence

Connectez-vous a l'admin puis allez dans:

```txt
Admin > Parametres
```

Vous pouvez modifier:

- Nom de l'agence
- Email
- Telephone
- WhatsApp
- Adresse
- Gouvernorat
- Google Maps
- Horaires

### Changer les biens

Allez dans:

```txt
Admin > Biens
```

Vous pouvez ajouter, modifier ou supprimer des biens.

## Structure

```txt
projet_js/
├── admin/
│   ├── _menu.php
│   ├── _property-fields.php
│   ├── dashboard.php
│   ├── messages.php
│   ├── properties.php
│   ├── property-add.php
│   ├── property-delete.php
│   ├── property-edit.php
│   ├── settings.php
│   └── visits.php
│
├── api/
│   └── properties.php
│
├── assets/
│   ├── css/
│   │   ├── animate.css
│   │   ├── flex-slider.css
│   │   ├── fontawesome.css
│   │   ├── owl.css
│   │   ├── templatemo-villa-agency.css
│   │   └── user-dashboard.css
│   │
│   ├── images/
│   │
│   ├── js/
│   │   └── jquery.custom.js
│   │
│   └── webfonts/
├── config/
│   ├── config.php
│   └── database.php
│
├── database/
│   └── schema.sql
│
├── includes/
│   ├── PropertyRepository.php
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── user_auth.php
│
├── public/
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── js/app.js
│   │   └── images/property-placeholder.svg
│   │
│   └── uploads/
│       ├── agencyCover/
│       ├── agencyLogo/
│       └── propertyImages/
│
├── user/
│   ├── _layout_bottom.php
│   ├── _layout_top.php
│   ├── dashboard.php
│   ├── favoris.php
│   ├── login.php
│   ├── logout.php
│   ├── mes-visites.php
│   ├── profil.php
│   ├── register.php
│   └── toggle-favori.php
│
├── admin/ (pages principales déjà incluses ci-dessus)
├── contact.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── properties.php
├── properties.php (root si utilisé)
├── property-details.php
└── README.md
```

## Notes techniques

- PHP natif avec PDO
- MySQL / MariaDB
- Bootstrap via CDN
- Protection CSRF simple sur les formulaires
- Echappement HTML avec `htmlspecialchars`
- Upload images: JPG, PNG, WEBP, max 3 MB
