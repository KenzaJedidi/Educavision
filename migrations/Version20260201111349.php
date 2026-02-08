<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201111349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, responsable VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, duree VARCHAR(100) NOT NULL, niveau VARCHAR(50) NOT NULL, prerequis_texte LONGTEXT DEFAULT NULL, competences_acquises LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE metier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, filiere_id INT NOT NULL, INDEX IDX_51A00D8C180AA129 (filiere_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE prerequis (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, ordre INT NOT NULL, formation_id INT NOT NULL, INDEX IDX_CAE3EB095200282E (formation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE simulation (id INT AUTO_INCREMENT NOT NULL, moyenne NUMERIC(5, 2) NOT NULL, specialites JSON NOT NULL, preferences JSON DEFAULT NULL, date_simulation DATETIME NOT NULL, resultats JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE metier ADD CONSTRAINT FK_51A00D8C180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE prerequis ADD CONSTRAINT FK_CAE3EB095200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metier DROP FOREIGN KEY FK_51A00D8C180AA129');
        $this->addSql('ALTER TABLE prerequis DROP FOREIGN KEY FK_CAE3EB095200282E');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE metier');
        $this->addSql('DROP TABLE prerequis');
        $this->addSql('DROP TABLE simulation');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
