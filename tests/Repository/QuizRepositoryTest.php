<?php

namespace App\Tests\Repository;

use App\Entity\Quiz;
use App\Entity\Chapter;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class QuizRepositoryTest extends KernelTestCase
{
    private QuizRepository $repository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Quiz::class);
    }

    public function testFindVisibleOrdered(): void
    {
        // Créer des quizzes de test
        $visibleQuiz = new Quiz();
        $visibleQuiz->setTitre('Visible Quiz');
        $visibleQuiz->setVisible(true);

        $hiddenQuiz = new Quiz();
        $hiddenQuiz->setTitre('Hidden Quiz');
        $hiddenQuiz->setVisible(false);

        $this->entityManager->persist($visibleQuiz);
        $this->entityManager->persist($hiddenQuiz);
        $this->entityManager->flush();

        // Test
        $visibleQuizzes = $this->repository->findVisibleOrdered();

        $this->assertIsArray($visibleQuizzes);
        $this->assertCount(1, $visibleQuizzes);
        $this->assertEquals('Visible Quiz', $visibleQuizzes[0]->getTitre());
        $this->assertTrue($visibleQuizzes[0]->isVisible());
    }

    public function testFindByChapter(): void
    {
        // Créer un chapitre et un quiz
        $chapter = new Chapter();
        $chapter->setTitre('Test Chapter');

        $quiz = new Quiz();
        $quiz->setTitre('Chapter Quiz');
        $quiz->setChapter($chapter);

        $this->entityManager->persist($chapter);
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        // Test
        $foundQuiz = $this->repository->findByChapter($chapter);

        $this->assertInstanceOf(Quiz::class, $foundQuiz);
        $this->assertEquals('Chapter Quiz', $foundQuiz->getTitre());
        $this->assertEquals($chapter, $foundQuiz->getChapter());
    }

    public function testFindByChapterReturnsNull(): void
    {
        // Test avec un chapitre qui n'a pas de quiz
        $chapter = new Chapter();
        $chapter->setTitre('Empty Chapter');

        $this->entityManager->persist($chapter);
        $this->entityManager->flush();

        // Test
        $foundQuiz = $this->repository->findByChapter($chapter);

        $this->assertNull($foundQuiz);
    }

    public function testFindAllByChapter(): void
    {
        // Créer un chapitre et plusieurs quizzes
        $chapter = new Chapter();
        $chapter->setTitre('Test Chapter');

        $quiz1 = new Quiz();
        $quiz1->setTitre('Quiz 1');
        $quiz1->setChapter($chapter);

        $quiz2 = new Quiz();
        $quiz2->setTitre('Quiz 2');
        $quiz2->setChapter($chapter);

        $quiz3 = new Quiz();
        $quiz3->setTitre('Quiz 3');
        $quiz3->setChapter($chapter);

        $this->entityManager->persist($chapter);
        $this->entityManager->persist($quiz1);
        $this->entityManager->persist($quiz2);
        $this->entityManager->persist($quiz3);
        $this->entityManager->flush();

        // Test
        $quizzes = $this->repository->findAllByChapter($chapter);

        $this->assertIsArray($quizzes);
        $this->assertCount(3, $quizzes);
        
        // Vérifier que tous les quizzes appartiennent au bon chapitre
        foreach ($quizzes as $quiz) {
            $this->assertEquals($chapter, $quiz->getChapter());
        }
    }

    public function testCountPublished(): void
    {
        // Créer des quizzes publiés et non publiés
        $publishedQuiz1 = new Quiz();
        $publishedQuiz1->setTitre('Published Quiz 1');
        $publishedQuiz1->setStatus('published');

        $publishedQuiz2 = new Quiz();
        $publishedQuiz2->setTitre('Published Quiz 2');
        $publishedQuiz2->setStatus('published');

        $draftQuiz = new Quiz();
        $draftQuiz->setTitre('Draft Quiz');
        $draftQuiz->setStatus('draft');

        $this->entityManager->persist($publishedQuiz1);
        $this->entityManager->persist($publishedQuiz2);
        $this->entityManager->persist($draftQuiz);
        $this->entityManager->flush();

        // Test
        $count = $this->repository->countPublished();

        $this->assertEquals(2, $count);
    }

    public function testFindByDifficulty(): void
    {
        // Créer des quizzes avec différents niveaux de difficulté
        $easyQuiz = new Quiz();
        $easyQuiz->setTitre('Easy Quiz');
        $easyQuiz->setDifficultyLevel('Facile');

        $mediumQuiz = new Quiz();
        $mediumQuiz->setTitre('Medium Quiz');
        $mediumQuiz->setDifficultyLevel('Moyen');

        $hardQuiz = new Quiz();
        $hardQuiz->setTitre('Hard Quiz');
        $hardQuiz->setDifficultyLevel('Difficile');

        $this->entityManager->persist($easyQuiz);
        $this->entityManager->persist($mediumQuiz);
        $this->entityManager->persist($hardQuiz);
        $this->entityManager->flush();

        // Test pour chaque difficulté
        $easyQuizzes = $this->repository->findByDifficulty('Facile');
        $mediumQuizzes = $this->repository->findByDifficulty('Moyen');
        $hardQuizzes = $this->repository->findByDifficulty('Difficile');

        $this->assertCount(1, $easyQuizzes);
        $this->assertCount(1, $mediumQuizzes);
        $this->assertCount(1, $hardQuizzes);

        $this->assertEquals('Easy Quiz', $easyQuizzes[0]->getTitre());
        $this->assertEquals('Medium Quiz', $mediumQuizzes[0]->getTitre());
        $this->assertEquals('Hard Quiz', $hardQuizzes[0]->getTitre());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Nettoyer la base de données
        $this->entityManager->createQuery('DELETE FROM App\Entity\Quiz q')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Chapter c')->execute();
    }
}
