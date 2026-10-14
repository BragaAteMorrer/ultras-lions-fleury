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
        // Some tables and indexes may predate this historical migration.
        // Automated rollback could delete data owned by earlier migrations.
        $this->abortIf(true, 'Rollback of this idempotent historical migration requires a manual schema review.');
    }
}
