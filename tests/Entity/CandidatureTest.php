<?php

namespace App\Tests\Entity;

use App\Entity\Candidature;
use App\Entity\OffreStage;
use PHPUnit\Framework\TestCase;

class CandidatureTest extends TestCase
{
    private Candidature $candidature;

    protected function setUp(): void
    {
        $this->candidature = new Candidature();
    }

    public function testCreateCandidature(): void
    {
        // Test de création d'une candidature avec des données valides
        $offreStage = new OffreStage();
        $offreStage->setTitre('Développeur Web Stage');

        $this->candidature->setOffreStage($offreStage);
        $this->candidature->setNom('Dupont');
        $this->candidature->setPrenom('Jean');
        $this->candidature->setEmail('jean.dupont@example.com');
        $this->candidature->setTelephone('0612345678');
        $this->candidature->setNiveauEtude('Bac+3');
        $this->candidature->setCv('cv_jean_dupont.pdf');
        $this->candidature->setLettreMotivation('Lettre de motivation détaillée...');
        $this->candidature->setStatut('En attente');
        $this->candidature->setDateCandidature(new \DateTime('2023-12-15 14:30:00'));
        $this->candidature->setScoreIa(85);
        $this->candidature->setResumeIa('Candidat très motivé avec de bonnes compétences.');
        $this->candidature->setCompetencesDetectees(['PHP', 'JavaScript', 'React']);
        $this->candidature->setNoteAdmin(4);
        $this->candidature->setCommentaireAdmin('Excellent candidat');
        $this->candidature->setFavori(true);

        $this->assertEquals($offreStage, $this->candidature->getOffreStage());
        $this->assertEquals('Dupont', $this->candidature->getNom());
        $this->assertEquals('Jean', $this->candidature->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $this->candidature->getEmail());
        $this->assertEquals('0612345678', $this->candidature->getTelephone());
        $this->assertEquals('Bac+3', $this->candidature->getNiveauEtude());
        $this->assertEquals('cv_jean_dupont.pdf', $this->candidature->getCv());
        $this->assertEquals('Lettre de motivation détaillée...', $this->candidature->getLettreMotivation());
        $this->assertEquals('En attente', $this->candidature->getStatut());
        $this->assertEquals(new \DateTime('2023-12-15 14:30:00'), $this->candidature->getDateCandidature());
        $this->assertEquals(85, $this->candidature->getScoreIa());
        $this->assertEquals('Candidat très motivé avec de bonnes compétences.', $this->candidature->getResumeIa());
        $this->assertEquals(['PHP', 'JavaScript', 'React'], $this->candidature->getCompetencesDetectees());
        $this->assertEquals(4, $this->candidature->getNoteAdmin());
        $this->assertEquals('Excellent candidat', $this->candidature->getCommentaireAdmin());
        $this->assertTrue($this->candidature->isFavori());
    }

    public function testUpdateCandidature(): void
    {
        // Initialisation
        $offreStage1 = new OffreStage();
        $offreStage1->setTitre('Original Stage');

        $this->candidature->setOffreStage($offreStage1);
        $this->candidature->setNom('Original');
        $this->candidature->setPrenom('Original');
        $this->candidature->setEmail('original@example.com');
        $this->candidature->setStatut('En attente');
        $this->candidature->setScoreIa(70);
        $this->candidature->setNoteAdmin(3);
        $this->candidature->setFavori(false);

        // Mise à jour
        $offreStage2 = new OffreStage();
        $offreStage2->setTitre('Updated Stage');

        $this->candidature->setOffreStage($offreStage2);
        $this->candidature->setNom('Updated');
        $this->candidature->setPrenom('Updated');
        $this->candidature->setEmail('updated@example.com');
        $this->candidature->setStatut('Acceptée');
        $this->candidature->setScoreIa(90);
        $this->candidature->setNoteAdmin(5);
        $this->candidature->setFavori(true);

        // Vérification des mises à jour
        $this->assertEquals($offreStage2, $this->candidature->getOffreStage());
        $this->assertEquals('Updated', $this->candidature->getNom());
        $this->assertEquals('Updated', $this->candidature->getPrenom());
        $this->assertEquals('updated@example.com', $this->candidature->getEmail());
        $this->assertEquals('Acceptée', $this->candidature->getStatut());
        $this->assertEquals(90, $this->candidature->getScoreIa());
        $this->assertEquals(5, $this->candidature->getNoteAdmin());
        $this->assertTrue($this->candidature->isFavori());
    }

    public function testCandidatureOffreStageRelation(): void
    {
        // Test de la relation avec l'offre de stage
        $offreStage = new OffreStage();
        $offreStage->setTitre('Test Stage');

        $this->candidature->setOffreStage($offreStage);

        // Vérification
        $this->assertEquals($offreStage, $this->candidature->getOffreStage());
        $this->assertEquals('Test Stage', $this->candidature->getOffreStage()->getTitre());
    }

    public function testCandidatureStatutValidation(): void
    {
        // Test avec différents statuts
        $this->candidature->setStatut('En attente');
        $this->assertEquals('En attente', $this->candidature->getStatut());

        $this->candidature->setStatut('Acceptée');
        $this->assertEquals('Acceptée', $this->candidature->getStatut());

        $this->candidature->setStatut('Refusée');
        $this->assertEquals('Refusée', $this->candidature->getStatut());

        $this->candidature->setStatut('En cours');
        $this->assertEquals('En cours', $this->candidature->getStatut());
    }

    public function testCandidatureEmailValidation(): void
    {
        // Test avec différents emails
        $this->candidature->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $this->candidature->getEmail());

        $this->candidature->setEmail('user.name+tag@domain.co.uk');
        $this->assertEquals('user.name+tag@domain.co.uk', $this->candidature->getEmail());
    }

    public function testCandidatureDateCandidature(): void
    {
        // Test avec une date spécifique
        $date = new \DateTime('2023-06-15 14:30:00');
        $this->candidature->setDateCandidature($date);
        $this->assertEquals($date, $this->candidature->getDateCandidature());

        // Test avec la date actuelle
        $now = new \DateTime();
        $this->candidature->setDateCandidature($now);
        $this->assertEquals($now, $this->candidature->getDateCandidature());
    }

    public function testCandidatureScoreIa(): void
    {
        // Test avec différents scores
        $this->candidature->setScoreIa(50);
        $this->assertEquals(50, $this->candidature->getScoreIa());

        $this->candidature->setScoreIa(75);
        $this->assertEquals(75, $this->candidature->getScoreIa());

        $this->candidature->setScoreIa(100);
        $this->assertEquals(100, $this->candidature->getScoreIa());

        // Test avec null
        $this->candidature->setScoreIa(null);
        $this->assertNull($this->candidature->getScoreIa());
    }

    public function testCandidatureCompetencesDetectees(): void
    {
        // Test avec différentes compétences
        $competences = ['PHP', 'JavaScript', 'React', 'Symfony'];
        $this->candidature->setCompetencesDetectees($competences);
        $this->assertEquals($competences, $this->candidature->getCompetencesDetectees());

        // Test avec tableau vide
        $this->candidature->setCompetencesDetectees([]);
        $this->assertEquals([], $this->candidature->getCompetencesDetectees());

        // Test avec null
        $this->candidature->setCompetencesDetectees(null);
        $this->assertNull($this->candidature->getCompetencesDetectees());
    }

    public function testCandidatureNoteAdmin(): void
    {
        // Test avec différentes notes
        $this->candidature->setNoteAdmin(1);
        $this->assertEquals(1, $this->candidature->getNoteAdmin());

        $this->candidature->setNoteAdmin(3);
        $this->assertEquals(3, $this->candidature->getNoteAdmin());

        $this->candidature->setNoteAdmin(5);
        $this->assertEquals(5, $this->candidature->getNoteAdmin());

        // Test avec null
        $this->candidature->setNoteAdmin(null);
        $this->assertNull($this->candidature->getNoteAdmin());
    }

    public function testCandidatureFavori(): void
    {
        // Test avec favori true
        $this->candidature->setFavori(true);
        $this->assertTrue($this->candidature->isFavori());

        // Test avec favori false
        $this->candidature->setFavori(false);
        $this->assertFalse($this->candidature->isFavori());
    }

    public function testCandidatureNullableFields(): void
    {
        // Test des champs nullable
        $this->candidature->setTelephone(null);
        $this->candidature->setNiveauEtude(null);
        $this->candidature->setCv(null);
        $this->candidature->setLettreMotivation(null);
        $this->candidature->setScoreIa(null);
        $this->candidature->setResumeIa(null);
        $this->candidature->setCompetencesDetectees(null);
        $this->candidature->setNoteAdmin(null);
        $this->candidature->setCommentaireAdmin(null);

        $this->assertNull($this->candidature->getTelephone());
        $this->assertNull($this->candidature->getNiveauEtude());
        $this->assertNull($this->candidature->getCv());
        $this->assertNull($this->candidature->getLettreMotivation());
        $this->assertNull($this->candidature->getScoreIa());
        $this->assertNull($this->candidature->getResumeIa());
        $this->assertNull($this->candidature->getCompetencesDetectees());
        $this->assertNull($this->candidature->getNoteAdmin());
        $this->assertNull($this->candidature->getCommentaireAdmin());
    }

    public function testCandidatureDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->candidature->getId());
        $this->assertNull($this->candidature->getOffreStage());
        $this->assertNull($this->candidature->getNom());
        $this->assertNull($this->candidature->getPrenom());
        $this->assertNull($this->candidature->getEmail());
        $this->assertNull($this->candidature->getTelephone());
        $this->assertNull($this->candidature->getNiveauEtude());
        $this->assertNull($this->candidature->getCv());
        $this->assertNull($this->candidature->getLettreMotivation());
        $this->assertEquals('En attente', $this->candidature->getStatut()); // Valeur par défaut
        $this->assertNull($this->candidature->getDateCandidature());
        $this->assertNull($this->candidature->getScoreIa());
        $this->assertNull($this->candidature->getResumeIa());
        $this->assertNull($this->candidature->getCompetencesDetectees());
        $this->assertNull($this->candidature->getNoteAdmin());
        $this->assertNull($this->candidature->getCommentaireAdmin());
        $this->assertFalse($this->candidature->isFavori()); // Valeur par défaut
    }

    public function testCandidatureFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $offreStage = new OffreStage();
        $offreStage->setTitre('Stage Test');

        // Création
        $this->candidature->setOffreStage($offreStage);
        $this->candidature->setNom('Test');
        $this->candidature->setPrenom('User');
        $this->candidature->setEmail('test@example.com');
        $this->candidature->setStatut('En attente');
        $this->candidature->setDateCandidature(new \DateTime('2023-12-15 10:00:00'));

        // Vérification initiale
        $this->assertEquals('Test', $this->candidature->getNom());
        $this->assertEquals('User', $this->candidature->getPrenom());
        $this->assertEquals('test@example.com', $this->candidature->getEmail());
        $this->assertEquals('En attente', $this->candidature->getStatut());

        // Modification
        $this->candidature->setStatut('Acceptée');
        $this->candidature->setScoreIa(85);
        $this->candidature->setNoteAdmin(4);
        $this->candidature->setFavori(true);

        // Vérification après modification
        $this->assertEquals('Acceptée', $this->candidature->getStatut());
        $this->assertEquals(85, $this->candidature->getScoreIa());
        $this->assertEquals(4, $this->candidature->getNoteAdmin());
        $this->assertTrue($this->candidature->isFavori());
    }
}
