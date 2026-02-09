<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260207231500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add vocal column to candidature to store optional audio path';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE candidature ADD vocal VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE candidature DROP vocal');
    }
}
