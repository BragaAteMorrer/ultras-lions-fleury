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
        $manager = $this->connection->createSchemaManager();
        $existing = array_map('strtolower', $manager->listTableNames());
        if (!in_array('refreshment_category', $existing, true)) {
        $this->addSql("CREATE TABLE refreshment_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        }
        if (!in_array('refreshment_product', $existing, true)) {
        $this->addSql("CREATE TABLE refreshment_product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(160) NOT NULL, price_cents INT NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_REFRESHMENT_PRODUCT_CATEGORY (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        }
        if (!in_array('refreshment_order', $existing, true)) {
        $this->addSql("CREATE TABLE refreshment_order (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, status VARCHAR(20) NOT NULL, unit_price_cents INT NOT NULL, customer_email VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_REFRESHMENT_ORDER_TICKET (ticket_id), INDEX IDX_REFRESHMENT_ORDER_PRODUCT (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        }
        if (!in_array('refreshment_report', $existing, true)) {
        $this->addSql("CREATE TABLE refreshment_report (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, recipient VARCHAR(180) NOT NULL, closes_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', sent_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_refreshment_report_ticket (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
        }
        if (in_array('refreshment_report', $existing, true)) {
            $columns = array_change_key_case($manager->listTableColumns('refreshment_report'), CASE_LOWER);
            if (!isset($columns['closes_at'])) {
                $this->addSql("ALTER TABLE refreshment_report ADD closes_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
            }
        }
        if (!in_array('refreshment_product', $existing, true) || !in_array(strtolower('FK_REFRESHMENT_PRODUCT_CATEGORY'), array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $manager->listTableForeignKeys('refreshment_product')), true)) {
        $this->addSql('ALTER TABLE refreshment_product ADD CONSTRAINT FK_REFRESHMENT_PRODUCT_CATEGORY FOREIGN KEY (category_id) REFERENCES refreshment_category (id) ON DELETE RESTRICT');
        }
        if (!in_array('refreshment_order', $existing, true) || !in_array(strtolower('FK_REFRESHMENT_ORDER_TICKET'), array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $manager->listTableForeignKeys('refreshment_order')), true)) {
        $this->addSql('ALTER TABLE refreshment_order ADD CONSTRAINT FK_REFRESHMENT_ORDER_TICKET FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE RESTRICT');
        }
        if (!in_array('refreshment_order', $existing, true) || !in_array(strtolower('FK_REFRESHMENT_ORDER_PRODUCT'), array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $manager->listTableForeignKeys('refreshment_order')), true)) {
        $this->addSql('ALTER TABLE refreshment_order ADD CONSTRAINT FK_REFRESHMENT_ORDER_PRODUCT FOREIGN KEY (product_id) REFERENCES refreshment_product (id) ON DELETE RESTRICT');
        }
        if (!in_array('refreshment_report', $existing, true) || !in_array(strtolower('FK_REFRESHMENT_REPORT_TICKET'), array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $manager->listTableForeignKeys('refreshment_report')), true)) {
        $this->addSql('ALTER TABLE refreshment_report ADD CONSTRAINT FK_REFRESHMENT_REPORT_TICKET FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE refreshment_report');
        $this->addSql('DROP TABLE refreshment_order');
        $this->addSql('DROP TABLE refreshment_product');
        $this->addSql('DROP TABLE refreshment_category');
    }
}
