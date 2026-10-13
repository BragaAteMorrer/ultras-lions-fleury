<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424131000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing ticket_order and payment_checkout tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ticket_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ticket_id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, customer_first_name VARCHAR(120) DEFAULT NULL, customer_last_name VARCHAR(120) DEFAULT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, payment_method VARCHAR(20) NOT NULL, paid TINYINT(1) NOT NULL, archived_at DATETIME DEFAULT NULL, INDEX IDX_E665A4F6A76ED395 (user_id), INDEX IDX_E665A4F6700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_order ADD CONSTRAINT FK_E665A4F6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket_order ADD CONSTRAINT FK_E665A4F6700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE payment_checkout (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, sumup_checkout_id VARCHAR(128) DEFAULT NULL, checkout_reference VARCHAR(64) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, email VARCHAR(180) DEFAULT NULL, customer_first_name VARCHAR(120) DEFAULT NULL, customer_last_name VARCHAR(120) DEFAULT NULL, cart JSON NOT NULL, created_at DATETIME NOT NULL, paid_at DATETIME DEFAULT NULL, processed_at DATETIME DEFAULT NULL, email_sent_at DATETIME DEFAULT NULL, INDEX IDX_73A5E4B6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_checkout ADD CONSTRAINT FK_73A5E4B6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment_checkout DROP FOREIGN KEY FK_73A5E4B6A76ED395');
        $this->addSql('ALTER TABLE ticket_order DROP FOREIGN KEY FK_E665A4F6A76ED395');
        $this->addSql('ALTER TABLE ticket_order DROP FOREIGN KEY FK_E665A4F6700047D2');
        $this->addSql('DROP TABLE payment_checkout');
        $this->addSql('DROP TABLE ticket_order');
    }
}
