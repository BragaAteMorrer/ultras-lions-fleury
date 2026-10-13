<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing merch_order table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE merch_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, merch_id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, customer_first_name VARCHAR(120) DEFAULT NULL, customer_last_name VARCHAR(120) DEFAULT NULL, size VARCHAR(20) DEFAULT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, payment_method VARCHAR(20) NOT NULL, executed TINYINT(1) NOT NULL, INDEX IDX_1E733F9EA76ED395 (user_id), INDEX IDX_1E733F9E67B3B43D (merch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE merch_order ADD CONSTRAINT FK_1E733F9EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE merch_order ADD CONSTRAINT FK_1E733F9E67B3B43D FOREIGN KEY (merch_id) REFERENCES merch (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE merch_order DROP FOREIGN KEY FK_1E733F9EA76ED395');
        $this->addSql('ALTER TABLE merch_order DROP FOREIGN KEY FK_1E733F9E67B3B43D');
        $this->addSql('DROP TABLE merch_order');
    }
}
