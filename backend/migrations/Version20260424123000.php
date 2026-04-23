<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Billetweb URL to ticket for home match embed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket ADD billetweb_url VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket DROP billetweb_url');
    }
}
