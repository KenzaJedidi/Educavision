<?php

namespace App\Tests\Entity;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use PHPUnit\Framework\TestCase;

class ReponseTest extends TestCase
{
    private Reponse $reponse;

    protected function setUp(): void
    {
        $this->reponse = new Reponse();
    }

    public function testCreateReponse(): void
    {
        // Test de création d'une réponse avec des données valides
        $reclamation = new Reclamation();
        $reclamation->setTitre('Test réclamation');

        $this->reponse->setReclamation($reclamation);
        $this->reponse->setContenu('Ceci est une réponse de test pour la réclamation.');
        $this->reponse->setDateReponse(new \DateTime('2023-12-15 14:30:00'));
        $this->reponse->setRating(5);

        $this->assertEquals($reclamation, $this->reponse->getReclamation());
        $this->assertEquals('Ceci est une réponse de test pour la réclamation.', $this->reponse->getContenu());
        $this->assertEquals(new \DateTime('2023-12-15 14:30:00'), $this->reponse->getDateReponse());
        $this->assertEquals(5, $this->reponse->getRating());
    }

    public function testUpdateReponse(): void
    {
        // Initialisation
        $reclamation1 = new Reclamation();
        $reclamation1->setTitre('Original réclamation');

        $this->reponse->setReclamation($reclamation1);
        $this->reponse->setContenu('Original contenu');
        $this->reponse->setDateReponse(new \DateTime('2023-12-15 10:00:00'));
        $this->reponse->setRating(3);

        // Mise à jour
        $reclamation2 = new Reclamation();
        $reclamation2->setTitre('Updated réclamation');

        $this->reponse->setReclamation($reclamation2);
        $this->reponse->setContenu('Updated contenu');
        $this->reponse->setDateReponse(new \DateTime('2023-12-15 16:00:00'));
        $this->reponse->setRating(4);

        // Vérification des mises à jour
        $this->assertEquals($reclamation2, $this->reponse->getReclamation());
        $this->assertEquals('Updated contenu', $this->reponse->getContenu());
        $this->assertEquals(new \DateTime('2023-12-15 16:00:00'), $this->reponse->getDateReponse());
        $this->assertEquals(4, $this->reponse->getRating());
    }

    public function testReponseReclamationRelation(): void
    {
        // Test de la relation avec la réclamation
        $reclamation = new Reclamation();
        $reclamation->setTitre('Test réclamation');

        $this->reponse->setReclamation($reclamation);

        // Vérification
        $this->assertEquals($reclamation, $this->reponse->getReclamation());
        
        // Pour tester la relation bidirectionnelle
        $reclamation->addReponse($this->reponse);
        $this->assertTrue($reclamation->getReponses()->contains($this->reponse));
        $this->assertEquals('traiter', $reclamation->getStatus()); // Statut changé automatiquement
    }

    public function testReponseContenuValidation(): void
    {
        // Test avec différents contenus
        $this->reponse->setContenu('Réponse courte');
        $this->assertEquals('Réponse courte', $this->reponse->getContenu());

        $longContenu = 'Ceci est une réponse très détaillée qui contient beaucoup d\'informations et d\'explications pour aider l\'utilisateur à résoudre son problème.';
        $this->reponse->setContenu($longContenu);
        $this->assertEquals($longContenu, $this->reponse->getContenu());

        // Test avec null
        $this->reponse->setContenu(null);
        $this->assertNull($this->reponse->getContenu());
    }

    public function testReponseDateReponse(): void
    {
        // Test avec une date spécifique
        $date = new \DateTime('2023-06-15 10:30:00');
        $this->reponse->setDateReponse($date);
        $this->assertEquals($date, $this->reponse->getDateReponse());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->reponse->setDateReponse($now);
        $this->assertEquals($now, $this->reponse->getDateReponse());
    }

    public function testReponseRatingValidation(): void
    {
        // Test avec différentes notes
        $this->reponse->setRating(1);
        $this->assertEquals(1, $this->reponse->getRating());

        $this->reponse->setRating(3);
        $this->assertEquals(3, $this->reponse->getRating());

        $this->reponse->setRating(5);
        $this->assertEquals(5, $this->reponse->getRating());

        // Test avec null
        $this->reponse->setRating(null);
        $this->assertNull($this->reponse->getRating());

        // Test avec 0 (note minimale)
        $this->reponse->setRating(0);
        $this->assertEquals(0, $this->reponse->getRating());
    }

    public function testReponseWithNullReclamation(): void
    {
        // Test avec une réclamation null
        $this->reponse->setReclamation(null);
        $this->assertNull($this->reponse->getReclamation());

        // Test setter avec null
        $this->reponse->setReclamation(null);
        $this->assertNull($this->reponse->getReclamation());
    }

    public function testReponseDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->reponse->getId());
        $this->assertNull($this->reponse->getReclamation()); // Non initialisé
        $this->assertNull($this->reponse->getContenu()); // Non initialisé
        $this->assertNull($this->reponse->getDateReponse()); // Non initialisé
        $this->assertNull($this->reponse->getRating());
    }

    public function testReponseIdGeneration(): void
    {
        // Test que l'ID est null par défaut (généré par Doctrine)
        $this->assertNull($this->reponse->getId());
    }

    public function testReponseFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $reclamation1 = new Reclamation();
        $reclamation1->setTitre('Workflow Test Reclamation');

        $reclamation2 = new Reclamation();
        $reclamation2->setTitre('Updated Workflow Reclamation');

        // Création
        $this->reponse->setReclamation($reclamation1);
        $this->reponse->setContenu('Réponse initiale');
        $this->reponse->setDateReponse(new \DateTime('2023-12-15 10:00:00'));
        $this->reponse->setRating(4);

        // Vérification initiale
        $this->assertEquals($reclamation1, $this->reponse->getReclamation());
        $this->assertEquals('Réponse initiale', $this->reponse->getContenu());
        $this->assertEquals(4, $this->reponse->getRating());

        // Modification
        $this->reponse->setReclamation($reclamation2);
        $this->reponse->setContenu('Réponse mise à jour');
        $this->reponse->setDateReponse(new \DateTime('2023-12-15 15:00:00'));
        $this->reponse->setRating(5);

        // Vérification après modification
        $this->assertEquals($reclamation2, $this->reponse->getReclamation());
        $this->assertEquals('Réponse mise à jour', $this->reponse->getContenu());
        $this->assertEquals(5, $this->reponse->getRating());
    }

    public function testReponseRelationConsistency(): void
    {
        // Test de la cohérence des relations
        $reclamation1 = new Reclamation();
        $reclamation1->setTitre('Test Reclamation 1');

        $reclamation2 = new Reclamation();
        $reclamation2->setTitre('Test Reclamation 2');

        $reponse2 = new Reponse();
        $reponse2->setContenu('Autre réponse');

        // Association avec la première réclamation
        $this->reponse->setReclamation($reclamation1);
        $reclamation1->addReponse($this->reponse);

        // Vérification
        $this->assertEquals($reclamation1, $this->reponse->getReclamation());
        $this->assertTrue($reclamation1->getReponses()->contains($this->reponse));
        $this->assertEquals('traiter', $reclamation1->getStatus());

        // Changement de réclamation
        $this->reponse->setReclamation($reclamation2);
        $reclamation2->addReponse($this->reponse);

        // Vérification après changement
        $this->assertEquals($reclamation2, $this->reponse->getReclamation());
        $this->assertTrue($reclamation2->getReponses()->contains($this->reponse));
        $this->assertEquals('traiter', $reclamation2->getStatus());
    }
}
