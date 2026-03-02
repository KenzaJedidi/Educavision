<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private Utilisateur $utilisateur;

    protected function setUp(): void
    {
        $this->utilisateur = new Utilisateur();
    }

    public function testCreateUtilisateur(): void
    {
        // Test de création d'un utilisateur avec des données valides
        $this->utilisateur->setNom('Dupont');
        $this->utilisateur->setPrenom('Jean');
        $this->utilisateur->setEmail('jean.dupont@example.com');
        $this->utilisateur->setMotDePasse('password123');
        $this->utilisateur->setRole('etudiant');
        $this->utilisateur->setTelephone('0612345678');
        $this->utilisateur->setAdresse('123 Rue de la République, 75001 Paris');
        $this->utilisateur->setActif(true);
        $this->utilisateur->setDateInscription(new \DateTime('2023-12-15 10:30:00'));
        $this->utilisateur->setDateModification(new \DateTime('2023-12-15 10:30:00'));

        $this->assertEquals('Dupont', $this->utilisateur->getNom());
        $this->assertEquals('Jean', $this->utilisateur->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $this->utilisateur->getEmail());
        $this->assertEquals('password123', $this->utilisateur->getMotDePasse());
        $this->assertEquals('etudiant', $this->utilisateur->getRole());
        $this->assertEquals('0612345678', $this->utilisateur->getTelephone());
        $this->assertEquals('123 Rue de la République, 75001 Paris', $this->utilisateur->getAdresse());
        $this->assertTrue($this->utilisateur->isActif());
        $this->assertEquals(new \DateTime('2023-12-15 10:30:00'), $this->utilisateur->getDateInscription());
        $this->assertEquals(new \DateTime('2023-12-15 10:30:00'), $this->utilisateur->getDateModification());
    }

    public function testUpdateUtilisateur(): void
    {
        // Initialisation
        $this->utilisateur->setNom('Original');
        $this->utilisateur->setPrenom('Original');
        $this->utilisateur->setEmail('original@example.com');
        $this->utilisateur->setMotDePasse('originalpass');
        $this->utilisateur->setRole('etudiant');
        $this->utilisateur->setActif(true);

        // Mise à jour
        $this->utilisateur->setNom('Updated');
        $this->utilisateur->setPrenom('Updated');
        $this->utilisateur->setEmail('updated@example.com');
        $this->utilisateur->setMotDePasse('newpassword');
        $this->utilisateur->setRole('professeur');
        $this->utilisateur->setActif(false);

        // Vérification des mises à jour
        $this->assertEquals('Updated', $this->utilisateur->getNom());
        $this->assertEquals('Updated', $this->utilisateur->getPrenom());
        $this->assertEquals('updated@example.com', $this->utilisateur->getEmail());
        $this->assertEquals('newpassword', $this->utilisateur->getMotDePasse());
        $this->assertEquals('professeur', $this->utilisateur->getRole());
        $this->assertFalse($this->utilisateur->isActif());
    }

    public function testUtilisateurRoleValidation(): void
    {
        // Test avec les rôles valides
        $this->utilisateur->setRole('etudiant');
        $this->assertEquals('etudiant', $this->utilisateur->getRole());

        $this->utilisateur->setRole('professeur');
        $this->assertEquals('professeur', $this->utilisateur->getRole());

        $this->utilisateur->setRole('admin');
        $this->assertEquals('admin', $this->utilisateur->getRole());

        // Test avec null
        $this->utilisateur->setRole(null);
        $this->assertNull($this->utilisateur->getRole());
    }

    public function testUtilisateurEmailValidation(): void
    {
        // Test avec différents emails
        $this->utilisateur->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $this->utilisateur->getEmail());

        $this->utilisateur->setEmail('user.name+tag@domain.co.uk');
        $this->assertEquals('user.name+tag@domain.co.uk', $this->utilisateur->getEmail());

        // Test avec null
        $this->utilisateur->setEmail(null);
        $this->assertNull($this->utilisateur->getEmail());
    }

    public function testUtilisateurPasswordValidation(): void
    {
        // Test avec différents mots de passe
        $this->utilisateur->setMotDePasse('simple123');
        $this->assertEquals('simple123', $this->utilisateur->getMotDePasse());

        $this->utilisateur->setMotDePasse('ComplexP@ssw0rd!');
        $this->assertEquals('ComplexP@ssw0rd!', $this->utilisateur->getMotDePasse());

        $this->utilisateur->setMotDePasse('');
        $this->assertEquals('', $this->utilisateur->getMotDePasse());
    }

    public function testUtilisateurFullName(): void
    {
        // Test du nom complet
        $this->utilisateur->setNom('Dupont');
        $this->utilisateur->setPrenom('Jean');
        $this->assertEquals('Jean Dupont', $this->utilisateur->getFullName());

        // Test avec des noms avec espaces
        $this->utilisateur->setNom('De La Croix');
        $this->utilisateur->setPrenom('Marie Claire');
        $this->assertEquals('Marie Claire De La Croix', $this->utilisateur->getFullName());
    }

    public function testUtilisateurUserInterface(): void
    {
        // Test de l'interface UserInterface
        $this->utilisateur->setEmail('test@example.com');
        $this->utilisateur->setRole('etudiant');

        // Test getUserIdentifier
        $this->assertEquals('test@example.com', $this->utilisateur->getUserIdentifier());

        // Test getRoles pour étudiant
        $roles = $this->utilisateur->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ETUDIANT', $roles);

        // Test getRoles pour professeur
        $this->utilisateur->setRole('professeur');
        $roles = $this->utilisateur->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_PROF', $roles);

        // Test getRoles pour admin
        $this->utilisateur->setRole('admin');
        $roles = $this->utilisateur->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);

        // Test getPassword
        $this->utilisateur->setMotDePasse('hashedpassword');
        $this->assertEquals('hashedpassword', $this->utilisateur->getPassword());

        // Test eraseCredentials (ne doit pas lever d'exception)
        $this->utilisateur->eraseCredentials();
        $this->assertTrue(true); // Si on arrive ici, pas d'exception
    }

    public function testUtilisateurFaceIdMethods(): void
    {
        // Test des méthodes Face ID
        $this->utilisateur->setFaceIdToken('face_token_123');
        $this->utilisateur->setFaceIdEnrolled(true);
        $this->utilisateur->setFaceIdEnrollmentDate(new \DateTime('2023-12-15 14:00:00'));

        $this->assertEquals('face_token_123', $this->utilisateur->getFaceIdToken());
        $this->assertTrue($this->utilisateur->isFaceIdEnrolled());
        $this->assertEquals(new \DateTime('2023-12-15 14:00:00'), $this->utilisateur->getFaceIdEnrollmentDate());

        // Test avec null
        $this->utilisateur->setFaceIdToken(null);
        $this->utilisateur->setFaceIdEnrolled(false);
        $this->utilisateur->setFaceIdEnrollmentDate(null);

        $this->assertNull($this->utilisateur->getFaceIdToken());
        $this->assertFalse($this->utilisateur->isFaceIdEnrolled());
        $this->assertNull($this->utilisateur->getFaceIdEnrollmentDate());
    }

    public function testUtilisateurBanMethods(): void
    {
        // Test des méthodes de bannissement
        $this->utilisateur->setBanUntil(new \DateTime('2024-01-15 23:59:59'));
        $this->utilisateur->setBanReason('Violation des conditions d\'utilisation');

        $this->assertEquals(new \DateTime('2024-01-15 23:59:59'), $this->utilisateur->getBanUntil());
        $this->assertEquals('Violation des conditions d\'utilisation', $this->utilisateur->getBanReason());

        // Test avec null
        $this->utilisateur->setBanUntil(null);
        $this->utilisateur->setBanReason(null);

        $this->assertNull($this->utilisateur->getBanUntil());
        $this->assertNull($this->utilisateur->getBanReason());
    }

    public function testUtilisateurResetToken(): void
    {
        // Test du token de réinitialisation
        $this->utilisateur->setResetToken('reset_token_abc123');
        $this->assertEquals('reset_token_abc123', $this->utilisateur->getResetToken());

        // Test avec null
        $this->utilisateur->setResetToken(null);
        $this->assertNull($this->utilisateur->getResetToken());
    }

    public function testUtilisateurNullableFields(): void
    {
        // Test des champs nullable
        $this->utilisateur->setTelephone(null);
        $this->utilisateur->setAdresse(null);
        $this->utilisateur->setActif(null);

        $this->assertNull($this->utilisateur->getTelephone());
        $this->assertNull($this->utilisateur->getAdresse());
        $this->assertNull($this->utilisateur->isActif());
    }

    public function testUtilisateurDateMethods(): void
    {
        // Test des méthodes de date
        $dateInscription = new \DateTime('2023-01-15 10:00:00');
        $dateModification = new \DateTime('2023-12-15 15:30:00');

        $this->utilisateur->setDateInscription($dateInscription);
        $this->utilisateur->setDateModification($dateModification);

        $this->assertEquals($dateInscription, $this->utilisateur->getDateInscription());
        $this->assertEquals($dateModification, $this->utilisateur->getDateModification());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->utilisateur->setDateModification($now);
        $this->assertEquals($now, $this->utilisateur->getDateModification());
    }

    public function testUtilisateurDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->utilisateur->getId());
        $this->assertNull($this->utilisateur->getNom());
        $this->assertNull($this->utilisateur->getPrenom());
        $this->assertNull($this->utilisateur->getEmail());
        $this->assertNull($this->utilisateur->getMotDePasse());
        $this->assertEquals('etudiant', $this->utilisateur->getRole()); // Valeur par défaut
        $this->assertNull($this->utilisateur->getTelephone());
        $this->assertNull($this->utilisateur->getAdresse());
        $this->assertTrue($this->utilisateur->isActif()); // Valeur par défaut
        $this->assertNull($this->utilisateur->getDateInscription());
        $this->assertNull($this->utilisateur->getDateModification());
        $this->assertNull($this->utilisateur->getFaceIdToken());
        $this->assertFalse($this->utilisateur->isFaceIdEnrolled()); // Valeur par défaut
        $this->assertNull($this->utilisateur->getFaceIdEnrollmentDate());
        $this->assertNull($this->utilisateur->getBanUntil());
        $this->assertNull($this->utilisateur->getBanReason());
        $this->assertNull($this->utilisateur->getResetToken());
    }

    public function testUtilisateurFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        // Création
        $this->utilisateur->setNom('Test');
        $this->utilisateur->setPrenom('User');
        $this->utilisateur->setEmail('test@example.com');
        $this->utilisateur->setMotDePasse('password123');
        $this->utilisateur->setRole('etudiant');
        $this->utilisateur->setActif(true);
        $this->utilisateur->setDateInscription(new \DateTime('2023-12-15 10:00:00'));
        $this->utilisateur->setDateModification(new \DateTime('2023-12-15 10:00:00'));

        // Vérification initiale
        $this->assertEquals('Test', $this->utilisateur->getNom());
        $this->assertEquals('User', $this->utilisateur->getPrenom());
        $this->assertEquals('test@example.com', $this->utilisateur->getEmail());
        $this->assertEquals('etudiant', $this->utilisateur->getRole());
        $this->assertTrue($this->utilisateur->isActif());

        // Modification
        $this->utilisateur->setRole('professeur');
        $this->utilisateur->setActif(false);
        $this->utilisateur->setDateModification(new \DateTime('2023-12-20 15:00:00'));
        $this->utilisateur->setFaceIdEnrolled(true);
        $this->utilisateur->setFaceIdEnrollmentDate(new \DateTime('2023-12-20 15:00:00'));

        // Vérification après modification
        $this->assertEquals('professeur', $this->utilisateur->getRole());
        $this->assertFalse($this->utilisateur->isActif());
        $this->assertEquals(new \DateTime('2023-12-20 15:00:00'), $this->utilisateur->getDateModification());
        $this->assertTrue($this->utilisateur->isFaceIdEnrolled());
        $this->assertEquals(new \DateTime('2023-12-20 15:00:00'), $this->utilisateur->getFaceIdEnrollmentDate());
    }
}
