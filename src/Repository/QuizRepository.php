<?php
namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * @return Quiz[] Returns visible quizzes ordered by creation date desc
     */
    public function findVisibleOrdered(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.visible = :vis')
            ->setParameter('vis', true)
            ->orderBy('q.datecreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les quizzes visibles avec leurs chapitres (évite N+1)
     */
    public function findVisibleOrderedWithChapter(): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.chapter', 'c')
            ->addSelect('c')
            ->where('q.visible = :vis')
            ->setParameter('vis', true)
            ->orderBy('q.datecreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne un quiz avec ses résultats et questions (évite N+1)
     */
    public function findQuizWithRelations(int $quizId): ?Quiz
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.results', 'r')
            ->addSelect('r')
            ->leftJoin('q.questions', 'qu')
            ->addSelect('qu')
            ->where('q.idquiz = :id')
            ->setParameter('id', $quizId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve le quiz d'un chapitre
     */
    public function findByChapter(?Chapter $chapter): ?Quiz
    {
        if (!$chapter) {
            return null;
        }

        return $this->createQueryBuilder('q')
            ->andWhere('q.chapter = :chapter')
            ->setParameter('chapter', $chapter)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve tous les quiz d'un chapitre
     */
    public function findAllByChapter(Chapter $chapter): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.chapter = :chapter')
            ->setParameter('chapter', $chapter)
            ->orderBy('q.datecreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les quiz publiés
     */
    public function countPublished(): int
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.idquiz)')
            ->andWhere('q.status = :status')
            ->setParameter('status', 'published')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les quiz par niveau de difficulté
     */
    public function findByDifficulty(string $difficulty): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.difficultyLevel = :difficulty')
            ->setParameter('difficulty', $difficulty)
            ->orderBy('q.datecreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
