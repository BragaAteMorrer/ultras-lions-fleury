<?php
declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261014205000 extends AbstractMigration
{
    public function getDescription(): string { return 'Persist one checkout recap delivery per payment'; }
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE checkout_email_delivery (id INT AUTO_INCREMENT NOT NULL, checkout_id INT NOT NULL, status VARCHAR(20) NOT NULL, attempts INT NOT NULL, started_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', sent_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_checkout_email_delivery_checkout (checkout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4");
        $this->addSql('ALTER TABLE checkout_email_delivery ADD CONSTRAINT FK_CHECKOUT_EMAIL_DELIVERY_CHECKOUT FOREIGN KEY (checkout_id) REFERENCES payment_checkout (id) ON DELETE CASCADE');
    }
    public function down(Schema $schema): void { $this->addSql('DROP TABLE checkout_email_delivery'); }
}
