<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify it to your needs!
 */
final class Version20260207040000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create conversation_message table';
    }

    public function up(Schema $schema): void
    {
        // Only create the table if it does not already exist (Doctrine DBAL 3+)
        $sm = $this->connection->createSchemaManager();
        if (!$sm->tablesExist(['conversation_message'])) {
            $this->addSql('CREATE TABLE conversation_message (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, sender_name VARCHAR(255) NOT NULL, sender_type VARCHAR(10) NOT NULL, content TEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9D1F6A89537A132B (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE conversation_message ADD CONSTRAINT FK_9D1F6A89537A132B FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE conversation_message');
    }
}
