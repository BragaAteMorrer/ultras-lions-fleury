<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261013153000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create match refreshment catalog, preorder and dispatch tables'; }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE refreshment_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE refreshment_product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(160) NOT NULL, price_cents INT NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_REFRESHMENT_PRODUCT_CATEGORY (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE refreshment_order (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, status VARCHAR(20) NOT NULL, unit_price_cents INT NOT NULL, customer_email VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_REFRESHMENT_ORDER_TICKET (ticket_id), INDEX IDX_REFRESHMENT_ORDER_PRODUCT (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE refreshment_report (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, recipient VARCHAR(180) NOT NULL, closes_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', sent_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_refreshment_report_ticket (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        $this->addSql('ALTER TABLE refreshment_product ADD CONSTRAINT FK_REFRESHMENT_PRODUCT_CATEGORY FOREIGN KEY (category_id) REFERENCES refreshment_category (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE refreshment_order ADD CONSTRAINT FK_REFRESHMENT_ORDER_TICKET FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE refreshment_order ADD CONSTRAINT FK_REFRESHMENT_ORDER_PRODUCT FOREIGN KEY (product_id) REFERENCES refreshment_product (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE refreshment_report ADD CONSTRAINT FK_REFRESHMENT_REPORT_TICKET FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE refreshment_report');
        $this->addSql('DROP TABLE refreshment_order');
        $this->addSql('DROP TABLE refreshment_product');
        $this->addSql('DROP TABLE refreshment_category');
    }
}
