-- Final consolidated schema for Ultras Lions
-- Generated from backend/migrations, ordered to be executable on MySQL/MariaDB.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS chant;
DROP TABLE IF EXISTS ticket;
DROP TABLE IF EXISTS ticket_category;
DROP TABLE IF EXISTS merch_stock;
DROP TABLE IF EXISTS post;
DROP TABLE IF EXISTS page;
DROP TABLE IF EXISTS event;
DROP TABLE IF EXISTS merch;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS group_page;
DROP TABLE IF EXISTS gallery;
DROP TABLE IF EXISTS event_category;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS merch_category;
DROP TABLE IF EXISTS site_config;
DROP TABLE IF EXISTS invite_code;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS visit;
DROP TABLE IF EXISTS messenger_messages;
DROP TABLE IF EXISTS `user`;

CREATE TABLE users (
    id INT AUTO_INCREMENT NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    photo_profil VARCHAR(255) DEFAULT NULL,
    nom VARCHAR(100) DEFAULT NULL,
    prenom VARCHAR(100) DEFAULT NULL,
    date_naissance DATE DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    code_postal VARCHAR(12) DEFAULT NULL,
    banniere_profil VARCHAR(255) DEFAULT NULL,
    taille_tshirt VARCHAR(10) DEFAULT NULL,
    taille_polo VARCHAR(10) DEFAULT NULL,
    taille_pull VARCHAR(10) DEFAULT NULL,
    taille_sweat VARCHAR(10) DEFAULT NULL,
    taille_veste VARCHAR(10) DEFAULT NULL,
    taille_short VARCHAR(10) DEFAULT NULL,
    UNIQUE KEY UNIQ_1483A5E9E7927C74 (email),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invite_code (
    id INT AUTO_INCREMENT NOT NULL,
    code VARCHAR(100) NOT NULL,
    used TINYINT(1) NOT NULL,
    expires_at DATETIME NOT NULL,
    UNIQUE KEY UNIQ_6F21F11277153098 (code),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_config (
    id INT AUTO_INCREMENT NOT NULL,
    site_name VARCHAR(255) DEFAULT NULL,
    hero_title VARCHAR(255) DEFAULT NULL,
    hero_subtitle LONGTEXT DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    UNIQUE KEY UNIQ_64C19C1989D9B62 (slug),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    section VARCHAR(40) NOT NULL,
    UNIQUE KEY UNIQ_EVENT_CATEGORY_SLUG (slug),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE merch_category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    UNIQUE KEY UNIQ_MERCH_CATEGORY_SLUG (slug),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_category (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    UNIQUE KEY UNIQ_TICKET_CATEGORY_SLUG (slug),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gallery (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE group_page (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(120) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    histoire_text LONGTEXT DEFAULT NULL,
    mentalite_text LONGTEXT DEFAULT NULL,
    fonctionnement_text LONGTEXT DEFAULT NULL,
    rejoindre_text LONGTEXT DEFAULT NULL,
    se_carter_text LONGTEXT DEFAULT NULL,
    logo_id INT DEFAULT NULL,
    banner_id INT DEFAULT NULL,
    KEY IDX_3D50ED50F98F144A (logo_id),
    KEY IDX_3D50ED50684EC833 (banner_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    id INT AUTO_INCREMENT NOT NULL,
    path VARCHAR(255) DEFAULT NULL,
    alt VARCHAR(255) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    gallery_id INT DEFAULT NULL,
    group_page_id INT DEFAULT NULL,
    merch_id INT DEFAULT NULL,
    event_id INT DEFAULT NULL,
    post_id INT DEFAULT NULL,
    KEY IDX_6A2CA10C4E7AF8F (gallery_id),
    KEY IDX_6A2CA10C8A4637E8 (group_page_id),
    KEY IDX_6A2CA10C56A273CC (merch_id),
    KEY IDX_6A2CA10C71F7E88B (event_id),
    KEY IDX_MEDIA_POST_ID (post_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    date DATETIME NOT NULL,
    description LONGTEXT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    opponent VARCHAR(180) DEFAULT NULL,
    match_location VARCHAR(12) DEFAULT NULL,
    season VARCHAR(9) DEFAULT NULL,
    journee INT DEFAULT NULL,
    image_id INT DEFAULT NULL,
    KEY IDX_EVENT_CATEGORY_ID (category_id),
    KEY IDX_3BAE0AA73DA5256D (image_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE merch (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    audience VARCHAR(20) NOT NULL DEFAULT 'public',
    description LONGTEXT DEFAULT NULL,
    image_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    KEY IDX_F1B42EE03DA5256D (image_id),
    KEY IDX_F1B42EE012469DE2 (category_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE merch_stock (
    id INT AUTO_INCREMENT NOT NULL,
    merch_id INT NOT NULL,
    size VARCHAR(30) NOT NULL,
    quantity INT NOT NULL,
    UNIQUE KEY UNIQ_MERCH_STOCK_MERCH_SIZE (merch_id, size),
    KEY IDX_79B2DF2DE4F9D9E (merch_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE page (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    image_id INT DEFAULT NULL,
    UNIQUE KEY UNIQ_140AB620989D9B62 (slug),
    KEY IDX_140AB6203DA5256D (image_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    image_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    UNIQUE KEY UNIQ_5A8A6C8D989D9B62 (slug),
    KEY IDX_5A8A6C8D3DA5256D (image_id),
    KEY IDX_POST_EVENT_CATEGORY_ID (category_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket (
    id INT AUTO_INCREMENT NOT NULL,
    image_id INT DEFAULT NULL,
    category_id INT DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    opponent VARCHAR(180) NOT NULL,
    match_date DATETIME NOT NULL,
    match_location VARCHAR(20) DEFAULT NULL,
    venue VARCHAR(180) DEFAULT NULL,
    price DOUBLE PRECISION NOT NULL,
    description LONGTEXT DEFAULT NULL,
    stock INT NOT NULL,
    KEY IDX_97A0ADA43DA5256D (image_id),
    KEY IDX_97A0ADA412469DE2 (category_id),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE visit (
    id INT AUTO_INCREMENT NOT NULL,
    visited_at DATETIME NOT NULL,
    page VARCHAR(255) NOT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messenger_messages (
    id BIGINT AUTO_INCREMENT NOT NULL,
    body LONGTEXT NOT NULL,
    headers LONGTEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    available_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    KEY IDX_75EA56E0FB7336F0 (queue_name),
    KEY IDX_75EA56E0E3BD61CE (available_at),
    KEY IDX_75EA56E016BA31DB (delivered_at),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chant (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(180) NOT NULL,
    lyrics LONGTEXT DEFAULT NULL,
    audio_path VARCHAR(255) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE event
    ADD CONSTRAINT FK_EVENT_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES event_category (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_3BAE0AA73DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL;

ALTER TABLE group_page
    ADD CONSTRAINT FK_3D50ED50F98F144A FOREIGN KEY (logo_id) REFERENCES media (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_3D50ED50684EC833 FOREIGN KEY (banner_id) REFERENCES media (id) ON DELETE SET NULL;

ALTER TABLE media
    ADD CONSTRAINT FK_6A2CA10C4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_6A2CA10C8A4637E8 FOREIGN KEY (group_page_id) REFERENCES group_page (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_6A2CA10C56A273CC FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_6A2CA10C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_MEDIA_POST_ID FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE SET NULL;

ALTER TABLE merch
    ADD CONSTRAINT FK_F1B42EE03DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_F1B42EE012469DE2 FOREIGN KEY (category_id) REFERENCES merch_category (id) ON DELETE SET NULL;

ALTER TABLE merch_stock
    ADD CONSTRAINT FK_79B2DF2DE4F9D9E FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE;

ALTER TABLE page
    ADD CONSTRAINT FK_140AB6203DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL;

ALTER TABLE post
    ADD CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_POST_EVENT_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES event_category (id) ON DELETE SET NULL;

ALTER TABLE ticket
    ADD CONSTRAINT FK_97A0ADA43DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL,
    ADD CONSTRAINT FK_97A0ADA412469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id) ON DELETE SET NULL;

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
    ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte Cle, Sac, DVD, Livre)', 'gadget'),
    ('Patch', 'patch'),
    ('Short', 'short'),
    ('Chaussure', 'chaussure'),
    ('Stickers', 'stickers');

INSERT INTO event_category (name, slug, section) VALUES
    ('Articles', 'articles', 'articles'),
    ('Photos de match', 'photos-de-match', 'photos_de_match'),
    ('Evenements', 'evenements', 'evenements'),
    ('Medias', 'medias', 'medias');

INSERT INTO ticket_category (name, slug) VALUES
    ('Domicile', 'domicile'),
    ('Exterieur', 'exterieur');

INSERT INTO group_page (
    name,
    description,
    histoire_text,
    mentalite_text,
    fonctionnement_text,
    rejoindre_text,
    se_carter_text
) VALUES (
    'Le Groupe',
    NULL,
    'Nes en tribune a Fleury, on a bati notre identite match apres match. Rouge et noir, fideles, presents partout.',
    'Fidelite, respect, solidarite. On chante, on pousse, on ne lache rien, a domicile comme en deplacement.',
    'Le groupe tourne grace aux benevoles, aux reunions et aux decisions collectives. Chacun a sa place, chacun met la main.',
    'Pour vivre le match autrement, participer aux tifos et deplacements, et faire partie d''une famille rouge et noire.',
    'Passe a la table de vente les jours de match, recupere ton code, puis inscris-toi pour acceder a l''espace membres.'
);

SET FOREIGN_KEY_CHECKS = 1;
