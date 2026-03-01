<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour enrichir la table chapters avec les champs IA et traduction
 */
final class Version20260223000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add IA enrichment and translation fields to chapters';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chapter 
            ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT \'draft\' COMMENT \'draft ou published\',
            ADD COLUMN enriched_content LONGTEXT DEFAULT NULL COMMENT \'Contenu enrichi par IA\',
            ADD COLUMN difficulty_level VARCHAR(50) DEFAULT NULL COMMENT \'débutant, intermédiaire, avancé\',
            ADD COLUMN translations JSON DEFAULT NULL COMMENT \'Traductions en différentes langues\',
            ADD COLUMN position INT DEFAULT NULL COMMENT \'Position pour drag & drop\',
            ADD COLUMN structured_outline LONGTEXT DEFAULT NULL COMMENT \'Plan structuré généré par IA\',
            ADD COLUMN updated_at DATETIME DEFAULT NULL COMMENT \'Date de dernière modification\'
        ');

        // Créer un index sur status et position pour les requêtes
        $this->addSql('CREATE INDEX idx_chapter_status ON chapter(status)');
        $this->addSql('CREATE INDEX idx_chapter_position ON chapter(position)');
        $this->addSql('CREATE INDEX idx_chapter_course_position ON chapter(course_id, position)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chapter 
            DROP COLUMN status,
            DROP COLUMN enriched_content,
            DROP COLUMN difficulty_level,
            DROP COLUMN translations,
            DROP COLUMN position,
            DROP COLUMN structured_outline,
            DROP COLUMN updated_at
        ');

        $this->addSql('DROP INDEX idx_chapter_status ON chapter');
        $this->addSql('DROP INDEX idx_chapter_position ON chapter');
        $this->addSql('DROP INDEX idx_chapter_course_position ON chapter');
    }
}
