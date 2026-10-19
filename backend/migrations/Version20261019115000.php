<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261019115000 extends AbstractMigration
{
    public function getDescription(): string { return 'Track buvette preparation and pickup separately from paid status'; }

    public function up(Schema $schema): void
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['refreshment_order'])) {
            $this->abortIf(true, 'refreshment_order is missing; apply earlier migrations first.');
        }
        $columns = array_change_key_case($this->connection->createSchemaManager()->listTableColumns('refreshment_order'), CASE_LOWER);
        if (!isset($columns['prepared_at'])) {
            $this->addSql("ALTER TABLE refreshment_order ADD prepared_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        }
        if (!isset($columns['collected_at'])) {
            $this->addSql("ALTER TABLE refreshment_order ADD collected_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        }
    }

    public function down(Schema $schema): void
    {
        // Historical proof of fulfillment must not be silently destroyed on rollback.
        $this->abortIf(true, 'Fulfillment audit data cannot be dropped automatically.');
    }
}
