<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reset password token fields to users.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD reset_password_token VARCHAR(128) DEFAULT NULL, ADD reset_password_token_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_RESET_PASSWORD_TOKEN ON users (reset_password_token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_RESET_PASSWORD_TOKEN ON users');
        $this->addSql('ALTER TABLE users DROP reset_password_token, DROP reset_password_token_expires_at');
    }
}
