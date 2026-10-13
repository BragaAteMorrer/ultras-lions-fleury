<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description field to gallery';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gallery ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gallery DROP description');
    }
}
