<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206013450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invite_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, used TINYINT(1) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6F21F11277153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo_profil VARCHAR(255) DEFAULT NULL, taille_veste VARCHAR(10) DEFAULT NULL, taille_short VARCHAR(10) DEFAULT NULL, banniere_profil VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE category RENAME INDEX uniq_category_slug TO UNIQ_64C19C1989D9B62');
        $this->addSql('ALTER TABLE event RENAME INDEX idx_event_image TO IDX_3BAE0AA73DA5256D');
        $this->addSql('ALTER TABLE group_page RENAME INDEX idx_grouppage_logo TO IDX_3D50ED50F98F144A');
        $this->addSql('ALTER TABLE group_page RENAME INDEX idx_grouppage_banner TO IDX_3D50ED50684EC833');
        $this->addSql('ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE media RENAME INDEX idx_media_gallery TO IDX_6A2CA10C4E7AF8F');
        $this->addSql('ALTER TABLE media RENAME INDEX idx_media_grouppage TO IDX_6A2CA10C8A4637E8');
        $this->addSql('ALTER TABLE merch RENAME INDEX idx_merch_image TO IDX_F1B42EE03DA5256D');
        $this->addSql('ALTER TABLE page RENAME INDEX uniq_page_slug TO UNIQ_140AB620989D9B62');
        $this->addSql('ALTER TABLE page RENAME INDEX idx_page_image TO IDX_140AB6203DA5256D');
        $this->addSql('ALTER TABLE post RENAME INDEX uniq_post_slug TO UNIQ_5A8A6C8D989D9B62');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_post_image TO IDX_5A8A6C8D3DA5256D');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_post_category TO IDX_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE visit CHANGE ip ip VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_messenger_queue TO IDX_75EA56E0FB7336F0');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_messenger_available TO IDX_75EA56E0E3BD61CE');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_messenger_delivered TO IDX_75EA56E016BA31DB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invite_code');
        $this->addSql('DROP TABLE site_config');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE category RENAME INDEX uniq_64c19c1989d9b62 TO UNIQ_CATEGORY_SLUG');
        $this->addSql('ALTER TABLE event RENAME INDEX idx_3bae0aa73da5256d TO IDX_EVENT_IMAGE');
        $this->addSql('ALTER TABLE group_page RENAME INDEX idx_3d50ed50f98f144a TO IDX_GROUPPAGE_LOGO');
        $this->addSql('ALTER TABLE group_page RENAME INDEX idx_3d50ed50684ec833 TO IDX_GROUPPAGE_BANNER');
        $this->addSql('ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT \'NULL\', CHANGE alt alt VARCHAR(255) DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE media RENAME INDEX idx_6a2ca10c8a4637e8 TO IDX_MEDIA_GROUPPAGE');
        $this->addSql('ALTER TABLE media RENAME INDEX idx_6a2ca10c4e7af8f TO IDX_MEDIA_GALLERY');
        $this->addSql('ALTER TABLE merch RENAME INDEX idx_f1b42ee03da5256d TO IDX_MERCH_IMAGE');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e0fb7336f0 TO IDX_MESSENGER_QUEUE');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e0e3bd61ce TO IDX_MESSENGER_AVAILABLE');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e016ba31db TO IDX_MESSENGER_DELIVERED');
        $this->addSql('ALTER TABLE page RENAME INDEX idx_140ab6203da5256d TO IDX_PAGE_IMAGE');
        $this->addSql('ALTER TABLE page RENAME INDEX uniq_140ab620989d9b62 TO UNIQ_PAGE_SLUG');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_5a8a6c8d3da5256d TO IDX_POST_IMAGE');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_5a8a6c8d12469de2 TO IDX_POST_CATEGORY');
        $this->addSql('ALTER TABLE post RENAME INDEX uniq_5a8a6c8d989d9b62 TO UNIQ_POST_SLUG');
        $this->addSql('ALTER TABLE visit CHANGE ip ip VARCHAR(45) DEFAULT \'NULL\'');
    }
}
