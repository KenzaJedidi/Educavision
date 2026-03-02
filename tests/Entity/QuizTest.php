<?php

namespace App\Tests\Entity;

use App\Entity\Quiz;
use App\Entity\Chapter;
use App\Entity\Result;
use App\Entity\Question;
use PHPUnit\Framework\TestCase;

class QuizTest extends TestCase
{
    private Quiz $quiz;

    protected function setUp(): void
    {
        $this->quiz = new Quiz();
    }

    public function testCreateQuiz(): void
    {
        // Test de création d'un quiz avec des données valides
        $this->quiz->setTitre('Test Quiz');
        $this->quiz->setDescription('Test description');
        $this->quiz->setVisible(true);
        $this->quiz->setDuree(30);
        $this->quiz->setStatus('published');
        $this->quiz->setDifficultyLevel('Moyen');
        $this->quiz->setTimeLimit(1800);
        $this->quiz->setNumberOfQuestions(10);

        $this->assertEquals('Test Quiz', $this->quiz->getTitre());
        $this->assertEquals('Test description', $this->quiz->getDescription());
        $this->assertTrue($this->quiz->isVisible());
        $this->assertEquals(30, $this->quiz->getDuree());
        $this->assertEquals('published', $this->quiz->getStatus());
        $this->assertEquals('Moyen', $this->quiz->getDifficultyLevel());
        $this->assertEquals(1800, $this->quiz->getTimeLimit());
        $this->assertEquals(10, $this->quiz->getNumberOfQuestions());
        $this->assertNotNull($this->quiz->getDatecreation());
    }

    public function testUpdateQuiz(): void
    {
        // Initialisation
        $this->quiz->setTitre('Original Title');
        $this->quiz->setDescription('Original description');
        $this->quiz->setVisible(false);
        $this->quiz->setDuree(15);
        $this->quiz->setStatus('draft');
        $this->quiz->setDifficultyLevel('Facile');

        // Mise à jour
        $this->quiz->setTitre('Updated Title');
        $this->quiz->setDescription('Updated description');
        $this->quiz->setVisible(true);
        $this->quiz->setDuree(45);
        $this->quiz->setStatus('published');
        $this->quiz->setDifficultyLevel('Difficile');
        $this->quiz->setTimeLimit(2700);
        $this->quiz->setNumberOfQuestions(20);
        $this->quiz->setAttempts(5);

        // Vérification des mises à jour
        $this->assertEquals('Updated Title', $this->quiz->getTitre());
        $this->assertEquals('Updated description', $this->quiz->getDescription());
        $this->assertTrue($this->quiz->isVisible());
        $this->assertEquals(45, $this->quiz->getDuree());
        $this->assertEquals('published', $this->quiz->getStatus());
        $this->assertEquals('Difficile', $this->quiz->getDifficultyLevel());
        $this->assertEquals(2700, $this->quiz->getTimeLimit());
        $this->assertEquals(20, $this->quiz->getNumberOfQuestions());
        $this->assertEquals(5, $this->quiz->getAttempts());
    }

    public function testQuizChapterRelation(): void
    {
        // Test de la relation avec le chapitre
        $chapter = new Chapter();
        $chapter->setTitre('Test Chapter');

        $this->quiz->setChapter($chapter);

        // Vérification
        $this->assertEquals($chapter, $this->quiz->getChapter());
        
        // Pour tester la relation bidirectionnelle
        $chapter->addQuiz($this->quiz);
        $this->assertTrue($chapter->getQuizzes()->contains($this->quiz));
    }

    public function testQuizResultsRelation(): void
    {
        // Test de la relation avec les résultats
        $result1 = new Result();
        $result1->setUtilisateur('user1');
        $result1->setScore(85);
        
        $result2 = new Result();
        $result2->setUtilisateur('user2');
        $result2->setScore(92);

        // Ajout de résultats
        $this->quiz->addResult($result1);
        $this->quiz->addResult($result2);

        // Vérification
        $this->assertCount(2, $this->quiz->getResults());
        $this->assertTrue($this->quiz->getResults()->contains($result1));
        $this->assertTrue($this->quiz->getResults()->contains($result2));
        $this->assertEquals($this->quiz, $result1->getQuiz());
        $this->assertEquals($this->quiz, $result2->getQuiz());

        // Suppression d'un résultat
        $this->quiz->removeResult($result1);
        $this->assertCount(1, $this->quiz->getResults());
        $this->assertFalse($this->quiz->getResults()->contains($result1));
        $this->assertNull($result1->getQuiz());
    }

    public function testQuizQuestionsRelation(): void
    {
        // Test de la relation avec les questions - simplifié sans accéder aux méthodes Question
        $this->assertCount(0, $this->quiz->getQuestions());
        
        // Test que la collection est bien initialisée
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->quiz->getQuestions());
    }

    public function testQuizMetadata(): void
    {
        // Test des métadonnées
        $metadata = [
            'tags' => ['php', 'symfony'],
            'difficulty' => 'intermediate',
            'estimated_time' => 30
        ];

        $this->quiz->setMetadata($metadata);
        $this->assertEquals($metadata, $this->quiz->getMetadata());

        // Test métadonnées null
        $this->quiz->setMetadata(null);
        $this->assertNull($this->quiz->getMetadata());
    }

    public function testQuizUpdatedAt(): void
    {
        // Test de la date de mise à jour
        $updatedAt = new \DateTime('2023-12-25 10:00:00');
        $this->quiz->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $this->quiz->getUpdatedAt());

        // Test date de mise à jour null
        $this->quiz->setUpdatedAt(null);
        $this->assertNull($this->quiz->getUpdatedAt());
    }

    public function testQuizDefaultValues(): void
    {
        // Test des valeurs par défaut
        $this->assertFalse($this->quiz->isVisible());
        $this->assertEquals('draft', $this->quiz->getStatus());
        $this->assertEquals(0, $this->quiz->getTimeLimit());
        $this->assertEquals(0, $this->quiz->getNumberOfQuestions());
        $this->assertEquals(0, $this->quiz->getAttempts());
        $this->assertNotNull($this->quiz->getDatecreation());
    }

    public function testQuizVisibilityMethods(): void
    {
        // Test des méthodes de visibilité
        $this->quiz->setVisible(true);
        $this->assertTrue($this->quiz->isVisible());
        $this->assertTrue($this->quiz->getVisible());

        $this->quiz->setVisible(false);
        $this->assertFalse($this->quiz->isVisible());
        $this->assertFalse($this->quiz->getVisible());
    }
}
