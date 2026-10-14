<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261014190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attach refreshment orders to verified payment checkouts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE refreshment_order ADD payment_checkout_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_REFRESHMENT_CHECKOUT ON refreshment_order (payment_checkout_id)');
        $this->addSql('ALTER TABLE refreshment_order ADD CONSTRAINT FK_REFRESHMENT_CHECKOUT FOREIGN KEY (payment_checkout_id) REFERENCES payment_checkout (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE refreshment_order DROP FOREIGN KEY FK_REFRESHMENT_CHECKOUT');
        $this->addSql('DROP INDEX IDX_REFRESHMENT_CHECKOUT ON refreshment_order');
        $this->addSql('ALTER TABLE refreshment_order DROP payment_checkout_id');
    }
}
