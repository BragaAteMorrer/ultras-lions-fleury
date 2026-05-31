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
        $this->addSql('ALTER TABLE media ADD merch_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_6A2CA10C56A273CC ON media (merch_id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C56A273CC FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C56A273CC');
        $this->addSql('DROP INDEX IDX_6A2CA10C56A273CC ON media');
        $this->addSql('ALTER TABLE media DROP merch_id');
    }
}
