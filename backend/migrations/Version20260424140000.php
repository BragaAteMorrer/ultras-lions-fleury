<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing password reset fields and index without altering existing data.';
    }

    public function up(Schema $schema): void
    {
        $manager = $this->connection->createSchemaManager();
        $columns = array_change_key_case($manager->listTableColumns('users'), CASE_LOWER);

        if (!isset($columns['reset_password_token'])) {
            $this->addSql('ALTER TABLE users ADD reset_password_token VARCHAR(128) DEFAULT NULL');
        }
        if (!isset($columns['reset_password_token_expires_at'])) {
            $this->addSql("ALTER TABLE users ADD reset_password_token_expires_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        }

        $indexes = $manager->listTableIndexes('users');
        $names = array_change_key_case($indexes, CASE_LOWER);
        if (!isset($names['idx_reset_password_token'])) {
            $this->addSql('CREATE INDEX IDX_RESET_PASSWORD_TOKEN ON users (reset_password_token)');
        }
    }

    public function down(Schema $schema): void
    {
        // Intentionally non-destructive: existing installations may have had these fields before the migration.
        $this->write('Rollback does not remove shared password reset data.');
    }
}
