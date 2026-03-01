<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add debouches field to formation table';
    }

    public function up(Schema $schema): void
    {
        // Only add the column if it does not already exist (Doctrine DBAL 3+)
        $sm = $this->connection->createSchemaManager();
        $columns = $sm->listTableColumns('formation');
        if (!array_key_exists('debouches', $columns)) {
            $this->addSql('ALTER TABLE formation ADD debouches LONGTEXT DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation DROP debouches');
    }
}
