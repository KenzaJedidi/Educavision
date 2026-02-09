<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260203000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create offre_stage table for internship offers management';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE offre_stage (
            id INT AUTO_INCREMENT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            entreprise VARCHAR(255) NOT NULL,
            lieu VARCHAR(255) DEFAULT NULL,
            date_debut DATETIME NOT NULL,
            date_fin DATETIME NOT NULL,
            duree_jours INT NOT NULL,
            date_creation DATETIME NOT NULL,
            statut VARCHAR(50) NOT NULL DEFAULT "Ouvert",
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE offre_stage');
    }
}
