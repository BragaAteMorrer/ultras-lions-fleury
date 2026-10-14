<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260311120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add merch_id to media table';
    }

    public function up(Schema $schema): void
    {
        $manager = $this->connection->createSchemaManager();
        $columns = array_change_key_case($manager->listTableColumns('media'), CASE_LOWER);
        if (!isset($columns['merch_id'])) {
            $this->addSql('ALTER TABLE media ADD merch_id INT DEFAULT NULL');
        }
        $indexes = array_change_key_case($manager->listTableIndexes('media'), CASE_LOWER);
        if (!isset($indexes['idx_6a2ca10c56a273cc'])) {
            $this->addSql('CREATE INDEX IDX_6A2CA10C56A273CC ON media (merch_id)');
        }
        $foreignKeys = $manager->listTableForeignKeys('media');
        $names = array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $foreignKeys);
        if (!in_array(strtolower('FK_6A2CA10C56A273CC'), $names, true)) {
            $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C56A273CC FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE SET NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C56A273CC');
        $this->addSql('DROP INDEX IDX_6A2CA10C56A273CC ON media');
        $this->addSql('ALTER TABLE media DROP merch_id');
    }
}
