SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- USERS
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,

    -- Mot de passe hashé
    password VARCHAR(255) NOT NULL,

    -- Identité
    pseudo VARCHAR(50) NOT NULL,
    nom VARCHAR(100) DEFAULT NULL,
    prenom VARCHAR(100) DEFAULT NULL,
    date_naissance DATE DEFAULT NULL,

    -- Coordonnées
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    code_postal VARCHAR(12) DEFAULT NULL,

    -- Photos
    photo_profil VARCHAR(255) DEFAULT NULL,
    banniere_profil VARCHAR(255) DEFAULT NULL,

    -- Tailles vêtements
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
