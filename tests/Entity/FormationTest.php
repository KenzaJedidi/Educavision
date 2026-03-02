<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use App\Entity\Prerequis;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    private Formation $formation;

    protected function setUp(): void
    {
        $this->formation = new Formation();
    }

    public function testCreateFormation(): void
    {
        // Test de création d'une formation avec des données valides
        $this->formation->setNom('Développement Web');
        $this->formation->setDescription('Formation complète en développement web');
        $this->formation->setDuree('6 mois');
        $this->formation->setNiveau('Bac+2');
        $this->formation->setPrerequisTexte('Connaissances de base en HTML/CSS');
        $this->formation->setCompetencesAcquises('HTML, CSS, JavaScript, PHP');
        $this->formation->setDebouches('Développeur Web, Intégrateur Web');
        $this->formation->setImage('web-dev.jpg');

        $this->assertEquals('Développement Web', $this->formation->getNom());
        $this->assertEquals('Formation complète en développement web', $this->formation->getDescription());
        $this->assertEquals('6 mois', $this->formation->getDuree());
        $this->assertEquals('Bac+2', $this->formation->getNiveau());
        $this->assertEquals('Connaissances de base en HTML/CSS', $this->formation->getPrerequisTexte());
        $this->assertEquals('HTML, CSS, JavaScript, PHP', $this->formation->getCompetencesAcquises());
        $this->assertEquals('Développeur Web, Intégrateur Web', $this->formation->getDebouches());
        $this->assertEquals('web-dev.jpg', $this->formation->getImage());
    }

    public function testUpdateFormation(): void
    {
        // Initialisation
        $this->formation->setNom('Original Formation');
        $this->formation->setDescription('Original description');
        $this->formation->setDuree('3 mois');
        $this->formation->setNiveau('Bac+1');
        $this->formation->setPrerequisTexte('Original prerequis');
        $this->formation->setCompetencesAcquises('Original competences');
        $this->formation->setDebouches('Original debouches');
        $this->formation->setImage('original.jpg');

        // Mise à jour
        $this->formation->setNom('Updated Formation');
        $this->formation->setDescription('Updated description');
        $this->formation->setDuree('12 mois');
        $this->formation->setNiveau('Bac+3');
        $this->formation->setPrerequisTexte('Updated prerequis');
        $this->formation->setCompetencesAcquises('Updated competences');
        $this->formation->setDebouches('Updated debouches');
        $this->formation->setImage('updated.jpg');

        // Vérification des mises à jour
        $this->assertEquals('Updated Formation', $this->formation->getNom());
        $this->assertEquals('Updated description', $this->formation->getDescription());
        $this->assertEquals('12 mois', $this->formation->getDuree());
        $this->assertEquals('Bac+3', $this->formation->getNiveau());
        $this->assertEquals('Updated prerequis', $this->formation->getPrerequisTexte());
        $this->assertEquals('Updated competences', $this->formation->getCompetencesAcquises());
        $this->assertEquals('Updated debouches', $this->formation->getDebouches());
        $this->assertEquals('updated.jpg', $this->formation->getImage());
    }

    public function testFormationPrerequisRelation(): void
    {
        // Test de la relation avec les prérequis
        $prerequis1 = new Prerequis();
        $prerequis1->setNom('Mathématiques');
        $prerequis1->setDescription('Niveau bac en mathématiques');

        $prerequis2 = new Prerequis();
        $prerequis2->setNom('Informatique');
        $prerequis2->setDescription('Bases de l\'informatique');

        // Ajout de prérequis
        $this->formation->addPrerequi($prerequis1);
        $this->formation->addPrerequi($prerequis2);

        // Vérification
        $this->assertCount(2, $this->formation->getPrerequis());
        $this->assertTrue($this->formation->getPrerequis()->contains($prerequis1));
        $this->assertTrue($this->formation->getPrerequis()->contains($prerequis2));
        $this->assertEquals($this->formation, $prerequis1->getFormation());
        $this->assertEquals($this->formation, $prerequis2->getFormation());

        // Suppression d'un prérequis
        $this->formation->removePrerequi($prerequis1);
        $this->assertCount(1, $this->formation->getPrerequis());
        $this->assertFalse($this->formation->getPrerequis()->contains($prerequis1));
        $this->assertNull($prerequis1->getFormation());
    }

    public function testFormationNomNullable(): void
    {
        // Test avec un nom
        $this->formation->setNom('Test Formation');
        $this->assertEquals('Test Formation', $this->formation->getNom());

        // Test avec nom null
        $this->formation->setNom(null);
        $this->assertNull($this->formation->getNom());
    }

    public function testFormationDescriptionNullable(): void
    {
        // Test avec une description
        $this->formation->setDescription('Test description');
        $this->assertEquals('Test description', $this->formation->getDescription());

        // Test avec description null
        $this->formation->setDescription(null);
        $this->assertNull($this->formation->getDescription());
    }

    public function testFormationDureeNullable(): void
    {
        // Test avec une durée
        $this->formation->setDuree('6 mois');
        $this->assertEquals('6 mois', $this->formation->getDuree());

        // Test avec durée null
        $this->formation->setDuree(null);
        $this->assertNull($this->formation->getDuree());
    }

    public function testFormationNiveauNullable(): void
    {
        // Test avec un niveau
        $this->formation->setNiveau('Bac+2');
        $this->assertEquals('Bac+2', $this->formation->getNiveau());

        // Test avec niveau null
        $this->formation->setNiveau(null);
        $this->assertNull($this->formation->getNiveau());
    }

    public function testFormationOptionalFields(): void
    {
        // Test des champs optionnels
        $this->formation->setPrerequisTexte('Prérequis spécifiques');
        $this->formation->setCompetencesAcquises('Compétences acquises');
        $this->formation->setDebouches('Débouchés professionnels');
        $this->formation->setImage('formation-image.jpg');

        $this->assertEquals('Prérequis spécifiques', $this->formation->getPrerequisTexte());
        $this->assertEquals('Compétences acquises', $this->formation->getCompetencesAcquises());
        $this->assertEquals('Débouchés professionnels', $this->formation->getDebouches());
        $this->assertEquals('formation-image.jpg', $this->formation->getImage());

        // Test avec null
        $this->formation->setPrerequisTexte(null);
        $this->formation->setCompetencesAcquises(null);
        $this->formation->setDebouches(null);
        $this->formation->setImage(null);

        $this->assertNull($this->formation->getPrerequisTexte());
        $this->assertNull($this->formation->getCompetencesAcquises());
        $this->assertNull($this->formation->getDebouches());
        $this->assertNull($this->formation->getImage());
    }

    public function testFormationDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->formation->getId());
        $this->assertNull($this->formation->getNom());
        $this->assertNull($this->formation->getDescription());
        $this->assertNull($this->formation->getDuree());
        $this->assertNull($this->formation->getNiveau());
        $this->assertNull($this->formation->getPrerequisTexte());
        $this->assertNull($this->formation->getCompetencesAcquises());
        $this->assertNull($this->formation->getDebouches());
        $this->assertNull($this->formation->getImage());
        $this->assertCount(0, $this->formation->getPrerequis());
    }

    public function testFormationPrerequisCollection(): void
    {
        // Test que la collection est bien initialisée
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->formation->getPrerequis());
        $this->assertCount(0, $this->formation->getPrerequis());
    }

    public function testFormationFullWorkflow(): void
    {
        // Test du workflow complet
        $prerequis = new Prerequis();
        $prerequis->setNom('Base informatique');
        $prerequis->setDescription('Connaissances de base en informatique');

        // Création
        $this->formation->setNom('Data Science');
        $this->formation->setDescription('Formation en science des données');
        $this->formation->setDuree('9 mois');
        $this->formation->setNiveau('Bac+3');
        $this->formation->addPrerequi($prerequis);

        // Vérification initiale
        $this->assertEquals('Data Science', $this->formation->getNom());
        $this->assertEquals('Formation en science des données', $this->formation->getDescription());
        $this->assertEquals('9 mois', $this->formation->getDuree());
        $this->assertEquals('Bac+3', $this->formation->getNiveau());
        $this->assertCount(1, $this->formation->getPrerequis());

        // Modification
        $this->formation->setNom('Data Science & Machine Learning');
        $this->formation->setDescription('Formation complète en data science et machine learning');
        $this->formation->setDuree('12 mois');
        $this->formation->setNiveau('Bac+4');

        // Vérification après modification
        $this->assertEquals('Data Science & Machine Learning', $this->formation->getNom());
        $this->assertEquals('Formation complète en data science et machine learning', $this->formation->getDescription());
        $this->assertEquals('12 mois', $this->formation->getDuree());
        $this->assertEquals('Bac+4', $this->formation->getNiveau());
        $this->assertCount(1, $this->formation->getPrerequis());
    }
}
