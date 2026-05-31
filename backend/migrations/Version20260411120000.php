<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add chants with audio and lyrics';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE chant (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, lyrics LONGTEXT DEFAULT NULL, audio_path VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE chant');
    }
}
