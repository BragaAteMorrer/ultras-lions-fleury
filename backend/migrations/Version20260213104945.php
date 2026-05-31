<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260213104945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, date DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, image_id INT DEFAULT NULL, INDEX IDX_3BAE0AA73DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE group_page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, logo_id INT DEFAULT NULL, banner_id INT DEFAULT NULL, INDEX IDX_3D50ED50F98F144A (logo_id), INDEX IDX_3D50ED50684EC833 (banner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE invite_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, used TINYINT NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6F21F11277153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, gallery_id INT DEFAULT NULL, group_page_id INT DEFAULT NULL, INDEX IDX_6A2CA10C4E7AF8F (gallery_id), INDEX IDX_6A2CA10C8A4637E8 (group_page_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE merch (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_F1B42EE03DA5256D (image_id), INDEX IDX_F1B42EE012469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE merch_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX uniq_merch_category_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE merch_stock (id INT AUTO_INCREMENT NOT NULL, size VARCHAR(30) NOT NULL, quantity INT NOT NULL, merch_id INT NOT NULL, INDEX IDX_8FADF0318A86BD8 (merch_id), UNIQUE INDEX uniq_merch_stock_merch_size (merch_id, size), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, image_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), INDEX IDX_140AB6203DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8D3DA5256D (image_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, opponent VARCHAR(180) NOT NULL, match_date DATETIME NOT NULL, venue VARCHAR(180) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, stock INT NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_97A0ADA33DA5256D (image_id), INDEX IDX_97A0ADA312469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX uniq_ticket_category_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo_profil VARCHAR(255) DEFAULT NULL, nom VARCHAR(100) DEFAULT NULL, prenom VARCHAR(100) DEFAULT NULL, date_naissance DATE DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, code_postal VARCHAR(12) DEFAULT NULL, taille_tshirt VARCHAR(10) DEFAULT NULL, taille_polo VARCHAR(10) DEFAULT NULL, taille_pull VARCHAR(10) DEFAULT NULL, taille_sweat VARCHAR(10) DEFAULT NULL, taille_veste VARCHAR(10) DEFAULT NULL, taille_short VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, visited_at DATETIME NOT NULL, page VARCHAR(255) NOT NULL, ip VARCHAR(45) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA73DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE group_page ADD CONSTRAINT FK_3D50ED50F98F144A FOREIGN KEY (logo_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE group_page ADD CONSTRAINT FK_3D50ED50684EC833 FOREIGN KEY (banner_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C8A4637E8 FOREIGN KEY (group_page_id) REFERENCES group_page (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE03DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE012469DE2 FOREIGN KEY (category_id) REFERENCES merch_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE merch_stock ADD CONSTRAINT FK_8FADF0318A86BD8 FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6203DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA312469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA73DA5256D');
        $this->addSql('ALTER TABLE group_page DROP FOREIGN KEY FK_3D50ED50F98F144A');
        $this->addSql('ALTER TABLE group_page DROP FOREIGN KEY FK_3D50ED50684EC833');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4E7AF8F');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C8A4637E8');
        $this->addSql('ALTER TABLE merch DROP FOREIGN KEY FK_F1B42EE03DA5256D');
        $this->addSql('ALTER TABLE merch DROP FOREIGN KEY FK_F1B42EE012469DE2');
        $this->addSql('ALTER TABLE merch_stock DROP FOREIGN KEY FK_8FADF0318A86BD8');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6203DA5256D');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3DA5256D');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA33DA5256D');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA312469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE group_page');
        $this->addSql('DROP TABLE invite_code');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE merch');
        $this->addSql('DROP TABLE merch_category');
        $this->addSql('DROP TABLE merch_stock');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE site_config');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_category');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visit');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
