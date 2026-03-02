<?php

namespace App\Tests\Entity;

use App\Entity\Filiere;
use App\Entity\Metier;
use PHPUnit\Framework\TestCase;

class FiliereTest extends TestCase
{
    private Filiere $filiere;

    protected function setUp(): void
    {
        $this->filiere = new Filiere();
    }

    public function testCreateFiliere(): void
    {
        // Test de création d'une filière avec des données valides
        $this->filiere->setNom('Informatique');
        $this->filiere->setDescription('Formation en développement logiciel et systèmes');
        $this->filiere->setResponsable('Jean Dupont');
        $this->filiere->setImage('informatique.jpg');
        $this->filiere->setDateCreation(new \DateTime('2023-01-15'));

        $this->assertEquals('Informatique', $this->filiere->getNom());
        $this->assertEquals('Formation en développement logiciel et systèmes', $this->filiere->getDescription());
        $this->assertEquals('Jean Dupont', $this->filiere->getResponsable());
        $this->assertEquals('informatique.jpg', $this->filiere->getImage());
        $this->assertEquals(new \DateTime('2023-01-15'), $this->filiere->getDateCreation());
    }

    public function testUpdateFiliere(): void
    {
        // Initialisation
        $this->filiere->setNom('Original Name');
        $this->filiere->setDescription('Original description');
        $this->filiere->setResponsable('Original responsable');
        $this->filiere->setImage('original.jpg');

        // Mise à jour
        $this->filiere->setNom('Updated Name');
        $this->filiere->setDescription('Updated description');
        $this->filiere->setResponsable('Updated responsable');
        $this->filiere->setImage('updated.jpg');
        $this->filiere->setDateCreation(new \DateTime('2023-12-25'));

        // Vérification des mises à jour
        $this->assertEquals('Updated Name', $this->filiere->getNom());
        $this->assertEquals('Updated description', $this->filiere->getDescription());
        $this->assertEquals('Updated responsable', $this->filiere->getResponsable());
        $this->assertEquals('updated.jpg', $this->filiere->getImage());
        $this->assertEquals(new \DateTime('2023-12-25'), $this->filiere->getDateCreation());
    }

    public function testFiliereMetiersRelation(): void
    {
        // Test de la relation avec les métiers
        $metier1 = new Metier();
        $metier1->setNom('Développeur Web');
        $metier1->setDescription('Développement de sites web');

        $metier2 = new Metier();
        $metier2->setNom('Administrateur Système');
        $metier2->setDescription('Gestion de systèmes informatiques');

        // Ajout de métiers
        $this->filiere->addMetier($metier1);
        $this->filiere->addMetier($metier2);

        // Vérification
        $this->assertCount(2, $this->filiere->getMetiers());
        $this->assertTrue($this->filiere->getMetiers()->contains($metier1));
        $this->assertTrue($this->filiere->getMetiers()->contains($metier2));
        $this->assertEquals($this->filiere, $metier1->getFiliere());
        $this->assertEquals($this->filiere, $metier2->getFiliere());

        // Suppression d'un métier
        $this->filiere->removeMetier($metier1);
        $this->assertCount(1, $this->filiere->getMetiers());
        $this->assertFalse($this->filiere->getMetiers()->contains($metier1));
        $this->assertNull($metier1->getFiliere());
    }

    public function testFiliereNomValidation(): void
    {
        // Test avec différents noms
        $this->filiere->setNom('Informatique');
        $this->assertEquals('Informatique', $this->filiere->getNom());

        $this->filiere->setNom('Gestion');
        $this->assertEquals('Gestion', $this->filiere->getNom());

        $this->filiere->setNom('Marketing Digital');
        $this->assertEquals('Marketing Digital', $this->filiere->getNom());
    }

    public function testFiliereDescriptionValidation(): void
    {
        // Test avec différentes descriptions
        $this->filiere->setDescription('Description courte');
        $this->assertEquals('Description courte', $this->filiere->getDescription());

        $longDescription = 'Ceci est une description très longue qui contient beaucoup de détails sur la filière et les formations proposées.';
        $this->filiere->setDescription($longDescription);
        $this->assertEquals($longDescription, $this->filiere->getDescription());
    }

    public function testFiliereResponsableNullable(): void
    {
        // Test avec responsable
        $this->filiere->setResponsable('Jean Dupont');
        $this->assertEquals('Jean Dupont', $this->filiere->getResponsable());

        // Test avec responsable null
        $this->filiere->setResponsable(null);
        $this->assertNull($this->filiere->getResponsable());
    }

    public function testFiliereImageNullable(): void
    {
        // Test avec image
        $this->filiere->setImage('photo.jpg');
        $this->assertEquals('photo.jpg', $this->filiere->getImage());

        // Test avec image null
        $this->filiere->setImage(null);
        $this->assertNull($this->filiere->getImage());
    }

    public function testFiliereDateCreation(): void
    {
        // Test avec une date spécifique
        $date = new \DateTime('2023-06-15 10:30:00');
        $this->filiere->setDateCreation($date);
        $this->assertEquals($date, $this->filiere->getDateCreation());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->filiere->setDateCreation($now);
        $this->assertEquals($now, $this->filiere->getDateCreation());
    }

    public function testFiliereDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->filiere->getId());
        $this->assertNull($this->filiere->getNom());
        $this->assertNull($this->filiere->getDescription());
        $this->assertNull($this->filiere->getResponsable());
        $this->assertNull($this->filiere->getImage());
        $this->assertNotNull($this->filiere->getDateCreation()); // Initialisée dans le constructeur
        $this->assertCount(0, $this->filiere->getMetiers());
    }

    public function testFiliereMetiersCollection(): void
    {
        // Test que la collection est bien initialisée
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->filiere->getMetiers());
        $this->assertCount(0, $this->filiere->getMetiers());
    }

    public function testFiliereFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $metier = new Metier();
        $metier->setNom('Développeur');
        $metier->setDescription('Développement logiciel');

        // Création
        $this->filiere->setNom('Computer Science');
        $this->filiere->setDescription('Formation en informatique');
        $this->filiere->setResponsable('Dr. Smith');
        $this->filiere->addMetier($metier);

        // Vérification initiale
        $this->assertEquals('Computer Science', $this->filiere->getNom());
        $this->assertEquals('Formation en informatique', $this->filiere->getDescription());
        $this->assertEquals('Dr. Smith', $this->filiere->getResponsable());
        $this->assertCount(1, $this->filiere->getMetiers());

        // Modification
        $this->filiere->setNom('Computer Science & Engineering');
        $this->filiere->setDescription('Formation complète en informatique et ingénierie');
        $this->filiere->setResponsable('Dr. Johnson');

        // Vérification après modification
        $this->assertEquals('Computer Science & Engineering', $this->filiere->getNom());
        $this->assertEquals('Formation complète en informatique et ingénierie', $this->filiere->getDescription());
        $this->assertEquals('Dr. Johnson', $this->filiere->getResponsable());
        $this->assertCount(1, $this->filiere->getMetiers());
    }
}
