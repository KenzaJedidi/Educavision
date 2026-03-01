<?php
namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Trouve les questions d'un quiz
     */
    public function findByQuiz(Quiz $quiz): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->orderBy('q.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les questions d'un quiz
     */
    public function countByQuiz(Quiz $quiz): int
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->andWhere('q.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les questions par difficulté
     */
    public function findByDifficulty(Quiz $quiz, string $difficulty): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.quiz = :quiz')
            ->andWhere('q.difficulty = :difficulty')
            ->setParameter('quiz', $quiz)
            ->setParameter('difficulty', $difficulty)
            ->orderBy('q.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
