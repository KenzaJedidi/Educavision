<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter les champs de scoring et de recommandation à la table course
 */
final class Version20260222195544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add scoring and recommendation fields to course table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE course ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE course ADD likes INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE course ADD comments_count INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE course ADD popularity_score DECIMAL(10, 2) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE course ADD wikipedia_summary TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD last_accessed DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD keywords VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE course DROP COLUMN views');
        $this->addSql('ALTER TABLE course DROP COLUMN likes');
        $this->addSql('ALTER TABLE course DROP COLUMN comments_count');
        $this->addSql('ALTER TABLE course DROP COLUMN popularity_score');
        $this->addSql('ALTER TABLE course DROP COLUMN wikipedia_summary');
        $this->addSql('ALTER TABLE course DROP COLUMN last_accessed');
        $this->addSql('ALTER TABLE course DROP COLUMN keywords');
    }
}
