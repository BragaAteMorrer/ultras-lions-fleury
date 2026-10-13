<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align schema with EventCategory, Event, Post, Media, Merch and Ticket entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, section VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_EVENT_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE event ADD category_id INT DEFAULT NULL, ADD opponent VARCHAR(180) DEFAULT NULL, ADD match_location VARCHAR(12) DEFAULT NULL, ADD season VARCHAR(9) DEFAULT NULL, ADD journee INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_EVENT_CATEGORY_ID ON event (category_id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_EVENT_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES event_category (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('DROP INDEX IDX_5A8A6C8D12469DE2 ON post');
        $this->addSql('CREATE INDEX IDX_POST_EVENT_CATEGORY_ID ON post (category_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_POST_EVENT_CATEGORY_ID FOREIGN KEY (category_id) REFERENCES event_category (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE media ADD post_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_MEDIA_POST_ID ON media (post_id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_MEDIA_POST_ID FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE SET NULL');

        $this->addSql("ALTER TABLE merch ADD audience VARCHAR(20) NOT NULL DEFAULT 'public'");
        $this->addSql("ALTER TABLE ticket ADD match_location VARCHAR(20) DEFAULT NULL");

        $this->addSql("INSERT INTO event_category (name, slug, section) VALUES ('Articles', 'articles', 'articles')");
        $this->addSql("INSERT INTO event_category (name, slug, section) VALUES ('Photos de match', 'photos-de-match', 'photos_de_match')");
        $this->addSql("INSERT INTO event_category (name, slug, section) VALUES ('Evenements', 'evenements', 'evenements')");
        $this->addSql("INSERT INTO event_category (name, slug, section) VALUES ('Medias', 'medias', 'medias')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM event_category WHERE slug IN (\'articles\', \'photos-de-match\', \'evenements\', \'medias\')');

        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_MEDIA_POST_ID');
        $this->addSql('DROP INDEX IDX_MEDIA_POST_ID ON media');
        $this->addSql('ALTER TABLE media DROP post_id');

        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_POST_EVENT_CATEGORY_ID');
        $this->addSql('DROP INDEX IDX_POST_EVENT_CATEGORY_ID ON post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D12469DE2 ON post (category_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_EVENT_CATEGORY_ID');
        $this->addSql('DROP INDEX IDX_EVENT_CATEGORY_ID ON event');
        $this->addSql('ALTER TABLE event DROP category_id, DROP opponent, DROP match_location, DROP season, DROP journee');

        $this->addSql('DROP TABLE event_category');

        $this->addSql('ALTER TABLE merch DROP audience');
        $this->addSql('ALTER TABLE ticket DROP match_location');
    }
}
