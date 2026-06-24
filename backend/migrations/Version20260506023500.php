<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260506023500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Google Analytics ID to site configuration.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site_config ADD google_analytics_id VARCHAR(40) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE site_config DROP google_analytics_id');
    }
}
