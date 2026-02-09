<?php
namespace App\Repository;

use App\Entity\Quiz;
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
            ->andWhere('q.visible = :vis')
            ->setParameter('vis', true)
            ->orderBy('q.datecreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
