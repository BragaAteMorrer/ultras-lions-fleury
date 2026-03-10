<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251216120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ticketing (tickets + categories) and seed home/away categories';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ticket_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_TICKET_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, title VARCHAR(180) NOT NULL, opponent VARCHAR(180) NOT NULL, match_date DATETIME NOT NULL, venue VARCHAR(180) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, stock INT NOT NULL, INDEX IDX_97A0ADA43DA5256D (image_id), INDEX IDX_97A0ADA412469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA43DA5256D FOREIGN KEY (image_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA412469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id) ON DELETE SET NULL');

        $this->addSql("INSERT INTO ticket_category (name, slug) VALUES ('Domicile', 'domicile')");
        $this->addSql("INSERT INTO ticket_category (name, slug) VALUES ('Extérieur', 'exterieur')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA43DA5256D');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA412469DE2');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_category');
    }
}

