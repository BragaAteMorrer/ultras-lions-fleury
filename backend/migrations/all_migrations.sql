-- Consolidated SQL generated from backend/migrations
-- Includes raw .sql files and SQL extracted from PHP migration up() methods.

-- ============================================================
-- Source: add_gallery_description.sql
-- ============================================================
ALTER TABLE gallery ADD description LONGTEXT DEFAULT NULL;

-- ============================================================
-- Source: billetterie.sql
-- ============================================================
-- phpMyAdmin SQL Dump
-- version 5.x
-- https://www.phpmyadmin.net/
--
-- Base de donnÃ©es : `your_database_name`
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table : ticket_category
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ticket_category`;
CREATE TABLE `ticket_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_TICKET_CATEGORY_SLUG` (`slug`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table : ticket
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE `ticket` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_id` INT DEFAULT NULL,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(180) NOT NULL,
  `opponent` VARCHAR(180) NOT NULL,
  `match_date` DATETIME NOT NULL,
  `venue` VARCHAR(180) DEFAULT NULL,
  `price` DOUBLE NOT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `stock` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_97A0ADA43DA5256D` (`image_id`),
  KEY `IDX_97A0ADA412469DE2` (`category_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Contraintes de clÃ©s Ã©trangÃ¨res
-- --------------------------------------------------------

ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA43DA5256D`
  FOREIGN KEY (`image_id`) REFERENCES `media` (`id`)
  ON DELETE SET NULL;

ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA412469DE2`
  FOREIGN KEY (`category_id`) REFERENCES `ticket_category` (`id`)
  ON DELETE SET NULL;

-- --------------------------------------------------------
-- DonnÃ©es initiales
-- --------------------------------------------------------

INSERT INTO `ticket_category` (`name`, `slug`) VALUES
('Domicile', 'domicile'),
('ExtÃ©rieur', 'exterieur');

COMMIT;

-- ============================================================
-- Source: importphpmyadmin.sql
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- USERS
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,

    -- Mot de passe hashÃ©
    password VARCHAR(255) NOT NULL,

    -- IdentitÃ©
    pseudo VARCHAR(50) NOT NULL,
    nom VARCHAR(100) DEFAULT NULL,
    prenom VARCHAR(100) DEFAULT NULL,
    date_naissance DATE DEFAULT NULL,

    -- CoordonnÃ©es
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    code_postal VARCHAR(12) DEFAULT NULL,

    -- Photos
    photo_profil VARCHAR(255) DEFAULT NULL,
    banniere_profil VARCHAR(255) DEFAULT NULL,

    -- Tailles vÃªtements
    taille_tshirt VARCHAR(10) DEFAULT NULL,
    taille_polo VARCHAR(10) DEFAULT NULL,
    taille_pull VARCHAR(10) DEFAULT NULL,
    taille_sweat VARCHAR(10) DEFAULT NULL,
    taille_veste VARCHAR(10) DEFAULT NULL,
    taille_short VARCHAR(10) DEFAULT NULL,

    UNIQUE KEY UNIQ_USERS_EMAIL (email),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- INVITE_CODE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS invite_code (
    id INT AUTO_INCREMENT NOT NULL,
    code VARCHAR(100) NOT NULL,
    used TINYINT(1) NOT NULL,
    expires_at DATETIME NOT NULL,
    UNIQUE KEY UNIQ_INVITE_CODE (code),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- SITE_CONFIG
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS site_config (
    id INT AUTO_INCREMENT NOT NULL,
    site_name VARCHAR(255) DEFAULT NULL,
    hero_title VARCHAR(255) DEFAULT NULL,
    hero_subtitle LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- CATEGORY
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    UNIQUE KEY UNIQ_CATEGORY_SLUG (slug),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- GALLERY
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- GROUP_PAGE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS group_page (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(120) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    logo_id INT DEFAULT NULL,
    banner_id INT DEFAULT NULL,
    KEY IDX_GROUPPAGE_LOGO (logo_id),
    KEY IDX_GROUPPAGE_BANNER (banner_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- MEDIA
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT NOT NULL,
    path VARCHAR(255) DEFAULT NULL,
    alt VARCHAR(255) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    gallery_id INT DEFAULT NULL,
    group_page_id INT DEFAULT NULL,
    KEY IDX_MEDIA_GALLERY (gallery_id),
    KEY IDX_MEDIA_GROUPPAGE (group_page_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE media 
    ADD CONSTRAINT FK_MEDIA_GALLERY FOREIGN KEY (gallery_id) REFERENCES gallery(id) ON DELETE SET NULL;

ALTER TABLE media 
    ADD CONSTRAINT FK_MEDIA_GROUPPAGE FOREIGN KEY (group_page_id) REFERENCES group_page(id) ON DELETE SET NULL;

ALTER TABLE group_page 
    ADD CONSTRAINT FK_GROUPPAGE_LOGO FOREIGN KEY (logo_id) REFERENCES media(id) ON DELETE SET NULL;

ALTER TABLE group_page 
    ADD CONSTRAINT FK_GROUPPAGE_BANNER FOREIGN KEY (banner_id) REFERENCES media(id) ON DELETE SET NULL;



-- -----------------------------------------------------
-- EVENT
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS event (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    date DATETIME NOT NULL,
    description LONGTEXT DEFAULT NULL,
    image_id INT DEFAULT NULL,
    KEY IDX_EVENT_IMAGE (image_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE event 
    ADD CONSTRAINT FK_EVENT_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL;



-- -----------------------------------------------------
-- MERCH
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS merch (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    price DOUBLE NOT NULL,
    description LONGTEXT DEFAULT NULL,
    image_id INT DEFAULT NULL,
    KEY IDX_MERCH_IMAGE (image_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE merch 
    ADD CONSTRAINT FK_MERCH_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL;



-- -----------------------------------------------------
-- PAGE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS page (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    image_id INT DEFAULT NULL,
    UNIQUE KEY UNIQ_PAGE_SLUG (slug),
    KEY IDX_PAGE_IMAGE (image_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE page 
    ADD CONSTRAINT FK_PAGE_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL;



-- -----------------------------------------------------
-- POST
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS post (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    image_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    UNIQUE KEY UNIQ_POST_SLUG (slug),
    KEY IDX_POST_IMAGE (image_id),
    KEY IDX_POST_CATEGORY (category_id),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE post 
    ADD CONSTRAINT FK_POST_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL;

ALTER TABLE post 
    ADD CONSTRAINT FK_POST_CATEGORY FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL;



-- -----------------------------------------------------
-- VISIT
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS visit (
    id INT AUTO_INCREMENT NOT NULL,
    visited_at DATETIME NOT NULL,
    page VARCHAR(255) NOT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Source: injectionsql15122025.sql
-- ============================================================
CREATE TABLE merch_category (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  UNIQUE INDEX UNIQ_MERCH_CATEGORY_SLUG (slug),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE merch
  ADD COLUMN category_id INT DEFAULT NULL,
  ADD INDEX IDX_MERCH_CATEGORY_ID (category_id),
  ADD CONSTRAINT FK_MERCH_CATEGORY_ID
    FOREIGN KEY (category_id) REFERENCES merch_category (id)
    ON DELETE SET NULL;

INSERT INTO merch_category (name, slug) VALUES
  ('T-shirt', 't-shirt'),
  ('Polo', 'polo'),
  ('Chemise', 'chemise'),
  ('Pull / Sweat', 'pull-sweat'),
  ('Veste', 'veste'),
  ('Manteau', 'manteau'),
  ('Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef'),
  ('Cartage', 'cartage'),
  ('Echarpe', 'echarpe'),
  ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte ClÃ©, Sac, DVD, Livre)', 'gadget'),
  ('Patch', 'patch'),
  ('Short', 'short'),
  ('Chaussure', 'chaussure'),
  ('Stickers', 'stickers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ============================================================
-- Source: injectionsql151220252.sql
-- ============================================================
-- MySQL / MariaDB (script relanÃ§able)
-- CatÃ©gories Merch + FK + seed + stocks par tailles + fix DateTimeImmutable

-- ----------------------------
-- 1) CatÃ©gories Merch
-- ----------------------------
CREATE TABLE IF NOT EXISTS merch_category (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  UNIQUE KEY UNIQ_MERCH_CATEGORY_SLUG (slug),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB;

-- Colonne category_id si absente
SET @col := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND COLUMN_NAME = 'category_id'
);
SET @sql := IF(@col = 0, 'ALTER TABLE merch ADD COLUMN category_id INT DEFAULT NULL', 'SELECT \"category_id dÃ©jÃ  prÃ©sent\"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index si absent
SET @idx := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND INDEX_NAME = 'IDX_MERCH_CATEGORY_ID'
);
SET @sql := IF(@idx = 0, 'CREATE INDEX IDX_MERCH_CATEGORY_ID ON merch (category_id)', 'SELECT \"index IDX_MERCH_CATEGORY_ID dÃ©jÃ  prÃ©sent\"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK si absente (nom fixÃ©: FK_MERCH_CATEGORY_ID)
SET @fk := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'merch'
    AND CONSTRAINT_NAME = 'FK_MERCH_CATEGORY_ID'
);
SET @sql := IF(
  @fk = 0,
  'ALTER TABLE merch ADD CONSTRAINT FK_MERCH_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES merch_category(id) ON DELETE SET NULL',
  'SELECT \"FK_MERCH_CATEGORY_ID dÃ©jÃ  prÃ©sente\"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Seed catÃ©gories (idempotent)
INSERT INTO merch_category (name, slug) VALUES
  ('T-shirt', 't-shirt'),
  ('Polo', 'polo'),
  ('Chemise', 'chemise'),
  ('Pull / Sweat', 'pull-sweat'),
  ('Veste', 'veste'),
  ('Manteau', 'manteau'),
  ('Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef'),
  ('Cartage', 'cartage'),
  ('Echarpe', 'echarpe'),
  ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte ClÃ©, Sac, DVD, Livre)', 'gadget'),
  ('Patch', 'patch'),
  ('Short', 'short'),
  ('Chaussure', 'chaussure'),
  ('Stickers', 'stickers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ----------------------------
-- 2) Stocks par tailles
-- ----------------------------
CREATE TABLE IF NOT EXISTS merch_stock (
  id INT AUTO_INCREMENT NOT NULL,
  merch_id INT NOT NULL,
  size VARCHAR(30) NOT NULL,
  quantity INT NOT NULL,
  UNIQUE KEY UNIQ_MERCH_STOCK_MERCH_SIZE (merch_id, size),
  KEY IDX_MERCH_STOCK_MERCH (merch_id),
  PRIMARY KEY (id),
  CONSTRAINT FK_MERCH_STOCK_MERCH
    FOREIGN KEY (merch_id) REFERENCES merch(id)
    ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE=InnoDB;

-- ----------------------------
-- 3) Fix DateTimeImmutable (optionnel)
-- ----------------------------
ALTER TABLE media MODIFY updated_at DATETIME NULL;
ALTER TABLE invite_code MODIFY expires_at DATETIME NOT NULL;
ALTER TABLE visit MODIFY visited_at DATETIME NOT NULL;

-- ============================================================
-- Source: injectionsql16032026.sql
-- ============================================================
-- migrations/20260316120000_add_group_page_sections.sql

ALTER TABLE group_page
  ADD histoire_text LONGTEXT DEFAULT NULL,
  ADD mentalite_text LONGTEXT DEFAULT NULL,
  ADD fonctionnement_text LONGTEXT DEFAULT NULL,
  ADD rejoindre_text LONGTEXT DEFAULT NULL,
  ADD se_carter_text LONGTEXT DEFAULT NULL;

-- ============================================================
-- Source: seed_group_page.sql
-- ============================================================
-- Seed default "Le Groupe" page (FR) if table is empty
INSERT INTO group_page (name, description, histoire_text, mentalite_text, fonctionnement_text, rejoindre_text, se_carter_text)
SELECT
    'Le Groupe',
    NULL,
    'Nes en tribune a Fleury, on a bati notre identite match apres match. Rouge et noir, fideles, presents partout.',
    'Fidelite, respect, solidarite. On chante, on pousse, on ne lache rien, a domicile comme en deplacement.',
    'Le groupe tourne grace aux benevoles, aux reunions et aux decisions collectives. Chacun a sa place, chacun met la main.',
    'Pour vivre le match autrement, participer aux tifos et deplacements, et faire partie d''une famille rouge et noire.',
    'Passe a la table de vente les jours de match, recupere ton code, puis inscris-toi pour acceder a l''espace membres.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM group_page);

-- ============================================================
-- Source: Version20250104192616.php
-- ============================================================
CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
;

CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
;


-- ============================================================
-- Source: Version20251201212207.php
-- ============================================================
CREATE TABLE users (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                pseudo VARCHAR(50) NOT NULL,
                photo_profil VARCHAR(255) DEFAULT NULL,
                taille_veste VARCHAR(10) DEFAULT NULL,
                taille_short VARCHAR(10) DEFAULT NULL,
                banniere_profil VARCHAR(255) DEFAULT NULL,
                UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE invite_code (
                id INT AUTO_INCREMENT NOT NULL,
                code VARCHAR(100) NOT NULL,
                used TINYINT(1) NOT NULL,
                expires_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_6F21F11277153098 (code),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE site_config (
                id INT AUTO_INCREMENT NOT NULL,
                site_name VARCHAR(255) DEFAULT NULL,
                hero_title VARCHAR(255) DEFAULT NULL,
                hero_subtitle LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE category (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(150) NOT NULL,
                UNIQUE INDEX UNIQ_CATEGORY_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE gallery (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE group_page (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(120) NOT NULL,
                description LONGTEXT DEFAULT NULL,
                logo_id INT DEFAULT NULL,
                banner_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_GROUPPAGE_LOGO (logo_id),
                INDEX IDX_GROUPPAGE_BANNER (banner_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE media (
                id INT AUTO_INCREMENT NOT NULL,
                path VARCHAR(255) DEFAULT NULL,
                alt VARCHAR(255) DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                gallery_id INT DEFAULT NULL,
                group_page_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_MEDIA_GALLERY (gallery_id),
                INDEX IDX_MEDIA_GROUPPAGE (group_page_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE media ADD CONSTRAINT FK_MEDIA_GALLERY FOREIGN KEY (gallery_id) REFERENCES gallery(id) ON DELETE SET NULL
;

ALTER TABLE media ADD CONSTRAINT FK_MEDIA_GROUPPAGE FOREIGN KEY (group_page_id) REFERENCES group_page(id) ON DELETE SET NULL
;

ALTER TABLE group_page ADD CONSTRAINT FK_GROUPPAGE_LOGO FOREIGN KEY (logo_id) REFERENCES media(id) ON DELETE SET NULL
;

ALTER TABLE group_page ADD CONSTRAINT FK_GROUPPAGE_BANNER FOREIGN KEY (banner_id) REFERENCES media(id) ON DELETE SET NULL
;

CREATE TABLE event (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                date DATETIME NOT NULL,
                description LONGTEXT DEFAULT NULL,
                image_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_EVENT_IMAGE (image_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE event ADD CONSTRAINT FK_EVENT_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
;

CREATE TABLE merch (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                description LONGTEXT DEFAULT NULL,
                image_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_MERCH_IMAGE (image_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE merch ADD CONSTRAINT FK_MERCH_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
;

CREATE TABLE page (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                slug VARCHAR(180) NOT NULL,
                content LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                image_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                UNIQUE INDEX UNIQ_PAGE_SLUG (slug),
                INDEX IDX_PAGE_IMAGE (image_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE page ADD CONSTRAINT FK_PAGE_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
;

CREATE TABLE post (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                slug VARCHAR(180) NOT NULL,
                content LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                image_id INT DEFAULT NULL,
                category_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                UNIQUE INDEX UNIQ_POST_SLUG (slug),
                INDEX IDX_POST_IMAGE (image_id),
                INDEX IDX_POST_CATEGORY (category_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE post ADD CONSTRAINT FK_POST_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
;

ALTER TABLE post ADD CONSTRAINT FK_POST_CATEGORY FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
;

CREATE TABLE visit (
                id INT AUTO_INCREMENT NOT NULL,
                visited_at DATETIME NOT NULL,
                page VARCHAR(255) NOT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;


-- ============================================================
-- Source: Version20251202215857.php
-- ============================================================
CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, visited_at DATETIME NOT NULL, page VARCHAR(255) NOT NULL, ip VARCHAR(45) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

ALTER TABLE category RENAME INDEX uniq_category_slug TO UNIQ_64C19C1989D9B62
;

ALTER TABLE event RENAME INDEX idx_event_image TO IDX_3BAE0AA73DA5256D
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_logo TO IDX_3D50ED50F98F144A
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_banner TO IDX_3D50ED50684EC833
;

ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL
;

ALTER TABLE media RENAME INDEX idx_media_gallery TO IDX_6A2CA10C4E7AF8F
;

ALTER TABLE media RENAME INDEX idx_media_grouppage TO IDX_6A2CA10C8A4637E8
;

ALTER TABLE merch RENAME INDEX idx_merch_image TO IDX_F1B42EE03DA5256D
;

ALTER TABLE page RENAME INDEX uniq_page_slug TO UNIQ_140AB620989D9B62
;

ALTER TABLE page RENAME INDEX idx_page_image TO IDX_140AB6203DA5256D
;

ALTER TABLE post RENAME INDEX uniq_post_slug TO UNIQ_5A8A6C8D989D9B62
;

ALTER TABLE post RENAME INDEX idx_post_image TO IDX_5A8A6C8D3DA5256D
;

ALTER TABLE post RENAME INDEX idx_post_category TO IDX_5A8A6C8D12469DE2
;

ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_queue TO IDX_75EA56E0FB7336F0
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_available TO IDX_75EA56E0E3BD61CE
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_delivered TO IDX_75EA56E016BA31DB
;


-- ============================================================
-- Source: Version20251202220409.php
-- ============================================================
CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, visited_at DATETIME NOT NULL, page VARCHAR(255) NOT NULL, ip VARCHAR(45) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

ALTER TABLE category RENAME INDEX uniq_category_slug TO UNIQ_64C19C1989D9B62
;

ALTER TABLE event RENAME INDEX idx_event_image TO IDX_3BAE0AA73DA5256D
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_logo TO IDX_3D50ED50F98F144A
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_banner TO IDX_3D50ED50684EC833
;

ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL
;

ALTER TABLE media RENAME INDEX idx_media_gallery TO IDX_6A2CA10C4E7AF8F
;

ALTER TABLE media RENAME INDEX idx_media_grouppage TO IDX_6A2CA10C8A4637E8
;

ALTER TABLE merch RENAME INDEX idx_merch_image TO IDX_F1B42EE03DA5256D
;

ALTER TABLE page RENAME INDEX uniq_page_slug TO UNIQ_140AB620989D9B62
;

ALTER TABLE page RENAME INDEX idx_page_image TO IDX_140AB6203DA5256D
;

ALTER TABLE post RENAME INDEX uniq_post_slug TO UNIQ_5A8A6C8D989D9B62
;

ALTER TABLE post RENAME INDEX idx_post_image TO IDX_5A8A6C8D3DA5256D
;

ALTER TABLE post RENAME INDEX idx_post_category TO IDX_5A8A6C8D12469DE2
;

ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_queue TO IDX_75EA56E0FB7336F0
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_available TO IDX_75EA56E0E3BD61CE
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_delivered TO IDX_75EA56E016BA31DB
;


-- ============================================================
-- Source: Version20251206013450.php
-- ============================================================
CREATE TABLE invite_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, used TINYINT(1) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6F21F11277153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo_profil VARCHAR(255) DEFAULT NULL, taille_veste VARCHAR(10) DEFAULT NULL, taille_short VARCHAR(10) DEFAULT NULL, banniere_profil VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

ALTER TABLE category RENAME INDEX uniq_category_slug TO UNIQ_64C19C1989D9B62
;

ALTER TABLE event RENAME INDEX idx_event_image TO IDX_3BAE0AA73DA5256D
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_logo TO IDX_3D50ED50F98F144A
;

ALTER TABLE group_page RENAME INDEX idx_grouppage_banner TO IDX_3D50ED50684EC833
;

ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL
;

ALTER TABLE media RENAME INDEX idx_media_gallery TO IDX_6A2CA10C4E7AF8F
;

ALTER TABLE media RENAME INDEX idx_media_grouppage TO IDX_6A2CA10C8A4637E8
;

ALTER TABLE merch RENAME INDEX idx_merch_image TO IDX_F1B42EE03DA5256D
;

ALTER TABLE page RENAME INDEX uniq_page_slug TO UNIQ_140AB620989D9B62
;

ALTER TABLE page RENAME INDEX idx_page_image TO IDX_140AB6203DA5256D
;

ALTER TABLE post RENAME INDEX uniq_post_slug TO UNIQ_5A8A6C8D989D9B62
;

ALTER TABLE post RENAME INDEX idx_post_image TO IDX_5A8A6C8D3DA5256D
;

ALTER TABLE post RENAME INDEX idx_post_category TO IDX_5A8A6C8D12469DE2
;

ALTER TABLE visit CHANGE ip ip VARCHAR(45) DEFAULT NULL
;

ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_queue TO IDX_75EA56E0FB7336F0
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_available TO IDX_75EA56E0E3BD61CE
;

ALTER TABLE messenger_messages RENAME INDEX idx_messenger_delivered TO IDX_75EA56E016BA31DB
;


-- ============================================================
-- Source: Version20251215120000.php
-- ============================================================
CREATE TABLE merch_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_MERCH_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
;

ALTER TABLE merch ADD category_id INT DEFAULT NULL
;

ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE012469DE2 FOREIGN KEY (category_id) REFERENCES merch_category (id) ON DELETE SET NULL
;

CREATE INDEX IDX_F1B42EE012469DE2 ON merch (category_id)
;

INSERT INTO merch_category (name, slug) VALUES ('T-shirt', 't-shirt')
;

INSERT INTO merch_category (name, slug) VALUES ('Polo', 'polo')
;

INSERT INTO merch_category (name, slug) VALUES ('Chemise', 'chemise')
;

INSERT INTO merch_category (name, slug) VALUES ('Pull / Sweat', 'pull-sweat')
;

INSERT INTO merch_category (name, slug) VALUES ('Veste', 'veste')
;

INSERT INTO merch_category (name, slug) VALUES ('Manteau', 'manteau')
;

INSERT INTO merch_category (name, slug) VALUES ('Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef')
;

INSERT INTO merch_category (name, slug) VALUES ('Cartage', 'cartage')
;

INSERT INTO merch_category (name, slug) VALUES ('Echarpe', 'echarpe')
;

INSERT INTO merch_category (name, slug) VALUES ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte Cle, Sac, DVD, Livre)', 'gadget')
;

INSERT INTO merch_category (name, slug) VALUES ('Patch', 'patch')
;

INSERT INTO merch_category (name, slug) VALUES ('Short', 'short')
;

INSERT INTO merch_category (name, slug) VALUES ('Chaussure', 'chaussure')
;

INSERT INTO merch_category (name, slug) VALUES ('Stickers', 'stickers')
;


-- ============================================================
-- Source: Version20251215124500.php
-- ============================================================
CREATE TABLE merch_stock (id INT AUTO_INCREMENT NOT NULL, merch_id INT NOT NULL, size VARCHAR(30) NOT NULL, quantity INT NOT NULL, INDEX IDX_79B2DF2DE4F9D9E (merch_id), UNIQUE INDEX UNIQ_MERCH_STOCK_MERCH_SIZE (merch_id, size), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
;

ALTER TABLE merch_stock ADD CONSTRAINT FK_79B2DF2DE4F9D9E FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE
;


-- ============================================================
-- Source: Version20251216120000.php
-- ============================================================
CREATE TABLE ticket_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_TICKET_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
;

CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, title VARCHAR(180) NOT NULL, opponent VARCHAR(180) NOT NULL, match_date DATETIME NOT NULL, venue VARCHAR(180) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, stock INT NOT NULL, INDEX IDX_97A0ADA43DA5256D (image_id), INDEX IDX_97A0ADA412469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
;

ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA43DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA412469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id) ON DELETE SET NULL
;

INSERT INTO ticket_category (name, slug) VALUES ('Domicile', 'domicile')
;

INSERT INTO ticket_category (name, slug) VALUES ('ExtÃ©rieur', 'exterieur')
;


-- ============================================================
-- Source: Version20260213104945.php
-- ============================================================
CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, date DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, image_id INT DEFAULT NULL, INDEX IDX_3BAE0AA73DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE group_page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, logo_id INT DEFAULT NULL, banner_id INT DEFAULT NULL, INDEX IDX_3D50ED50F98F144A (logo_id), INDEX IDX_3D50ED50684EC833 (banner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE invite_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, used TINYINT NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6F21F11277153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, gallery_id INT DEFAULT NULL, group_page_id INT DEFAULT NULL, INDEX IDX_6A2CA10C4E7AF8F (gallery_id), INDEX IDX_6A2CA10C8A4637E8 (group_page_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE merch (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_F1B42EE03DA5256D (image_id), INDEX IDX_F1B42EE012469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE merch_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX uniq_merch_category_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE merch_stock (id INT AUTO_INCREMENT NOT NULL, size VARCHAR(30) NOT NULL, quantity INT NOT NULL, merch_id INT NOT NULL, INDEX IDX_8FADF0318A86BD8 (merch_id), UNIQUE INDEX uniq_merch_stock_merch_size (merch_id, size), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, image_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), INDEX IDX_140AB6203DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8D3DA5256D (image_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, opponent VARCHAR(180) NOT NULL, match_date DATETIME NOT NULL, venue VARCHAR(180) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, stock INT NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_97A0ADA33DA5256D (image_id), INDEX IDX_97A0ADA312469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE ticket_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX uniq_ticket_category_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo_profil VARCHAR(255) DEFAULT NULL, nom VARCHAR(100) DEFAULT NULL, prenom VARCHAR(100) DEFAULT NULL, date_naissance DATE DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, code_postal VARCHAR(12) DEFAULT NULL, taille_tshirt VARCHAR(10) DEFAULT NULL, taille_polo VARCHAR(10) DEFAULT NULL, taille_pull VARCHAR(10) DEFAULT NULL, taille_sweat VARCHAR(10) DEFAULT NULL, taille_veste VARCHAR(10) DEFAULT NULL, taille_short VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, visited_at DATETIME NOT NULL, page VARCHAR(255) NOT NULL, ip VARCHAR(45) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4
;

ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA73DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE group_page ADD CONSTRAINT FK_3D50ED50F98F144A FOREIGN KEY (logo_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE group_page ADD CONSTRAINT FK_3D50ED50684EC833 FOREIGN KEY (banner_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL
;

ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C8A4637E8 FOREIGN KEY (group_page_id) REFERENCES group_page (id) ON DELETE SET NULL
;

ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE03DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE012469DE2 FOREIGN KEY (category_id) REFERENCES merch_category (id) ON DELETE SET NULL
;

ALTER TABLE merch_stock ADD CONSTRAINT FK_8FADF0318A86BD8 FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE
;

ALTER TABLE page ADD CONSTRAINT FK_140AB6203DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL
;

ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL
;

ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA312469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id) ON DELETE SET NULL
;


-- ============================================================
-- Source: Version20260311120000.php
-- ============================================================
ALTER TABLE media ADD merch_id INT DEFAULT NULL
;

CREATE INDEX IDX_6A2CA10C56A273CC ON media (merch_id)
;

ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C56A273CC FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE SET NULL
;


-- ============================================================
-- Source: Version20260311123000.php
-- ============================================================
ALTER TABLE media ADD event_id INT DEFAULT NULL
;

CREATE INDEX IDX_6A2CA10C71F7E88B ON media (event_id)
;

ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE SET NULL
;


-- ============================================================
-- Source: Version20260316120000.php
-- ============================================================
ALTER TABLE group_page ADD histoire_text LONGTEXT DEFAULT NULL
;

ALTER TABLE group_page ADD mentalite_text LONGTEXT DEFAULT NULL
;

ALTER TABLE group_page ADD fonctionnement_text LONGTEXT DEFAULT NULL
;

ALTER TABLE group_page ADD rejoindre_text LONGTEXT DEFAULT NULL
;

ALTER TABLE group_page ADD se_carter_text LONGTEXT DEFAULT NULL
;


-- ============================================================
-- Source: Version20260408120000.php
-- ============================================================
ALTER TABLE gallery ADD description LONGTEXT DEFAULT NULL
;


-- ============================================================
-- Source: Version20260408123000.php
-- ============================================================
INSERT INTO group_page (name, description, histoire_text, mentalite_text, fonctionnement_text, rejoindre_text, se_carter_text)
SELECT
    'Le Groupe',
    NULL,
    'Nes en tribune a Fleury, on a bati notre identite match apres match. Rouge et noir, fideles, presents partout.',
    'Fidelite, respect, solidarite. On chante, on pousse, on ne lache rien, a domicile comme en deplacement.',
    'Le groupe tourne grace aux benevoles, aux reunions et aux decisions collectives. Chacun a sa place, chacun met la main.',
    'Pour vivre le match autrement, participer aux tifos et deplacements, et faire partie d''une famille rouge et noire.',
    'Passe a la table de vente les jours de match, recupere ton code, puis inscris-toi pour acceder a l''espace membres.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM group_page);


-- ============================================================
-- Source: Version20260411120000.php
-- ============================================================
CREATE TABLE chant (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, lyrics LONGTEXT DEFAULT NULL, audio_path VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
;


