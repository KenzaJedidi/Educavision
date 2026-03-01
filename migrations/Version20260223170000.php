<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position field to Answer table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE answer ADD position INT DEFAULT 1');
        $this->addSql('CREATE INDEX idx_answer_position ON answer (position)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_answer_position ON answer');
        $this->addSql('ALTER TABLE answer DROP COLUMN position');
    }
}
