<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251215120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add merch categories and seed defaults';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE merch_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_MERCH_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE merch ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE merch ADD CONSTRAINT FK_F1B42EE012469DE2 FOREIGN KEY (category_id) REFERENCES merch_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F1B42EE012469DE2 ON merch (category_id)');

        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('T-shirt', 't-shirt')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Polo', 'polo')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Chemise', 'chemise')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Pull / Sweat', 'pull-sweat')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Veste', 'veste')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Manteau', 'manteau')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Couvre-Chef (Bob Casquette Bonnet/Cache-Cou)', 'couvre-chef')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Cartage', 'cartage')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Echarpe', 'echarpe')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Gadget (Drapeau, Briquet, Sacoche, Calendrier, Affiche, Lunettes, Sac Banane, Pins, Porte Cle, Sac, DVD, Livre)', 'gadget')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Patch', 'patch')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Short', 'short')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Chaussure', 'chaussure')");
        $this->addSql("INSERT INTO merch_category (name, slug) VALUES ('Stickers', 'stickers')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE merch DROP FOREIGN KEY FK_F1B42EE012469DE2');
        $this->addSql('DROP TABLE merch_category');
        $this->addSql('DROP INDEX IDX_F1B42EE012469DE2 ON merch');
        $this->addSql('ALTER TABLE merch DROP category_id');
    }
}

