<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Duplicate of Version20251202215857 generated on the same day.
 * The earlier migration already creates site_config/visit and renames indexes.
 * Applying the same DDL twice breaks installation from a fresh database.
 */
final class Version20251202220409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op: duplicated DDL of Version20251202215857';
    }

    public function up(Schema $schema): void
    {
        // Intentionally empty. Preserve version history.
    }

    public function down(Schema $schema): void
    {
        // Intentionally empty: do not drop tables created by the earlier migration.
    }
}
