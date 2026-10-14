<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260520190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add managed account links for parent child account management.';
    }

    public function up(Schema $schema): void
    {
        $manager = $this->connection->createSchemaManager();
        if (!$manager->tablesExist(['user_account_link'])) {
            $this->addSql('CREATE TABLE user_account_link (id INT AUTO_INCREMENT NOT NULL, guardian_id INT NOT NULL, managed_user_id INT NOT NULL, status VARCHAR(20) NOT NULL, token VARCHAR(64) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_83F8E6C8B5D9E68 (guardian_id), INDEX IDX_83F8E6C8D7B981E1 (managed_user_id), UNIQUE INDEX uniq_user_account_link_pair (guardian_id, managed_user_id), UNIQUE INDEX UNIQ_83F8E6C85F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE user_account_link ADD CONSTRAINT FK_83F8E6C8B5D9E68 FOREIGN KEY (guardian_id) REFERENCES users (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE user_account_link ADD CONSTRAINT FK_83F8E6C8D7B981E1 FOREIGN KEY (managed_user_id) REFERENCES users (id) ON DELETE CASCADE');
            return;
        }
        $columns = array_change_key_case($manager->listTableColumns('user_account_link'), CASE_LOWER);
        foreach (['id', 'guardian_id', 'managed_user_id', 'status', 'token', 'requested_at', 'accepted_at', 'expires_at'] as $required) {
            if (!isset($columns[$required])) {
                throw new \RuntimeException('Existing user_account_link table is incomplete: missing '.$required);
            }
        }
        $indexes = array_change_key_case($manager->listTableIndexes('user_account_link'), CASE_LOWER);
        foreach ([
            'idx_83f8e6c8b5d9e68' => 'CREATE INDEX IDX_83F8E6C8B5D9E68 ON user_account_link (guardian_id)',
            'idx_83f8e6c8d7b981e1' => 'CREATE INDEX IDX_83F8E6C8D7B981E1 ON user_account_link (managed_user_id)',
            'uniq_user_account_link_pair' => 'CREATE UNIQUE INDEX uniq_user_account_link_pair ON user_account_link (guardian_id, managed_user_id)',
            'uniq_83f8e6c85f37a13b' => 'CREATE UNIQUE INDEX UNIQ_83F8E6C85F37A13B ON user_account_link (token)',
        ] as $name => $sql) {
            if (!isset($indexes[$name])) {
                $this->addSql($sql);
            }
        }
        $foreignKeys = array_map(static fn ($fk): string => strtolower((string) $fk->getName()), $manager->listTableForeignKeys('user_account_link'));
        if (!in_array('fk_83f8e6c8b5d9e68', $foreignKeys, true)) {
            $this->addSql('ALTER TABLE user_account_link ADD CONSTRAINT FK_83F8E6C8B5D9E68 FOREIGN KEY (guardian_id) REFERENCES users (id) ON DELETE CASCADE');
        }
        if (!in_array('fk_83f8e6c8d7b981e1', $foreignKeys, true)) {
            $this->addSql('ALTER TABLE user_account_link ADD CONSTRAINT FK_83F8E6C8D7B981E1 FOREIGN KEY (managed_user_id) REFERENCES users (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_account_link');
    }
}
