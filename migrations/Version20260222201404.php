<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour corriger le type du champ salaire dans la table offre_stage
 */
final class Version20260222201404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix salaire field type in offre_stage table';
    }

    public function up(Schema $schema): void
    {
        // Modifier le type du champ salaire de DECIMAL(10,2) à VARCHAR(20)
        // pour correspondre au type string de l'entité
        $this->addSql('ALTER TABLE offre_stage MODIFY COLUMN salaire VARCHAR(20)');
    }

    public function down(Schema $schema): void
    {
        // Revenir au type DECIMAL(10,2) original
        $this->addSql('ALTER TABLE offre_stage MODIFY COLUMN salaire DECIMAL(10,2)');
    }
}
