<?php

namespace App\Repository;

use App\Entity\Result;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Result>
 */
class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    /**
     * Retourne tous les résultats d'un quiz
     */
    public function findByQuiz(Quiz $quiz): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->orderBy('r.datepassage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les résultats d'un utilisateur
     */
    public function findByUtilisateur(string $utilisateur): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('r.datepassage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les résultats d'un utilisateur pour un quiz spécifique
     */
    public function findByQuizAndUtilisateur(Quiz $quiz, string $utilisateur): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.quiz = :quiz')
            ->andWhere('r.utilisateur = :utilisateur')
            ->setParameter('quiz', $quiz)
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('r.datepassage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne le meilleur score pour un quiz
     */
    public function findBestScoreByQuiz(Quiz $quiz): ?int
    {
        $result = $this->createQueryBuilder('r')
            ->select('MAX(r.score)')
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (int) $result : null;
    }

    /**
     * Retourne la moyenne des scores pour un quiz
     */
    public function findAverageScoreByQuiz(Quiz $quiz): ?float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.score)')
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? (float) $result : null;
    }

    /**
     * Compte le nombre de tentatives pour un quiz
     */
    public function countAttemptsByQuiz(Quiz $quiz): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.idresult)')
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de tentatives pour un utilisateur
     */
    public function countAttemptsByUtilisateur(string $utilisateur): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.idresult)')
            ->where('r.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les résultats récents (derniers 7 jours)
     */
    public function findRecentResults(int $days = 7): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('r')
            ->where('r.datepassage >= :date')
            ->setParameter('date', $date)
            ->orderBy('r.datepassage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les résultats avec un score minimum
     */
    public function findByMinimumScore(int $minScore): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.score >= :score')
            ->setParameter('score', $minScore)
            ->orderBy('r.score', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime tous les résultats d'un quiz
     */
    public function deleteByQuiz(Quiz $quiz): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.quiz = :quiz')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->execute();
    }

    /**
     * Supprime tous les résultats d'un utilisateur
     */
    public function deleteByUtilisateur(string $utilisateur): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->execute();
    }
}
