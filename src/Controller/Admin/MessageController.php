<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'admin_message_index', methods: ['GET'])]
    public function index(
        Request $request,
        MessageRepository $messageRepository,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $student = $request->query->get('student');
        $teacher = $request->query->get('teacher');
        $isRead = $request->query->get('is_read');

        $messages = $messageRepository->searchMessages($search, $student, $teacher, $isRead !== null ? (bool)$isRead : null);
        $courses = $courseRepository->findAll();

        return $this->render('admin/message/index.html.twig', [
            'messages' => $messages,
            'courses' => $courses,
            'search' => $search,
            'student' => $student,
            'teacher' => $teacher,
            'isRead' => $isRead,
        ]);
    }

    #[Route('/new', name: 'admin_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $message->setCreatedAt(new \DateTime());
        
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Le message a été créé avec succès !');

            return $this->redirectToRoute('admin_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/unread', name: 'admin_message_unread', methods: ['GET'])]
    public function unreadMessages(MessageRepository $messageRepository): Response
    {
        $messages = $messageRepository->findUnreadMessages();

        return $this->render('admin/message/unread.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/mark-all-read', name: 'admin_message_mark_all_read', methods: ['GET'])]
    public function markAllAsRead(MessageRepository $messageRepository, EntityManagerInterface $entityManager): Response
    {
        $messages = $messageRepository->findUnreadMessages();
        
        foreach ($messages as $message) {
            $message->markAsRead();
        }
        
        $entityManager->flush();

        $this->addFlash('success', 'Tous les messages ont été marqués comme lus !');

        return $this->redirectToRoute('admin_message_index');
    }

    #[Route('/course/{courseTitle}', name: 'admin_message_by_course', methods: ['GET'])]
    public function byCourse(
        string $courseTitle,
        MessageRepository $messageRepository
    ): Response {
        $messages = $messageRepository->findByCourseTitle($courseTitle);

        return $this->render('admin/message/by_course.html.twig', [
            'courseTitle' => $courseTitle,
            'messages' => $messages,
        ]);
    }

    #[Route('/{id}', name: 'admin_message_show', methods: ['GET'])]
    public function show(
        string $id,
        MessageRepository $messageRepository
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        return $this->render('admin/message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_message_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        string $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Message mis à jour avec succès !');
            return $this->redirectToRoute('admin_message_show', ['id' => $message->getId()]);
        }

        return $this->render('admin/message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_message_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request, 
        string $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
            $this->addFlash('success', 'Message supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_message_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/mark-read', name: 'admin_message_mark_read', methods: ['GET'])]
    public function markAsRead(
        string $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        $message->markAsRead();
        $entityManager->flush();

        $this->addFlash('success', 'Message marqué comme lu !');
        return $this->redirectToRoute('admin_message_show', ['id' => $message->getId()]);
    }

    #[Route('/{id}/mark-unread', name: 'admin_message_mark_unread', methods: ['GET'])]
    public function markAsUnread(
        string $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        $message->markAsUnread();
        $entityManager->flush();

        $this->addFlash('success', 'Le message a été marqué comme non lu !');

        return $this->redirectToRoute('admin_message_show', ['id' => $message->getId()]);
    }

    #[Route('/{id}/reply', name: 'admin_message_reply', methods: ['GET', 'POST'])]
    public function reply(
        Request $request,
        string $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $message = $messageRepository->find((int)$id);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        if ($request->isMethod('POST')) {
            $replyContent = $request->request->get('reply_content');
            
            if (!$replyContent) {
                $this->addFlash('error', 'Veuillez saisir une réponse.');
                return $this->redirectToRoute('admin_message_reply', ['id' => $id]);
            }

            // Mettre à jour le message avec la réponse
            $message->setLastMessage($replyContent);
            $message->setLastMessageAt(new \DateTime());
            $message->setMessageCount($message->getMessageCount() + 1);
            $message->markAsRead();

            $entityManager->flush();

            $this->addFlash('success', 'Réponse envoyée avec succès !');
            return $this->redirectToRoute('admin_message_show', ['id' => $id]);
        }

        return $this->render('admin/message/reply.html.twig', [
            'message' => $message,
        ]);
    }
}
