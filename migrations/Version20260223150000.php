<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new fields to Quiz table for AI integration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quiz ADD chapter_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD status VARCHAR(50) DEFAULT "draft"');
        $this->addSql('ALTER TABLE quiz ADD difficulty_level VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD time_limit INT DEFAULT 0');
        $this->addSql('ALTER TABLE quiz ADD number_of_questions INT DEFAULT 0');
        $this->addSql('ALTER TABLE quiz ADD attempts INT DEFAULT 0');
        $this->addSql('ALTER TABLE quiz ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD metadata JSON DEFAULT NULL');
        
        // Add foreign key
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_quiz_chapter FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE SET NULL');
        
        // Add indices
        $this->addSql('CREATE INDEX idx_quiz_chapter ON quiz (chapter_id)');
        $this->addSql('CREATE INDEX idx_quiz_status ON quiz (status)');
        $this->addSql('CREATE INDEX idx_quiz_difficulty ON quiz (difficulty_level)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_quiz_chapter');
        $this->addSql('DROP INDEX idx_quiz_chapter ON quiz');
        $this->addSql('DROP INDEX idx_quiz_status ON quiz');
        $this->addSql('DROP INDEX idx_quiz_difficulty ON quiz');
        
        $this->addSql('ALTER TABLE quiz DROP COLUMN chapter_id');
        $this->addSql('ALTER TABLE quiz DROP COLUMN status');
        $this->addSql('ALTER TABLE quiz DROP COLUMN difficulty_level');
        $this->addSql('ALTER TABLE quiz DROP COLUMN time_limit');
        $this->addSql('ALTER TABLE quiz DROP COLUMN number_of_questions');
        $this->addSql('ALTER TABLE quiz DROP COLUMN attempts');
        $this->addSql('ALTER TABLE quiz DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE quiz DROP COLUMN metadata');
    }
}
