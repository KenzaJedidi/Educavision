<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position and difficulty fields to Question table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question ADD position INT DEFAULT 1');
        $this->addSql('ALTER TABLE question ADD difficulty VARCHAR(50) DEFAULT "Moyen"');
        
        // Add index
        $this->addSql('CREATE INDEX idx_question_position ON question (position)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_question_position ON question');
        $this->addSql('ALTER TABLE question DROP COLUMN position');
        $this->addSql('ALTER TABLE question DROP COLUMN difficulty');
    }
}
