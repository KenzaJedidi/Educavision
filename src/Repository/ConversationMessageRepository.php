<?php

namespace App\Repository;

use App\Entity\ConversationMessage;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConversationMessage>
 */
class ConversationMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationMessage::class);
    }

    /**
     * Retourne tous les messages d'une conversation
     */
    public function findByMessage(Message $message): array
    {
        return $this->createQueryBuilder('cm')
            ->where('cm.message = :message')
            ->setParameter('message', $message)
            ->orderBy('cm.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Ajoute un message à la conversation
     */
    public function addMessage(Message $message, string $senderName, string $senderType, string $content): ConversationMessage
    {
        $conversationMessage = new ConversationMessage();
        $conversationMessage->setMessage($message);
        $conversationMessage->setSenderName($senderName);
        $conversationMessage->setSenderType($senderType);
        $conversationMessage->setContent($content);
        $conversationMessage->setCreatedAt(new \DateTime());

        $this->getEntityManager()->persist($conversationMessage);
        $this->getEntityManager()->flush();

        return $conversationMessage;
    }

    /**
     * Initialise une conversation avec le message initial
     */
    public function initializeConversation(Message $message): void
    {
        // Vérifier si la conversation existe déjà
        $existingMessages = $this->findByMessage($message);
        
        if (empty($existingMessages)) {
            // Ajouter le message initial
            $this->addMessage($message, $message->getStudent(), 'student', $message->getContent());
            
            // Ajouter la réponse du professeur si elle existe
            if ($message->getLastMessage() && $message->getLastMessage() !== $message->getContent()) {
                $teacherName = $message->getTeacher() ?: 'Professeur';
                $this->addMessage($message, $teacherName, 'teacher', $message->getLastMessage());
            }
        }
    }

    /**
     * Ajoute une réponse à la conversation
     */
    public function addReply(Message $message, string $senderName, string $senderType, string $content): void
    {
        $this->addMessage($message, $senderName, $senderType, $content);
    }

    //    /**
    //     * @return ConversationMessage[] Returns an array of ConversationMessage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('cm')
    //            ->andWhere('cm.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('cm.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ConversationMessage
    //    {
    //        return $this->createQueryBuilder('cm')
    //            ->andWhere('cm.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
