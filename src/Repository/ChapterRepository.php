<?php

namespace App\Repository;

use App\Entity\Chapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapter>
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }

    /**
     * Retourne les chapitres d'un cours ordonnés par position
     */
    public function findByCourseOrdered(int $courseId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la prochaine position pour un cours
     */
    public function getNextPosition(int $courseId): int
    {
        $maxPosition = $this->createQueryBuilder('c')
            ->select('MAX(c.position)')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getSingleScalarResult();

        return ($maxPosition ?? 0) + 1;
    }

    /**
     * Recherche de chapitres par titre
     */
    public function searchByTitle(string $term, int $courseId = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.title LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        if ($courseId) {
            $qb->andWhere('c.course = :courseId')
               ->setParameter('courseId', $courseId);
        }

        return $qb->orderBy('c.position', 'ASC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Déplace un chapitre vers le haut
     */
    public function moveUp(int $chapterId): bool
    {
        $chapter = $this->find($chapterId);
        if (!$chapter || $chapter->getPosition() <= 1) {
            return false;
        }

        $previousChapter = $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->andWhere('c.position = :position')
            ->setParameter('courseId', $chapter->getCourse()->getId())
            ->setParameter('position', $chapter->getPosition() - 1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($previousChapter) {
            $previousChapter->setPosition($chapter->getPosition());
            $chapter->setPosition($chapter->getPosition() - 1);
            
            $this->getEntityManager()->persist($previousChapter);
            $this->getEntityManager()->persist($chapter);
            $this->getEntityManager()->flush();
            
            return true;
        }

        return false;
    }

    /**
     * Déplace un chapitre vers le bas
     */
    public function moveDown(int $chapterId): bool
    {
        $chapter = $this->find($chapterId);
        if (!$chapter) {
            return false;
        }

        $maxPosition = $this->createQueryBuilder('c')
            ->select('MAX(c.position)')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $chapter->getCourse()->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if ($chapter->getPosition() >= $maxPosition) {
            return false;
        }

        $nextChapter = $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->andWhere('c.position = :position')
            ->setParameter('courseId', $chapter->getCourse()->getId())
            ->setParameter('position', $chapter->getPosition() + 1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($nextChapter) {
            $nextChapter->setPosition($chapter->getPosition());
            $chapter->setPosition($chapter->getPosition() + 1);
            
            $this->getEntityManager()->persist($nextChapter);
            $this->getEntityManager()->persist($chapter);
            $this->getEntityManager()->flush();
            
            return true;
        }

        return false;
    }

    /**
     * @deprecated Use getNextPosition() instead
     */
    public function getNextOrder(int $courseId): int
    {
        return $this->getNextPosition($courseId);
    }

    //    /**
    //     * @return Chapter[] Returns an array of Chapter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Chapter
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
