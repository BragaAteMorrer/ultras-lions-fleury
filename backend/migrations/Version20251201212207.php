<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251201212207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for Ultras Lions website (FULL — with users, invite_code, site_config)';
    }

    public function up(Schema $schema): void
    {
        // -----------------------------
        // USERS
        // -----------------------------
        $this->addSql('
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
        ');

        // -----------------------------
        // INVITE CODE
        // -----------------------------
        $this->addSql('
            CREATE TABLE invite_code (
                id INT AUTO_INCREMENT NOT NULL,
                code VARCHAR(100) NOT NULL,
                used TINYINT(1) NOT NULL,
                expires_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_6F21F11277153098 (code),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // -----------------------------
        // SITE CONFIG
        // -----------------------------
        $this->addSql('
            CREATE TABLE site_config (
                id INT AUTO_INCREMENT NOT NULL,
                site_name VARCHAR(255) DEFAULT NULL,
                hero_title VARCHAR(255) DEFAULT NULL,
                hero_subtitle LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // -----------------------------
        // CATEGORY
        // -----------------------------
        $this->addSql('
            CREATE TABLE category (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(150) NOT NULL,
                UNIQUE INDEX UNIQ_CATEGORY_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // -----------------------------
        // GALLERY
        // -----------------------------
        $this->addSql('
            CREATE TABLE gallery (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // -----------------------------
        // GROUP PAGE
        // -----------------------------
        $this->addSql('
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
        ');

        // -----------------------------
        // MEDIA
        // -----------------------------
        $this->addSql('
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
        ');

        // FKS MEDIA
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_MEDIA_GALLERY FOREIGN KEY (gallery_id) REFERENCES gallery(id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_MEDIA_GROUPPAGE FOREIGN KEY (group_page_id) REFERENCES group_page(id) ON DELETE SET NULL');

        // FKS GROUP_PAGE
        $this->addSql('ALTER TABLE group_page ADD CONSTRAINT FK_GROUPPAGE_LOGO FOREIGN KEY (logo_id) REFERENCES media(id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE group_page ADD CONSTRAINT FK_GROUPPAGE_BANNER FOREIGN KEY (banner_id) REFERENCES media(id) ON DELETE SET NULL');

        // -----------------------------
        // EVENT
        // -----------------------------
        $this->addSql('
            CREATE TABLE event (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                date DATETIME NOT NULL,
                description LONGTEXT DEFAULT NULL,
                image_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_EVENT_IMAGE (image_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_EVENT_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL');

        // -----------------------------
        // MERCH
        // -----------------------------
        $this->addSql('
            CREATE TABLE merch (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(180) NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                description LONGTEXT DEFAULT NULL,
                image_id INT DEFAULT NULL,
                PRIMARY KEY(id),
                INDEX IDX_MERCH_IMAGE (image_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
        $this->addSql('ALTER TABLE merch ADD CONSTRAINT FK_MERCH_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL');

        // -----------------------------
        // PAGE
        // -----------------------------
        $this->addSql('
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
        ');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_PAGE_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL');

        // -----------------------------
        // POST
        // -----------------------------
        $this->addSql('
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
        ');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_POST_IMAGE FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_POST_CATEGORY FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL');

        // -----------------------------
        // VISIT
        // -----------------------------
        $this->addSql('
            CREATE TABLE visit (
                id INT AUTO_INCREMENT NOT NULL,
                visited_at DATETIME NOT NULL,
                page VARCHAR(255) NOT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE visit');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE merch');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE group_page');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE site_config');
        $this->addSql('DROP TABLE invite_code');
        $this->addSql('DROP TABLE users');
    }
}
