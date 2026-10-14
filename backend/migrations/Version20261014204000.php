<?php
declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261014204000 extends AbstractMigration
{
    public function getDescription(): string { return 'Track manual reconciliation of paid checkouts requiring review'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_checkout ADD review_resolved_at DATETIME DEFAULT NULL, ADD review_resolution VARCHAR(24) DEFAULT NULL, ADD review_reference VARCHAR(180) DEFAULT NULL');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_checkout DROP review_resolved_at, DROP review_resolution, DROP review_reference');
    }
}
