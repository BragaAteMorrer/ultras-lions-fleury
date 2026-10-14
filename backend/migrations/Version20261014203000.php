<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261014203000 extends AbstractMigration
{
    public function getDescription(): string { return 'Add optional limited refreshment inventory'; }
    public function up(Schema $schema): void { $this->addSql('ALTER TABLE refreshment_product ADD stock INT DEFAULT NULL'); }
    public function down(Schema $schema): void { $this->addSql('ALTER TABLE refreshment_product DROP stock'); }
}
