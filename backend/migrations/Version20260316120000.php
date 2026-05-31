<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add editable group section texts to group_page';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE group_page ADD histoire_text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_page ADD mentalite_text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_page ADD fonctionnement_text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_page ADD rejoindre_text LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_page ADD se_carter_text LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE group_page DROP histoire_text');
        $this->addSql('ALTER TABLE group_page DROP mentalite_text');
        $this->addSql('ALTER TABLE group_page DROP fonctionnement_text');
        $this->addSql('ALTER TABLE group_page DROP rejoindre_text');
        $this->addSql('ALTER TABLE group_page DROP se_carter_text');
    }
}
