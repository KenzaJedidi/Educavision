<?php

namespace App\Repository;

use App\Entity\Course;
use App\DTO\CourseListItemDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Retourne les cours actifs (status = 1)
     */
    public function findActiveCourses(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée multicritère avec pagination
     * 
     * @param string|null $title Titre du cours
     * @param string|null $category Catégorie
     * @param string|null $keywords Mots-clés (recherche dans keywords)
     * @param string|null $sortBy Critère de tri (date, popularity, views)
     * @param int $page Page actuelle (1-based)
     * @param int $limit Nombre d'éléments par page
     * 
     * @return array{items: Course[], total: int, page: int, limit: int, pages: int}
     */
    public function searchCoursesAdvanced(
        ?string $title = null,
        ?string $category = null,
        ?string $keywords = null,
        ?string $sortBy = 'date',
        int $page = 1,
        int $limit = 12
    ): array {
        $qb = $this->createAdvancedQueryBuilder($title, $category, $keywords);

        // Tri
        $qb = $this->applySort($qb, $sortBy);

        // Compte le total avant pagination
        $countQb = clone $qb;
        $total = $countQb->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Pagination
        $offset = ($page - 1) * $limit;
        $courses = $qb->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Convertir les entités en DTOs pour éviter les références circulaires
        $items = array_map(function(Course $course) {
            return new CourseListItemDTO(
                $course->getId(),
                $course->getTitre(),
                $course->getDescription(),
                $course->getPrice(),
                $course->getCategory(),
                $course->getImageUrl(),
                $course->getCreatedAt(),
                $course->getWikipediaSummary(),
                $course->getKeywords(),
                $course->getViews(),
                $course->getLikes()
            );
        }, $courses);

        $totalPages = ceil($total / $limit);

        return [
            'items' => $items,
            'total' => (int) $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => (int) $totalPages,
        ];
    }

    /**
     * Recherche avancée de cours (compatible avec l'ancienne API)
     */
    public function searchCourses(string $term = null, string $category = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1);

        if ($term) {
            $qb->andWhere('c.titre LIKE :term OR c.description LIKE :term OR c.keywords LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        if ($category) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        return $qb->orderBy('c.created_at', 'DESC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Retourne les catégories disponibles
     */
    public function getAvailableCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.category')
            ->where('c.category IS NOT NULL')
            ->andWhere('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Compte le nombre de cours actifs
     */
    public function countActiveCourses(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les cours les plus populaires
     */
    public function findMostPopular(int $limit = 6): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->orderBy('c.popularity_score', 'DESC')
            ->addOrderBy('c.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les cours les plus récemment créés
     */
    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->orderBy('c.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les cours avec le résumé IA généré
     */
    public function findCoursesWithAISummary(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1)
            ->andWhere('c.wikipedia_summary IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Crée un QueryBuilder avec les filtres avancés
     */
    private function createAdvancedQueryBuilder(
        ?string $title = null,
        ?string $category = null,
        ?string $keywords = null
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 1);

        // Filtre par titre
        if (!empty($title)) {
            $qb->andWhere('c.titre LIKE :title')
               ->setParameter('title', '%' . $title . '%');
        }

        // Filtre par catégorie
        if (!empty($category)) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        // Filtre par mots-clés
        if (!empty($keywords)) {
            $keywordArray = array_map('trim', explode(',', $keywords));
            $keywordConditions = [];
            
            foreach ($keywordArray as $idx => $keyword) {
                $paramName = 'keyword' . $idx;
                $keywordConditions[] = "c.keywords LIKE :" . $paramName;
                $qb->setParameter($paramName, '%' . $keyword . '%');
            }
            
            if (!empty($keywordConditions)) {
                $qb->andWhere('(' . implode(' OR ', $keywordConditions) . ')');
            }
        }

        return $qb;
    }

    /**
     * Applique le tri selon le critère demandé
     */
    private function applySort(QueryBuilder $qb, string $sortBy): QueryBuilder
    {
        return match ($sortBy) {
            'popularity' => $qb->orderBy('c.popularity_score', 'DESC')
                                ->addOrderBy('c.views', 'DESC'),
            'views' => $qb->orderBy('c.views', 'DESC')
                           ->addOrderBy('c.created_at', 'DESC'),
            'price_asc' => $qb->orderBy('c.price', 'ASC')
                               ->addOrderBy('c.created_at', 'DESC'),
            'price_desc' => $qb->orderBy('c.price', 'DESC')
                                ->addOrderBy('c.created_at', 'DESC'),
            'date' => $qb->orderBy('c.created_at', 'DESC'),
            default => $qb->orderBy('c.created_at', 'DESC'),
        };
    }

    //    /**
    //     * @return Course[] Returns an array of Course objects
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

    //    public function findOneBySomeField($value): ?Course
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
