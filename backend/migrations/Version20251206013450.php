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
    private function renameIndexIfPresent(string $table, string $from, string $to): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist([$table])) { return; }
        $indexes = array_change_key_case($schemaManager->listTableIndexes($table), CASE_LOWER);
        if (isset($indexes[strtolower($from)]) && !isset($indexes[strtolower($to)])) {
            $this->addSql(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to));
        }
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS invite_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, used TINYINT(1) NOT NULL, expires_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6F21F11277153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS site_config (id INT AUTO_INCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo_profil VARCHAR(255) DEFAULT NULL, taille_veste VARCHAR(10) DEFAULT NULL, taille_short VARCHAR(10) DEFAULT NULL, banniere_profil VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->renameIndexIfPresent('category', 'uniq_category_slug', 'UNIQ_64C19C1989D9B62');
        $this->renameIndexIfPresent('event', 'idx_event_image', 'IDX_3BAE0AA73DA5256D');
        $this->renameIndexIfPresent('group_page', 'idx_grouppage_logo', 'IDX_3D50ED50F98F144A');
        $this->renameIndexIfPresent('group_page', 'idx_grouppage_banner', 'IDX_3D50ED50684EC833');
        $this->addSql('ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->renameIndexIfPresent('media', 'idx_media_gallery', 'IDX_6A2CA10C4E7AF8F');
        $this->renameIndexIfPresent('media', 'idx_media_grouppage', 'IDX_6A2CA10C8A4637E8');
        $this->renameIndexIfPresent('merch', 'idx_merch_image', 'IDX_F1B42EE03DA5256D');
        $this->renameIndexIfPresent('page', 'uniq_page_slug', 'UNIQ_140AB620989D9B62');
        $this->renameIndexIfPresent('page', 'idx_page_image', 'IDX_140AB6203DA5256D');
        $this->renameIndexIfPresent('post', 'uniq_post_slug', 'UNIQ_5A8A6C8D989D9B62');
        $this->renameIndexIfPresent('post', 'idx_post_image', 'IDX_5A8A6C8D3DA5256D');
        $this->renameIndexIfPresent('post', 'idx_post_category', 'IDX_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE visit CHANGE ip ip VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->renameIndexIfPresent('messenger_messages', 'idx_messenger_queue', 'IDX_75EA56E0FB7336F0');
        $this->renameIndexIfPresent('messenger_messages', 'idx_messenger_available', 'IDX_75EA56E0E3BD61CE');
        $this->renameIndexIfPresent('messenger_messages', 'idx_messenger_delivered', 'IDX_75EA56E016BA31DB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invite_code');
        $this->addSql('DROP TABLE site_config');
        $this->addSql('DROP TABLE users');
        $this->renameIndexIfPresent('category', 'uniq_64c19c1989d9b62', 'UNIQ_CATEGORY_SLUG');
        $this->renameIndexIfPresent('event', 'idx_3bae0aa73da5256d', 'IDX_EVENT_IMAGE');
        $this->renameIndexIfPresent('group_page', 'idx_3d50ed50f98f144a', 'IDX_GROUPPAGE_LOGO');
        $this->renameIndexIfPresent('group_page', 'idx_3d50ed50684ec833', 'IDX_GROUPPAGE_BANNER');
        $this->addSql('ALTER TABLE media CHANGE path path VARCHAR(255) DEFAULT \'NULL\', CHANGE alt alt VARCHAR(255) DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->renameIndexIfPresent('media', 'idx_6a2ca10c8a4637e8', 'IDX_MEDIA_GROUPPAGE');
        $this->renameIndexIfPresent('media', 'idx_6a2ca10c4e7af8f', 'IDX_MEDIA_GALLERY');
        $this->renameIndexIfPresent('merch', 'idx_f1b42ee03da5256d', 'IDX_MERCH_IMAGE');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->renameIndexIfPresent('messenger_messages', 'idx_75ea56e0fb7336f0', 'IDX_MESSENGER_QUEUE');
        $this->renameIndexIfPresent('messenger_messages', 'idx_75ea56e0e3bd61ce', 'IDX_MESSENGER_AVAILABLE');
        $this->renameIndexIfPresent('messenger_messages', 'idx_75ea56e016ba31db', 'IDX_MESSENGER_DELIVERED');
        $this->renameIndexIfPresent('page', 'idx_140ab6203da5256d', 'IDX_PAGE_IMAGE');
        $this->renameIndexIfPresent('page', 'uniq_140ab620989d9b62', 'UNIQ_PAGE_SLUG');
        $this->renameIndexIfPresent('post', 'idx_5a8a6c8d3da5256d', 'IDX_POST_IMAGE');
        $this->renameIndexIfPresent('post', 'idx_5a8a6c8d12469de2', 'IDX_POST_CATEGORY');
        $this->renameIndexIfPresent('post', 'uniq_5a8a6c8d989d9b62', 'UNIQ_POST_SLUG');
        $this->addSql('ALTER TABLE visit CHANGE ip ip VARCHAR(45) DEFAULT \'NULL\'');
    }
}
