<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260221120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category column to reclamation table';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $columns = $sm->listTableColumns('reclamation');
        if (!array_key_exists('category', $columns)) {
            $this->addSql("ALTER TABLE reclamation ADD category VARCHAR(100) DEFAULT NULL");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reclamation DROP category');
    }
}
