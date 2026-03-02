<?php

namespace App\Tests\Entity;

use App\Entity\OffreStage;
use PHPUnit\Framework\TestCase;

class OffreStageTest extends TestCase
{
    private OffreStage $offreStage;

    protected function setUp(): void
    {
        $this->offreStage = new OffreStage();
    }

    public function testCreateOffreStage(): void
    {
        // Test de création d'une offre de stage avec des données valides
        $this->offreStage->setTitre('Développeur Web Stage');
        $this->offreStage->setDescription('Stage en développement web avec React et Symfony.');
        $this->offreStage->setEntreprise('Tech Company');
        $this->offreStage->setLieu('Paris');
        $this->offreStage->setDateDebut(new \DateTime('2024-01-15'));
        $this->offreStage->setDateFin(new \DateTime('2024-04-15'));
        $this->offreStage->setDureeJours(90);
        $this->offreStage->setDateCreation(new \DateTime('2023-12-01'));
        $this->offreStage->setStatut('Ouvert');
        $this->offreStage->setSalaire('1200.00');
        $this->offreStage->setCompetencesRequises(['PHP', 'JavaScript', 'React']);
        $this->offreStage->setDescriptionIa('Stage idéal pour les étudiants en informatique.');

        $this->assertEquals('Développeur Web Stage', $this->offreStage->getTitre());
        $this->assertEquals('Stage en développement web avec React et Symfony.', $this->offreStage->getDescription());
        $this->assertEquals('Tech Company', $this->offreStage->getEntreprise());
        $this->assertEquals('Paris', $this->offreStage->getLieu());
        $this->assertEquals(new \DateTime('2024-01-15'), $this->offreStage->getDateDebut());
        $this->assertEquals(new \DateTime('2024-04-15'), $this->offreStage->getDateFin());
        $this->assertEquals(90, $this->offreStage->getDureeJours());
        $this->assertEquals(new \DateTime('2023-12-01'), $this->offreStage->getDateCreation());
        $this->assertEquals('Ouvert', $this->offreStage->getStatut());
        $this->assertEquals('1200.00', $this->offreStage->getSalaire());
        $this->assertEquals(['PHP', 'JavaScript', 'React'], $this->offreStage->getCompetencesRequises());
        $this->assertEquals('Stage idéal pour les étudiants en informatique.', $this->offreStage->getDescriptionIa());
    }

    public function testUpdateOffreStage(): void
    {
        // Initialisation
        $this->offreStage->setTitre('Original Stage');
        $this->offreStage->setDescription('Original description');
        $this->offreStage->setEntreprise('Original Company');
        $this->offreStage->setLieu('Original Location');
        $this->offreStage->setDateDebut(new \DateTime('2024-01-01'));
        $this->offreStage->setDateFin(new \DateTime('2024-03-01'));
        $this->offreStage->setDureeJours(60);
        $this->offreStage->setStatut('Ouvert');
        $this->offreStage->setSalaire('1000.00');

        // Mise à jour
        $this->offreStage->setTitre('Updated Stage');
        $this->offreStage->setDescription('Updated description');
        $this->offreStage->setEntreprise('Updated Company');
        $this->offreStage->setLieu('Updated Location');
        $this->offreStage->setDateDebut(new \DateTime('2024-02-01'));
        $this->offreStage->setDateFin(new \DateTime('2024-05-01'));
        $this->offreStage->setDureeJours(90);
        $this->offreStage->setStatut('Fermé');
        $this->offreStage->setSalaire('1500.00');

        // Vérification des mises à jour
        $this->assertEquals('Updated Stage', $this->offreStage->getTitre());
        $this->assertEquals('Updated description', $this->offreStage->getDescription());
        $this->assertEquals('Updated Company', $this->offreStage->getEntreprise());
        $this->assertEquals('Updated Location', $this->offreStage->getLieu());
        $this->assertEquals(new \DateTime('2024-02-01'), $this->offreStage->getDateDebut());
        $this->assertEquals(new \DateTime('2024-05-01'), $this->offreStage->getDateFin());
        $this->assertEquals(90, $this->offreStage->getDureeJours());
        $this->assertEquals('Fermé', $this->offreStage->getStatut());
        $this->assertEquals('1500.00', $this->offreStage->getSalaire());
    }

    public function testOffreStageStatutValidation(): void
    {
        // Test avec les statuts valides
        $this->offreStage->setStatut('Ouvert');
        $this->assertEquals('Ouvert', $this->offreStage->getStatut());

        $this->offreStage->setStatut('Fermé');
        $this->assertEquals('Fermé', $this->offreStage->getStatut());

        $this->offreStage->setStatut('Pourvu');
        $this->assertEquals('Pourvu', $this->offreStage->getStatut());

        // Test avec null
        $this->offreStage->setStatut(null);
        $this->assertNull($this->offreStage->getStatut());
    }

    public function testOffreStageCompetencesRequises(): void
    {
        // Test avec différentes compétences
        $competences = ['PHP', 'JavaScript', 'React', 'Symfony'];
        $this->offreStage->setCompetencesRequises($competences);
        $this->assertEquals($competences, $this->offreStage->getCompetencesRequises());

        // Test avec tableau vide
        $this->offreStage->setCompetencesRequises([]);
        $this->assertEquals([], $this->offreStage->getCompetencesRequises());

        // Test avec null
        $this->offreStage->setCompetencesRequises(null);
        $this->assertNull($this->offreStage->getCompetencesRequises());
    }

    public function testOffreStageDateValidation(): void
    {
        // Test avec des dates valides
        $dateDebut = new \DateTime('2024-01-01');
        $dateFin = new \DateTime('2024-03-01');
        
        $this->offreStage->setDateDebut($dateDebut);
        $this->offreStage->setDateFin($dateFin);
        
        $this->assertEquals($dateDebut, $this->offreStage->getDateDebut());
        $this->assertEquals($dateFin, $this->offreStage->getDateFin());

        // Test avec null
        $this->offreStage->setDateDebut(null);
        $this->offreStage->setDateFin(null);
        
        $this->assertNull($this->offreStage->getDateDebut());
        $this->assertNull($this->offreStage->getDateFin());
    }

    public function testOffreStageDureeJours(): void
    {
        // Test avec différentes durées
        $this->offreStage->setDureeJours(30);
        $this->assertEquals(30, $this->offreStage->getDureeJours());

        $this->offreStage->setDureeJours(90);
        $this->assertEquals(90, $this->offreStage->getDureeJours());

        $this->offreStage->setDureeJours(180);
        $this->assertEquals(180, $this->offreStage->getDureeJours());

        // Test avec null
        $this->offreStage->setDureeJours(null);
        $this->assertNull($this->offreStage->getDureeJours());
    }

    public function testOffreStageSalaire(): void
    {
        // Test avec différents salaires
        $this->offreStage->setSalaire('1000.00');
        $this->assertEquals('1000.00', $this->offreStage->getSalaire());

        $this->offreStage->setSalaire('1500.50');
        $this->assertEquals('1500.50', $this->offreStage->getSalaire());

        $this->offreStage->setSalaire('2000.00');
        $this->assertEquals('2000.00', $this->offreStage->getSalaire());

        // Test avec null
        $this->offreStage->setSalaire(null);
        $this->assertNull($this->offreStage->getSalaire());
    }

    public function testOffreStageLieuNullable(): void
    {
        // Test avec un lieu
        $this->offreStage->setLieu('Paris');
        $this->assertEquals('Paris', $this->offreStage->getLieu());

        // Test avec lieu null
        $this->offreStage->setLieu(null);
        $this->assertNull($this->offreStage->getLieu());
    }

    public function testOffreStageDescriptionIa(): void
    {
        // Test avec description IA
        $this->offreStage->setDescriptionIa('Description générée par IA');
        $this->assertEquals('Description générée par IA', $this->offreStage->getDescriptionIa());

        // Test avec null
        $this->offreStage->setDescriptionIa(null);
        $this->assertNull($this->offreStage->getDescriptionIa());
    }

    public function testOffreStageDateCreation(): void
    {
        // Test avec une date spécifique
        $date = new \DateTime('2023-12-01 10:30:00');
        $this->offreStage->setDateCreation($date);
        $this->assertEquals($date, $this->offreStage->getDateCreation());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->offreStage->setDateCreation($now);
        $this->assertEquals($now, $this->offreStage->getDateCreation());
    }

    public function testOffreStageDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->offreStage->getId());
        $this->assertNull($this->offreStage->getTitre());
        $this->assertNull($this->offreStage->getDescription());
        $this->assertNull($this->offreStage->getEntreprise());
        $this->assertNull($this->offreStage->getLieu());
        $this->assertNull($this->offreStage->getDateDebut());
        $this->assertNull($this->offreStage->getDateFin());
        $this->assertNull($this->offreStage->getDureeJours());
        $this->assertNull($this->offreStage->getDateCreation());
        $this->assertEquals('Ouvert', $this->offreStage->getStatut()); // Valeur par défaut
        $this->assertNull($this->offreStage->getSalaire());
        $this->assertNull($this->offreStage->getCompetencesRequises());
        $this->assertNull($this->offreStage->getDescriptionIa());
    }

    public function testOffreStageFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        // Création
        $this->offreStage->setTitre('Stage Développeur');
        $this->offreStage->setDescription('Stage en développement web');
        $this->offreStage->setEntreprise('Tech Corp');
        $this->offreStage->setLieu('Lyon');
        $this->offreStage->setDateDebut(new \DateTime('2024-02-01'));
        $this->offreStage->setDateFin(new \DateTime('2024-05-01'));
        $this->offreStage->setDureeJours(90);
        $this->offreStage->setDateCreation(new \DateTime('2023-12-15'));
        $this->offreStage->setStatut('Ouvert');
        $this->offreStage->setSalaire('1200.00');
        $this->offreStage->setCompetencesRequises(['PHP', 'JavaScript']);

        // Vérification initiale
        $this->assertEquals('Stage Développeur', $this->offreStage->getTitre());
        $this->assertEquals('Tech Corp', $this->offreStage->getEntreprise());
        $this->assertEquals('Ouvert', $this->offreStage->getStatut());

        // Modification
        $this->offreStage->setStatut('Pourvu');
        $this->offreStage->setSalaire('1300.00');
        $this->offreStage->setCompetencesRequises(['PHP', 'JavaScript', 'React']);

        // Vérification après modification
        $this->assertEquals('Pourvu', $this->offreStage->getStatut());
        $this->assertEquals('1300.00', $this->offreStage->getSalaire());
        $this->assertEquals(['PHP', 'JavaScript', 'React'], $this->offreStage->getCompetencesRequises());
    }
}
