<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Course and Chapter entities with new field structure and validation';
    }

    public function up(Schema $schema): void
    {
        // Update Course table
        $this->addSql('ALTER TABLE course CHANGE titre title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE course CHANGE status status VARCHAR(20) DEFAULT \'draft\' NOT NULL');
        $this->addSql('ALTER TABLE course CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE course MODIFY description TEXT NOT NULL');

        // Update Chapter table
        $this->addSql('ALTER TABLE chapter CHANGE titre title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE chapter CHANGE description content TEXT NOT NULL');
        $this->addSql('ALTER TABLE chapter CHANGE ordre position INT NOT NULL');
        $this->addSql('ALTER TABLE chapter CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE chapter MODIFY course_id INT NOT NULL');

        // Update existing data
        $this->addSql('UPDATE course SET status = \'published\' WHERE status = 1');
        $this->addSql('UPDATE course SET status = \'draft\' WHERE status = 0');
        
        // Set default position for chapters that don't have one
        $this->addSql('UPDATE chapter SET position = 1 WHERE position IS NULL OR position = 0');
    }

    public function down(Schema $schema): void
    {
        // Revert Course table
        $this->addSql('ALTER TABLE course CHANGE title titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE course CHANGE status status INT NOT NULL');
        $this->addSql('UPDATE course SET status = 1 WHERE status = \'published\'');
        $this->addSql('UPDATE course SET status = 0 WHERE status = \'draft\'');
        $this->addSql('ALTER TABLE course MODIFY description TEXT DEFAULT NULL');

        // Revert Chapter table
        $this->addSql('ALTER TABLE chapter CHANGE title titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE chapter CHANGE content description TEXT NOT NULL');
        $this->addSql('ALTER TABLE chapter CHANGE position ordre INT NOT NULL');
        $this->addSql('ALTER TABLE chapter MODIFY course_id INT DEFAULT NULL');
    }
}
