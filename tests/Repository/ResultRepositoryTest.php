<?php

namespace App\Tests\Repository;

use App\Entity\Result;
use App\Entity\Quiz;
use App\Repository\ResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultRepositoryTest extends KernelTestCase
{
    private ResultRepository $repository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Result::class);
    }

    public function testFindByQuiz(): void
    {
        // Créer un quiz et des résultats
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $result1 = new Result();
        $result1->setQuiz($quiz);
        $result1->setUtilisateur('user1');
        $result1->setScore(85);

        $result2 = new Result();
        $result2->setQuiz($quiz);
        $result2->setUtilisateur('user2');
        $result2->setScore(92);

        $this->entityManager->persist($quiz);
        $this->entityManager->persist($result1);
        $this->entityManager->persist($result2);
        $this->entityManager->flush();

        // Test
        $results = $this->repository->findByQuiz($quiz);

        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        
        foreach ($results as $result) {
            $this->assertEquals($quiz, $result->getQuiz());
        }
    }

    public function testFindByUtilisateur(): void
    {
        // Créer des quizzes et des résultats pour le même utilisateur
        $quiz1 = new Quiz();
        $quiz1->setTitre('Quiz 1');

        $quiz2 = new Quiz();
        $quiz2->setTitre('Quiz 2');

        $result1 = new Result();
        $result1->setQuiz($quiz1);
        $result1->setUtilisateur('test_user');
        $result1->setScore(75);

        $result2 = new Result();
        $result2->setQuiz($quiz2);
        $result2->setUtilisateur('test_user');
        $result2->setScore(88);

        $otherResult = new Result();
        $otherResult->setQuiz($quiz1);
        $otherResult->setUtilisateur('other_user');
        $otherResult->setScore(65);

        $this->entityManager->persist($quiz1);
        $this->entityManager->persist($quiz2);
        $this->entityManager->persist($result1);
        $this->entityManager->persist($result2);
        $this->entityManager->persist($otherResult);
        $this->entityManager->flush();

        // Test
        $results = $this->repository->findByUtilisateur('test_user');

        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        
        foreach ($results as $result) {
            $this->assertEquals('test_user', $result->getUtilisateur());
        }
    }

    public function testFindByQuizAndUtilisateur(): void
    {
        // Créer un quiz et des résultats pour différents utilisateurs
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $targetResult = new Result();
        $targetResult->setQuiz($quiz);
        $targetResult->setUtilisateur('target_user');
        $targetResult->setScore(95);

        $otherResult = new Result();
        $otherResult->setQuiz($quiz);
        $otherResult->setUtilisateur('other_user');
        $otherResult->setScore(80);

        $this->entityManager->persist($quiz);
        $this->entityManager->persist($targetResult);
        $this->entityManager->persist($otherResult);
        $this->entityManager->flush();

        // Test
        $results = $this->repository->findByQuizAndUtilisateur($quiz, 'target_user');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('target_user', $results[0]->getUtilisateur());
        $this->assertEquals($quiz, $results[0]->getQuiz());
    }

    public function testFindBestScoreByQuiz(): void
    {
        // Créer un quiz et des résultats avec différents scores
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $result1 = new Result();
        $result1->setQuiz($quiz);
        $result1->setUtilisateur('user1');
        $result1->setScore(75);

        $result2 = new Result();
        $result2->setQuiz($quiz);
        $result2->setUtilisateur('user2');
        $result2->setScore(92);

        $result3 = new Result();
        $result3->setQuiz($quiz);
        $result3->setUtilisateur('user3');
        $result3->setScore(88);

        $this->entityManager->persist($quiz);
        $this->entityManager->persist($result1);
        $this->entityManager->persist($result2);
        $this->entityManager->persist($result3);
        $this->entityManager->flush();

        // Test
        $bestScore = $this->repository->findBestScoreByQuiz($quiz);

        $this->assertEquals(92, $bestScore);
    }

    public function testFindAverageScoreByQuiz(): void
    {
        // Créer un quiz et des résultats
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $result1 = new Result();
        $result1->setQuiz($quiz);
        $result1->setUtilisateur('user1');
        $result1->setScore(80);

        $result2 = new Result();
        $result2->setQuiz($quiz);
        $result2->setUtilisateur('user2');
        $result2->setScore(90);

        $result3 = new Result();
        $result3->setQuiz($quiz);
        $result3->setUtilisateur('user3');
        $result3->setScore(85);

        $this->entityManager->persist($quiz);
        $this->entityManager->persist($result1);
        $this->entityManager->persist($result2);
        $this->entityManager->persist($result3);
        $this->entityManager->flush();

        // Test
        $averageScore = $this->repository->findAverageScoreByQuiz($quiz);

        $this->assertEquals(85.0, $averageScore);
    }

    public function testCountAttemptsByQuiz(): void
    {
        // Créer un quiz et plusieurs résultats
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        for ($i = 1; $i <= 5; $i++) {
            $result = new Result();
            $result->setQuiz($quiz);
            $result->setUtilisateur("user{$i}");
            $result->setScore(70 + $i);
            $this->entityManager->persist($result);
        }

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        // Test
        $attempts = $this->repository->countAttemptsByQuiz($quiz);

        $this->assertEquals(5, $attempts);
    }

    public function testCountAttemptsByUtilisateur(): void
    {
        // Créer plusieurs quizzes et des résultats pour le même utilisateur
        for ($i = 1; $i <= 3; $i++) {
            $quiz = new Quiz();
            $quiz->setTitre("Quiz {$i}");

            $result = new Result();
            $result->setQuiz($quiz);
            $result->setUtilisateur('test_user');
            $result->setScore(70 + $i);

            $this->entityManager->persist($quiz);
            $this->entityManager->persist($result);
        }

        $this->entityManager->flush();

        // Test
        $attempts = $this->repository->countAttemptsByUtilisateur('test_user');

        $this->assertEquals(3, $attempts);
    }

    public function testFindByMinimumScore(): void
    {
        // Créer des résultats avec différents scores
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');

        $scores = [65, 75, 85, 95];
        foreach ($scores as $score) {
            $result = new Result();
            $result->setQuiz($quiz);
            $result->setUtilisateur("user_{$score}");
            $result->setScore($score);
            $this->entityManager->persist($result);
        }

        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        // Test avec un score minimum de 80
        $results = $this->repository->findByMinimumScore(80);

        $this->assertIsArray($results);
        $this->assertCount(2, $results); // 85 et 95
        
        foreach ($results as $result) {
            $this->assertGreaterThanOrEqual(80, $result->getScore());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Nettoyer la base de données
        $this->entityManager->createQuery('DELETE FROM App\Entity\Result r')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Quiz q')->execute();
    }
}
