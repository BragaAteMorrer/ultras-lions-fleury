<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251215124500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add merch stock per size';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE merch_stock (id INT AUTO_INCREMENT NOT NULL, merch_id INT NOT NULL, size VARCHAR(30) NOT NULL, quantity INT NOT NULL, INDEX IDX_79B2DF2DE4F9D9E (merch_id), UNIQUE INDEX UNIQ_MERCH_STOCK_MERCH_SIZE (merch_id, size), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE merch_stock ADD CONSTRAINT FK_79B2DF2DE4F9D9E FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE merch_stock DROP FOREIGN KEY FK_79B2DF2DE4F9D9E');
        $this->addSql('DROP TABLE merch_stock');
    }
}

