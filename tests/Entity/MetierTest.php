<?php

namespace App\Tests\Entity;

use App\Entity\Metier;
use App\Entity\Filiere;
use PHPUnit\Framework\TestCase;

class MetierTest extends TestCase
{
    private Metier $metier;

    protected function setUp(): void
    {
        $this->metier = new Metier();
    }

    public function testCreateMetier(): void
    {
        // Test de création d'un métier avec des données valides
        $filiere = new Filiere();
        $filiere->setNom('Informatique');

        $this->metier->setNom('Développeur Web');
        $this->metier->setDescription('Développement de sites web et applications');
        $this->metier->setFiliere($filiere);

        $this->assertEquals('Développeur Web', $this->metier->getNom());
        $this->assertEquals('Développement de sites web et applications', $this->metier->getDescription());
        $this->assertEquals($filiere, $this->metier->getFiliere());
    }

    public function testUpdateMetier(): void
    {
        // Initialisation
        $filiere = new Filiere();
        $filiere->setNom('Original Filiere');

        $this->metier->setNom('Original Metier');
        $this->metier->setDescription('Original description');
        $this->metier->setFiliere($filiere);

        // Mise à jour
        $newFiliere = new Filiere();
        $newFiliere->setNom('Updated Filiere');

        $this->metier->setNom('Updated Metier');
        $this->metier->setDescription('Updated description');
        $this->metier->setFiliere($newFiliere);

        // Vérification des mises à jour
        $this->assertEquals('Updated Metier', $this->metier->getNom());
        $this->assertEquals('Updated description', $this->metier->getDescription());
        $this->assertEquals($newFiliere, $this->metier->getFiliere());
    }

    public function testMetierFiliereRelation(): void
    {
        // Test de la relation avec la filière
        $filiere = new Filiere();
        $filiere->setNom('Informatique');

        $this->metier->setFiliere($filiere);

        // Vérification
        $this->assertEquals($filiere, $this->metier->getFiliere());
        
        // Pour tester la relation bidirectionnelle
        $filiere->addMetier($this->metier);
        $this->assertTrue($filiere->getMetiers()->contains($this->metier));
    }

    public function testMetierNomValidation(): void
    {
        // Test avec différents noms
        $this->metier->setNom('Développeur Web');
        $this->assertEquals('Développeur Web', $this->metier->getNom());

        $this->metier->setNom('Data Scientist');
        $this->assertEquals('Data Scientist', $this->metier->getNom());

        $this->metier->setNom('Chef de Projet');
        $this->assertEquals('Chef de Projet', $this->metier->getNom());

        // Test avec nom contenant des caractères spéciaux
        $this->metier->setNom('Ingénieur R&D');
        $this->assertEquals('Ingénieur R&D', $this->metier->getNom());
    }

    public function testMetierDescriptionValidation(): void
    {
        // Test avec différentes descriptions
        $this->metier->setDescription('Description courte');
        $this->assertEquals('Description courte', $this->metier->getDescription());

        $longDescription = 'Ceci est une description très détaillée du métier qui inclut les responsabilités quotidiennes, les compétences requises, les conditions de travail et les perspectives d\'évolution de carrière.';
        $this->metier->setDescription($longDescription);
        $this->assertEquals($longDescription, $this->metier->getDescription());

        // Test avec description contenant des caractères spéciaux
        $descriptionSpeciale = 'Gestion de projets d\'innovation & développement R&D';
        $this->metier->setDescription($descriptionSpeciale);
        $this->assertEquals($descriptionSpeciale, $this->metier->getDescription());
    }

    public function testMetierFiliereNullable(): void
    {
        // Test avec une filière
        $filiere = new Filiere();
        $filiere->setNom('Test Filiere');
        $this->metier->setFiliere($filiere);
        $this->assertEquals($filiere, $this->metier->getFiliere());

        // Test avec filière null
        $this->metier->setFiliere(null);
        $this->assertNull($this->metier->getFiliere());
    }

    public function testMetierDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->metier->getId());
        $this->assertNull($this->metier->getNom());
        $this->assertNull($this->metier->getDescription());
        $this->assertNull($this->metier->getFiliere());
    }

    public function testMetierWithNullFiliere(): void
    {
        // Test avec une filière null
        $this->metier->setNom('Test Metier');
        $this->metier->setDescription('Test description');
        $this->metier->setFiliere(null);

        $this->assertEquals('Test Metier', $this->metier->getNom());
        $this->assertEquals('Test description', $this->metier->getDescription());
        $this->assertNull($this->metier->getFiliere());
    }

    public function testMetierFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $filiere1 = new Filiere();
        $filiere1->setNom('Informatique');

        $filiere2 = new Filiere();
        $filiere2->setNom('Marketing');

        // Création
        $this->metier->setNom('Développeur Full Stack');
        $this->metier->setDescription('Développement complet d\'applications web');
        $this->metier->setFiliere($filiere1);

        // Vérification initiale
        $this->assertEquals('Développeur Full Stack', $this->metier->getNom());
        $this->assertEquals('Développement complet d\'applications web', $this->metier->getDescription());
        $this->assertEquals($filiere1, $this->metier->getFiliere());

        // Modification
        $this->metier->setNom('Développeur Full Stack & DevOps');
        $this->metier->setDescription('Développement complet et gestion d\'infrastructure');
        $this->metier->setFiliere($filiere2);

        // Vérification après modification
        $this->assertEquals('Développeur Full Stack & DevOps', $this->metier->getNom());
        $this->assertEquals('Développement complet et gestion d\'infrastructure', $this->metier->getDescription());
        $this->assertEquals($filiere2, $this->metier->getFiliere());
    }

    public function testMetierRelationConsistency(): void
    {
        // Test de la cohérence des relations
        $filiere = new Filiere();
        $filiere->setNom('Test Filiere');

        $metier2 = new Metier();
        $metier2->setNom('Autre Metier');

        // Ajout du métier à la filière
        $filiere->addMetier($this->metier);
        $filiere->addMetier($metier2);

        // Vérification que les deux métiers sont bien associés à la filière
        $this->assertEquals($filiere, $this->metier->getFiliere());
        $this->assertEquals($filiere, $metier2->getFiliere());
        $this->assertCount(2, $filiere->getMetiers());

        // Suppression d'un métier
        $filiere->removeMetier($metier2);
        $this->assertCount(1, $filiere->getMetiers());
        $this->assertNull($metier2->getFiliere());
    }
}
