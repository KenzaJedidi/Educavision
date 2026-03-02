<?php

namespace App\Tests\Entity;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use PHPUnit\Framework\TestCase;

class ReclamationTest extends TestCase
{
    private Reclamation $reclamation;

    protected function setUp(): void
    {
        $this->reclamation = new Reclamation();
    }

    public function testCreateReclamation(): void
    {
        // Test de création d'une réclamation avec des données valides
        $this->reclamation->setNom('Dupont');
        $this->reclamation->setPrenom('Jean');
        $this->reclamation->setEmail('jean.dupont@example.com');
        $this->reclamation->setRole('etudiant');
        $this->reclamation->setStatus('en cours de traitement');
        $this->reclamation->setTitre('Problème de connexion');
        $this->reclamation->setDescription('Je ne peux pas me connecter à la plateforme depuis hier.');
        $this->reclamation->setDateReclamation(new \DateTime('2023-12-15 10:30:00'));

        $this->assertEquals('Dupont', $this->reclamation->getNom());
        $this->assertEquals('Jean', $this->reclamation->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $this->reclamation->getEmail());
        $this->assertEquals('etudiant', $this->reclamation->getRole());
        $this->assertEquals('en cours de traitement', $this->reclamation->getStatus());
        $this->assertEquals('Problème de connexion', $this->reclamation->getTitre());
        $this->assertEquals('Je ne peux pas me connecter à la plateforme depuis hier.', $this->reclamation->getDescription());
        $this->assertEquals(new \DateTime('2023-12-15 10:30:00'), $this->reclamation->getDateReclamation());
    }

    public function testUpdateReclamation(): void
    {
        // Initialisation
        $this->reclamation->setNom('Original');
        $this->reclamation->setPrenom('Original');
        $this->reclamation->setEmail('original@example.com');
        $this->reclamation->setRole('etudiant');
        $this->reclamation->setStatus('en cours de traitement');
        $this->reclamation->setTitre('Original titre');
        $this->reclamation->setDescription('Original description');

        // Mise à jour
        $this->reclamation->setNom('Updated');
        $this->reclamation->setPrenom('Updated');
        $this->reclamation->setEmail('updated@example.com');
        $this->reclamation->setRole('professeur');
        $this->reclamation->setStatus('traiter');
        $this->reclamation->setTitre('Updated titre');
        $this->reclamation->setDescription('Updated description');

        // Vérification des mises à jour
        $this->assertEquals('Updated', $this->reclamation->getNom());
        $this->assertEquals('Updated', $this->reclamation->getPrenom());
        $this->assertEquals('updated@example.com', $this->reclamation->getEmail());
        $this->assertEquals('professeur', $this->reclamation->getRole());
        $this->assertEquals('traiter', $this->reclamation->getStatus());
        $this->assertEquals('Updated titre', $this->reclamation->getTitre());
        $this->assertEquals('Updated description', $this->reclamation->getDescription());
    }

    public function testReclamationReponsesRelation(): void
    {
        // Test de la relation avec les réponses
        $reponse1 = new Reponse();
        $reponse1->setContenu('Première réponse');
        $reponse1->setDateReponse(new \DateTime('2023-12-15 14:00:00'));

        $reponse2 = new Reponse();
        $reponse2->setContenu('Deuxième réponse');
        $reponse2->setDateReponse(new \DateTime('2023-12-15 16:00:00'));

        // Ajout de réponses
        $this->reclamation->addReponse($reponse1);
        $this->reclamation->addReponse($reponse2);

        // Vérification
        $this->assertCount(2, $this->reclamation->getReponses());
        $this->assertTrue($this->reclamation->getReponses()->contains($reponse1));
        $this->assertTrue($this->reclamation->getReponses()->contains($reponse2));
        $this->assertEquals($this->reclamation, $reponse1->getReclamation());
        $this->assertEquals($this->reclamation, $reponse2->getReclamation());
        $this->assertEquals('traiter', $this->reclamation->getStatus()); // Statut changé automatiquement

        // Suppression d'une réponse
        $this->reclamation->removeReponse($reponse1);
        $this->assertCount(1, $this->reclamation->getReponses());
        $this->assertFalse($this->reclamation->getReponses()->contains($reponse1));
        $this->assertNull($reponse1->getReclamation());
        $this->assertEquals('traiter', $this->reclamation->getStatus()); // Toujours traiter car il reste une réponse

        // Suppression de la dernière réponse
        $this->reclamation->removeReponse($reponse2);
        $this->assertCount(0, $this->reclamation->getReponses());
        $this->assertEquals('en cours de traitement', $this->reclamation->getStatus()); // Retour au statut initial
    }

    public function testReclamationAutoFields(): void
    {
        // Test des champs automatiques
        $this->reclamation->setResumeAuto('Résumé automatique généré par IA');
        $this->reclamation->setCategory('Technique');
        $this->reclamation->setSentimentAuto('Négatif');
        $this->reclamation->setTempsResolutionAuto(48);

        $this->assertEquals('Résumé automatique généré par IA', $this->reclamation->getResumeAuto());
        $this->assertEquals('Technique', $this->reclamation->getCategory());
        $this->assertEquals('Négatif', $this->reclamation->getSentimentAuto());
        $this->assertEquals(48, $this->reclamation->getTempsResolutionAuto());

        // Test avec null
        $this->reclamation->setResumeAuto(null);
        $this->reclamation->setCategory(null);
        $this->reclamation->setSentimentAuto(null);
        $this->reclamation->setTempsResolutionAuto(null);

        $this->assertNull($this->reclamation->getResumeAuto());
        $this->assertNull($this->reclamation->getCategory());
        $this->assertNull($this->reclamation->getSentimentAuto());
        $this->assertNull($this->reclamation->getTempsResolutionAuto());
    }

    public function testReclamationStatusValidation(): void
    {
        // Test avec les statuts valides
        $this->reclamation->setStatus('en cours de traitement');
        $this->assertEquals('en cours de traitement', $this->reclamation->getStatus());

        $this->reclamation->setStatus('traiter');
        $this->assertEquals('traiter', $this->reclamation->getStatus());
    }

    public function testReclamationRoleValidation(): void
    {
        // Test avec différents rôles
        $this->reclamation->setRole('etudiant');
        $this->assertEquals('etudiant', $this->reclamation->getRole());

        $this->reclamation->setRole('professeur');
        $this->assertEquals('professeur', $this->reclamation->getRole());

        // Test avec null
        $this->reclamation->setRole(null);
        $this->assertNull($this->reclamation->getRole());
    }

    public function testReclamationEmailValidation(): void
    {
        // Test avec différents emails
        $this->reclamation->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $this->reclamation->getEmail());

        $this->reclamation->setEmail('user.name+tag@domain.co.uk');
        $this->assertEquals('user.name+tag@domain.co.uk', $this->reclamation->getEmail());

        // Test avec null
        $this->reclamation->setEmail(null);
        $this->assertNull($this->reclamation->getEmail());
    }

    public function testReclamationDateReclamation(): void
    {
        // Test avec une date spécifique
        $date = new \DateTime('2023-06-15 14:30:00');
        $this->reclamation->setDateReclamation($date);
        $this->assertEquals($date, $this->reclamation->getDateReclamation());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->reclamation->setDateReclamation($now);
        $this->assertEquals($now, $this->reclamation->getDateReclamation());
    }

    public function testReclamationDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->reclamation->getId());
        $this->assertNull($this->reclamation->getNom()); // Non initialisé
        $this->assertNull($this->reclamation->getPrenom()); // Non initialisé
        $this->assertNull($this->reclamation->getEmail()); // Non initialisé
        $this->assertNull($this->reclamation->getRole()); // Non initialisé
        $this->assertEquals('en cours de traitement', $this->reclamation->getStatus()); // Valeur par défaut
        $this->assertNull($this->reclamation->getTitre()); // Non initialisé
        $this->assertNull($this->reclamation->getDescription()); // Non initialisé
        $this->assertNull($this->reclamation->getDateReclamation()); // Non initialisé
        $this->assertCount(0, $this->reclamation->getReponses());
    }

    public function testReclamationReponsesCollection(): void
    {
        // Test que la collection est bien initialisée
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->reclamation->getReponses());
        $this->assertCount(0, $this->reclamation->getReponses());
    }

    public function testReclamationFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $reponse = new Reponse();
        $reponse->setContenu('Réponse de test');
        $reponse->setDateReponse(new \DateTime('2023-12-15 15:00:00'));

        // Création
        $this->reclamation->setNom('Test');
        $this->reclamation->setPrenom('User');
        $this->reclamation->setEmail('test@example.com');
        $this->reclamation->setRole('etudiant');
        $this->reclamation->setTitre('Test réclamation');
        $this->reclamation->setDescription('Description de test');
        $this->reclamation->setDateReclamation(new \DateTime('2023-12-15 10:00:00'));

        // Vérification initiale
        $this->assertEquals('Test', $this->reclamation->getNom());
        $this->assertEquals('User', $this->reclamation->getPrenom());
        $this->assertEquals('test@example.com', $this->reclamation->getEmail());
        $this->assertEquals('etudiant', $this->reclamation->getRole());
        $this->assertEquals('Test réclamation', $this->reclamation->getTitre());
        $this->assertEquals('Description de test', $this->reclamation->getDescription());
        $this->assertEquals('en cours de traitement', $this->reclamation->getStatus());

        // Ajout d'une réponse (doit changer le statut)
        $this->reclamation->addReponse($reponse);
        $this->assertEquals('traiter', $this->reclamation->getStatus());
        $this->assertCount(1, $this->reclamation->getReponses());

        // Modification
        $this->reclamation->setNom('Updated Test');
        $this->reclamation->setPrenom('Updated User');
        $this->reclamation->setEmail('updated@example.com');
        $this->reclamation->setRole('professeur');

        // Vérification après modification
        $this->assertEquals('Updated Test', $this->reclamation->getNom());
        $this->assertEquals('Updated User', $this->reclamation->getPrenom());
        $this->assertEquals('updated@example.com', $this->reclamation->getEmail());
        $this->assertEquals('professeur', $this->reclamation->getRole());
        $this->assertEquals('traiter', $this->reclamation->getStatus()); // Statut maintenu
        $this->assertCount(1, $this->reclamation->getReponses());
    }
}
