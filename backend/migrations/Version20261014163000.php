<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261014163000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Persist buvette report delivery attempts, status, and frozen snapshot';
    }

    public function up(Schema $schema): void
    {
        $columns = array_change_key_case($this->connection->createSchemaManager()->listTableColumns('refreshment_report'), CASE_LOWER);
        $fields = [
            'delivery_status' => "VARCHAR(24) DEFAULT 'pending' NOT NULL",
            'attempts' => 'INT DEFAULT 0 NOT NULL',
            'delivery_key' => 'VARCHAR(64) DEFAULT NULL',
            'last_attempt_at' => "DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'",
            'snapshot' => 'JSON DEFAULT NULL',
        ];
        foreach ($fields as $field => $definition) {
            if (!isset($columns[$field])) {
                $this->addSql(sprintf('ALTER TABLE refreshment_report ADD %s %s', $field, $definition));
            }
        }
        $indexes = array_change_key_case($this->connection->createSchemaManager()->listTableIndexes('refreshment_report'), CASE_LOWER);
        if (!isset($indexes['uniq_refreshment_delivery_key'])) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_REFRESHMENT_DELIVERY_KEY ON refreshment_report (delivery_key)');
        }
        $this->addSql("UPDATE refreshment_report SET delivery_status = 'sent' WHERE sent_at IS NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_REFRESHMENT_DELIVERY_KEY ON refreshment_report');
        $this->addSql('ALTER TABLE refreshment_report DROP delivery_status, DROP attempts, DROP delivery_key, DROP last_attempt_at, DROP snapshot');
    }
}
