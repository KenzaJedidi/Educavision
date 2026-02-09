<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retourne les messages pour un cours spécifique (recherche par titre)
     */
    public function findByCourseTitle(string $courseTitle): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.title LIKE :title')
            ->setParameter('title', 'Cours : ' . $courseTitle)
            ->orderBy('m.last_message_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les messages non lus
     */
    public function findUnreadMessages(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.is_read = :isRead')
            ->setParameter('isRead', 0)
            ->orderBy('m.last_message_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée de messages
     */
    public function searchMessages(
        string $title = null,
        string $student = null,
        string $teacher = null,
        ?bool $isRead = null
    ): array {
        $qb = $this->createQueryBuilder('m');

        if ($title) {
            $qb->andWhere('m.title LIKE :title')
               ->setParameter('title', '%' . $title . '%');
        }

        if ($student) {
            $qb->andWhere('m.student LIKE :student')
               ->setParameter('student', '%' . $student . '%');
        }

        if ($teacher) {
            $qb->andWhere('m.teacher LIKE :teacher')
               ->setParameter('teacher', '%' . $teacher . '%');
        }

        if ($isRead !== null) {
            $qb->andWhere('m.is_read = :isRead')
               ->setParameter('isRead', $isRead ? 1 : 0);
        }

        return $qb->orderBy('m.last_message_at', 'DESC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Compte les messages non lus
     */
    public function countUnreadMessages(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.is_read = :isRead')
            ->setParameter('isRead', 0)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne les messages récents (derniers 7 jours)
     */
    public function findRecentMessages(): array
    {
        $sevenDaysAgo = new \DateTime('-7 days');

        return $this->createQueryBuilder('m')
            ->where('m.last_message_at >= :date')
            ->setParameter('date', $sevenDaysAgo)
            ->orderBy('m.last_message_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Marque tous les messages d'un cours comme lus
     */
    public function markCourseMessagesAsRead(string $courseTitle): int
    {
        return $this->createQueryBuilder('m')
            ->update()
            ->set('m.is_read', ':isRead')
            ->where('m.title LIKE :title')
            ->setParameter('isRead', 1)
            ->setParameter('title', 'Cours : ' . $courseTitle)
            ->getQuery()
            ->execute();
    }

    /**
     * Trouve ou crée une discussion pour un cours
     */
    public function findOrCreateDiscussion(string $courseTitle, string $studentName): Message
    {
        $message = $this->createQueryBuilder('m')
            ->where('m.title LIKE :title')
            ->andWhere('m.student = :student')
            ->setParameter('title', 'Cours : ' . $courseTitle)
            ->setParameter('student', $studentName)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$message) {
            $message = new Message();
            $message->setTitle('Cours : ' . $courseTitle);
            $message->setStudent($studentName);
            $message->setContent('Nouvelle discussion pour le cours : ' . $courseTitle);
            $this->getEntityManager()->persist($message);
            $this->getEntityManager()->flush();
        }

        return $message;
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
