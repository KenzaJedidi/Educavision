<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration: Create utilisateur table for user management and authentication.
 */
final class Version20260208000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create utilisateur table for user management and Symfony Security authentication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE utilisateur (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(180) NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT \'etudiant\',
            telephone VARCHAR(20) DEFAULT NULL,
            adresse LONGTEXT DEFAULT NULL,
            actif TINYINT(1) NOT NULL DEFAULT 1,
            date_inscription DATETIME NOT NULL,
            date_modification DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE utilisateur');
    }
}
