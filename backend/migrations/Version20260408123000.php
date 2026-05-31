<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed default group page content (FR) if none exists';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
INSERT INTO group_page (name, description, histoire_text, mentalite_text, fonctionnement_text, rejoindre_text, se_carter_text)
SELECT
    'Le Groupe',
    NULL,
    'Nes en tribune a Fleury, on a bati notre identite match apres match. Rouge et noir, fideles, presents partout.',
    'Fidelite, respect, solidarite. On chante, on pousse, on ne lache rien, a domicile comme en deplacement.',
    'Le groupe tourne grace aux benevoles, aux reunions et aux decisions collectives. Chacun a sa place, chacun met la main.',
    'Pour vivre le match autrement, participer aux tifos et deplacements, et faire partie d''une famille rouge et noire.',
    'Passe a la table de vente les jours de match, recupere ton code, puis inscris-toi pour acceder a l''espace membres.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM group_page);
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM group_page WHERE name = 'Le Groupe'");
    }
}
