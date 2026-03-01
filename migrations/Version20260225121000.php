<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add banUntil and banReason to Utilisateur for admin ban feature';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs ADD ban_until DATETIME DEFAULT NULL, ADD ban_reason VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs DROP ban_until, DROP ban_reason');
    }
}
