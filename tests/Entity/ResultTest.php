<?php

namespace App\Tests\Entity;

use App\Entity\Result;
use App\Entity\Quiz;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private Result $result;

    protected function setUp(): void
    {
        $this->result = new Result();
    }

    public function testCreateResult(): void
    {
        // Test de création d'un résultat avec des données valides
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $this->result->setQuiz($quiz);
        $this->result->setUtilisateur('john_doe');
        $this->result->setScore(85);

        $this->assertEquals($quiz, $this->result->getQuiz());
        $this->assertEquals('john_doe', $this->result->getUtilisateur());
        $this->assertEquals(85, $this->result->getScore());
        $this->assertNotNull($this->result->getDatepassage());
    }

    public function testUpdateResult(): void
    {
        // Initialisation
        $quiz = new Quiz();
        $quiz->setTitre('Original Quiz');

        $this->result->setQuiz($quiz);
        $this->result->setUtilisateur('original_user');
        $this->result->setScore(75);

        // Mise à jour
        $newQuiz = new Quiz();
        $newQuiz->setTitre('Updated Quiz');

        $this->result->setQuiz($newQuiz);
        $this->result->setUtilisateur('updated_user');
        $this->result->setScore(92);

        // Vérification des mises à jour
        $this->assertEquals($newQuiz, $this->result->getQuiz());
        $this->assertEquals('updated_user', $this->result->getUtilisateur());
        $this->assertEquals(92, $this->result->getScore());
    }

    public function testResultQuizRelation(): void
    {
        // Test de la relation avec le quiz
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $this->result->setQuiz($quiz);

        // Vérification
        $this->assertEquals($quiz, $this->result->getQuiz());
        
        // Pour tester la relation bidirectionnelle
        $quiz->addResult($this->result);
        $this->assertTrue($quiz->getResults()->contains($this->result));
    }

    public function testResultScoreValidation(): void
    {
        // Test avec différents scores
        $this->result->setScore(0);
        $this->assertEquals(0, $this->result->getScore());

        $this->result->setScore(50);
        $this->assertEquals(50, $this->result->getScore());

        $this->result->setScore(100);
        $this->assertEquals(100, $this->result->getScore());

        // Test avec un score négatif (possible selon le code actuel)
        $this->result->setScore(-10);
        $this->assertEquals(-10, $this->result->getScore());

        // Test avec un score supérieur à 100 (possible selon le code actuel)
        $this->result->setScore(150);
        $this->assertEquals(150, $this->result->getScore());
    }

    public function testResultUtilisateur(): void
    {
        // Test avec différents noms d'utilisateur
        $this->result->setUtilisateur('user123');
        $this->assertEquals('user123', $this->result->getUtilisateur());

        $this->result->setUtilisateur('john.doe@example.com');
        $this->assertEquals('john.doe@example.com', $this->result->getUtilisateur());

        $this->result->setUtilisateur('Jean Dupont');
        $this->assertEquals('Jean Dupont', $this->result->getUtilisateur());

        // Test avec une chaîne vide
        $this->result->setUtilisateur('');
        $this->assertEquals('', $this->result->getUtilisateur());
    }

    public function testResultDatePassage(): void
    {
        // Test de la date de passage
        $defaultDate = $this->result->getDatepassage();
        $this->assertNotNull($defaultDate);
        $this->assertInstanceOf(\DateTimeInterface::class, $defaultDate);

        // Test avec une date personnalisée
        $customDate = new \DateTime('2023-12-25 15:30:00');
        $this->result->setDatepassage($customDate);
        $this->assertEquals($customDate, $this->result->getDatepassage());
    }

    public function testResultDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertNull($this->result->getQuiz());
        $this->assertNull($this->result->getUtilisateur());
        $this->assertNull($this->result->getScore());
        $this->assertNotNull($this->result->getDatepassage());
    }

    public function testResultWithNullQuiz(): void
    {
        // Test avec un quiz null
        $this->result->setQuiz(null);
        $this->assertNull($this->result->getQuiz());

        // Test setter avec null
        $this->result->setQuiz(null);
        $this->assertNull($this->result->getQuiz());
    }

    public function testResultIdGeneration(): void
    {
        // Test que l'ID est null par défaut (généré par Doctrine)
        // La propriété idresult n'est pas initialisée par défaut
        $this->assertNull($this->result->getIdresult());
    }

    public function testResultFullWorkflow(): void
    {
        // Test du workflow complet de création et modification
        $quiz = new Quiz();
        $quiz->setTitre('Workflow Test Quiz');

        // Création
        $this->result->setQuiz($quiz);
        $this->result->setUtilisateur('workflow_user');
        $this->result->setScore(88);

        // Vérification initiale
        $this->assertEquals($quiz, $this->result->getQuiz());
        $this->assertEquals('workflow_user', $this->result->getUtilisateur());
        $this->assertEquals(88, $this->result->getScore());

        // Modification
        $this->result->setScore(95);
        $this->result->setUtilisateur('updated_workflow_user');

        // Vérification après modification
        $this->assertEquals(95, $this->result->getScore());
        $this->assertEquals('updated_workflow_user', $this->result->getUtilisateur());
        $this->assertEquals($quiz, $this->result->getQuiz());
    }
}
