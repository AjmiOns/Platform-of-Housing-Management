-- ============================================================
--  DATABASE : tunisie_logement
--  Encoding : utf8mb4 / utf8mb4_unicode_ci
--  Default admin : admin@dar-tunisie.tn / admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS tunisie_logement
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tunisie_logement;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS visit_requests;
DROP TABLE IF EXISTS property_features;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS owners;
DROP TABLE IF EXISTS agency_settings;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  TABLE : users
-- ============================================================
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)  NOT NULL,
  email         VARCHAR(160)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  role          ENUM('admin','agent') NOT NULL DEFAULT 'admin',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : agency_settings
-- ============================================================
CREATE TABLE agency_settings (
  id             INT PRIMARY KEY DEFAULT 1,
  agency_name    VARCHAR(160)  NOT NULL,
  slogan         VARCHAR(255)  NOT NULL,
  email          VARCHAR(160)  NOT NULL,
  phone          VARCHAR(30)   NOT NULL,
  whatsapp       VARCHAR(30)   NOT NULL,
  address        VARCHAR(255)  NOT NULL,
  city           VARCHAR(100)  NOT NULL,
  governorate    VARCHAR(100)  NOT NULL,
  map_embed_url  TEXT          NULL,
  facebook       VARCHAR(255)  NULL,
  instagram      VARCHAR(255)  NULL,
  working_hours  VARCHAR(160)  NOT NULL,
  updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : owners
-- ============================================================
CREATE TABLE owners (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  full_name  VARCHAR(160)  NOT NULL,
  cin        VARCHAR(20)   NULL,
  phone      VARCHAR(30)   NOT NULL,
  email      VARCHAR(160)  NULL,
  address    VARCHAR(255)  NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : categories
-- ============================================================
CREATE TABLE categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : properties
-- ============================================================
CREATE TABLE properties (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  owner_id            INT           NULL,
  category_id         INT           NOT NULL,
  title               VARCHAR(180)  NOT NULL,
  description         TEXT          NOT NULL,
  governorate         VARCHAR(100)  NOT NULL,
  city                VARCHAR(100)  NOT NULL,
  address             VARCHAR(255)  NOT NULL,
  rent_price          DECIMAL(10,2) NOT NULL,
  area                DECIMAL(8,2)  NOT NULL,
  rooms               INT           NOT NULL DEFAULT 1,
  bedrooms            INT           NOT NULL DEFAULT 1,
  bathrooms           INT           NOT NULL DEFAULT 1,
  floor               VARCHAR(30)   NULL,
  parking             INT           NOT NULL DEFAULT 0,
  furnished           TINYINT(1)    NOT NULL DEFAULT 0,
  availability_status ENUM('available','reserved','rented') NOT NULL DEFAULT 'available',
  contract_ready      TINYINT(1)    NOT NULL DEFAULT 1,
  payment_method      VARCHAR(80)   NOT NULL DEFAULT 'Virement bancaire ou espece',
  image_url           VARCHAR(500)  NULL,
  published           TINYINT(1)    NOT NULL DEFAULT 1,
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_properties_owner    FOREIGN KEY (owner_id)    REFERENCES owners(id)     ON DELETE SET NULL,
  CONSTRAINT fk_properties_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : property_features
-- ============================================================
CREATE TABLE property_features (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT          NOT NULL,
  feature     VARCHAR(120) NOT NULL,
  CONSTRAINT fk_features_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : visit_requests
-- ============================================================
CREATE TABLE visit_requests (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT          NOT NULL,
  full_name   VARCHAR(160) NOT NULL,
  phone       VARCHAR(30)  NOT NULL,
  email       VARCHAR(160) NULL,
  visit_date  DATE         NOT NULL,
  visit_time  TIME         NOT NULL,
  message     TEXT         NULL,
  status      ENUM('new','confirmed','cancelled','done') NOT NULL DEFAULT 'new',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_visit_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : contact_messages
-- ============================================================
CREATE TABLE contact_messages (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(160) NOT NULL,
  phone     VARCHAR(30)  NOT NULL,
  email     VARCHAR(160) NULL,
  subject   VARCHAR(180) NOT NULL,
  message   TEXT         NOT NULL,
  status    ENUM('new','read','archived') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
--  SEED DATA
-- ============================================================

-- Default admin account (password: admin123)
INSERT INTO users (name, email, password_hash, role) VALUES
('Administrateur', 'admin@dar-tunisie.tn',
 '$2y$12$CdJcQqxSecc5DYonFYc.aOKi/1VZXyyof.9ZQ199jF2pDuWud9D1O', 'admin');

-- Agency settings
INSERT INTO agency_settings
  (id, agency_name, slogan, email, phone, whatsapp, address, city, governorate,
   map_embed_url, facebook, instagram, working_hours)
VALUES
  (1, 'Dar Tunisie', 'Location de maisons et appartements en Tunisie',
   'contact@dar-tunisie.tn', '+216 71 234 567', '+216 55 123 456',
   'Avenue Habib Bourguiba', 'Tunis', 'Tunis',
   'https://www.google.com/maps?q=Tunis,Tunisia&output=embed',
   '#', '#', 'Lundi - Samedi : 09:00 - 18:00');

-- Owners
INSERT INTO owners (full_name, cin, phone, email, address) VALUES
('Mohamed Ben Salem', '12345678', '+216 98 111 222', 'mohamed@example.tn', 'La Marsa, Tunis'),
('Sarra Trabelsi',   '87654321', '+216 24 333 444', 'sarra@example.tn',   'Hammamet, Nabeul'),
('Youssef Jaziri',   '11223344', '+216 29 555 666', 'youssef@example.tn', 'Sahloul, Sousse');

-- Categories
INSERT INTO categories (name, slug) VALUES
('Appartement', 'appartement'),
('Maison',      'maison'),
('Villa',       'villa'),
('Studio',      'studio'),
('Bureau',      'bureau');

-- Properties
INSERT INTO properties
  (owner_id, category_id, title, description, governorate, city, address,
   rent_price, area, rooms, bedrooms, bathrooms, floor, parking, furnished,
   availability_status, contract_ready, payment_method, image_url, published)
VALUES
(1, 1,
 'Appartement S+2 meuble a La Marsa',
 'Appartement lumineux proche de la plage, ideal pour famille ou couple. Quartier calme, commerces et transport disponibles.',
 'Tunis', 'La Marsa', 'Rue de la Plage, La Marsa',
 1450, 105, 3, 2, 1, '2', 1, 1, 'available', 1,
 'Virement bancaire ou espece',
 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80', 1),

(2, 3,
 'Villa avec jardin a Hammamet',
 'Villa spacieuse avec jardin, terrasse, cuisine equipee et acces rapide a la zone touristique.',
 'Nabeul', 'Hammamet', 'Zone touristique Hammamet Nord',
 2800, 260, 5, 4, 2, 'RDC + 1', 2, 1, 'available', 1,
 'Cheque ou virement bancaire',
 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80', 1),

(3, 1,
 'Appartement S+3 a Sahloul',
 'Appartement proche hopital et universite, bon standing, balcon, place parking et syndic securise.',
 'Sousse', 'Sahloul', 'Avenue Yasser Arafat, Sahloul',
 1200, 130, 4, 3, 2, '4', 1, 0, 'available', 1,
 'Virement bancaire',
 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80', 1),

(1, 4,
 'Studio moderne au Centre Urbain Nord',
 'Studio adapte aux etudiants et jeunes actifs, proche transport, centre commercial et bureaux.',
 'Tunis', 'Centre Urbain Nord', 'Rue du Lac, Centre Urbain Nord',
 850, 55, 1, 1, 1, '5', 0, 1, 'reserved', 1,
 'Espece ou virement',
 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80', 1),

(2, 2,
 'Maison traditionnelle a Nabeul',
 'Maison independante avec cour, proche souk et commodites. Bon choix pour location annuelle.',
 'Nabeul', 'Nabeul', 'Quartier El Mrezga',
 1600, 180, 4, 3, 1, 'RDC', 1, 0, 'available', 1,
 'Virement bancaire',
 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80', 1),

(3, 1,
 'Appartement haut standing a Monastir',
 'Appartement neuf, residence securisee, ascenseur, balcon et vue degagee.',
 'Monastir', 'Monastir', 'Route de la Falaise',
 1100, 95, 3, 2, 1, '3', 1, 1, 'rented', 1,
 'Virement bancaire',
 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80', 1);

-- Property features
INSERT INTO property_features (property_id, feature) VALUES
(1, 'Climatisation'),   (1, 'Cuisine equipee'), (1, 'Balcon'),       (1, 'Proche plage'),
(2, 'Jardin'),          (2, 'Terrasse'),         (2, 'Garage'),       (2, 'Chauffage central'),
(3, 'Ascenseur'),       (3, 'Residence securisee'), (3, 'Balcon'),
(4, 'Meuble'),          (4, 'Internet disponible'), (4, 'Proche transport'),
(5, 'Cour'),            (5, 'Entree independante'), (5, 'Quartier calme'),
(6, 'Ascenseur'),       (6, 'Parking'),          (6, 'Haut standing');
-- ============================================================
--  MIGRATION : ajout des colonnes logo et cover dans agency_settings
-- ============================================================
ALTER TABLE agency_settings
    ADD COLUMN IF NOT EXISTS logo_url  VARCHAR(500) NULL AFTER map_embed_url,
    ADD COLUMN IF NOT EXISTS cover_url VARCHAR(500) NULL AFTER logo_url;
