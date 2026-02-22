<?php

namespace App\Controller\Front;

use App\Entity\Course;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChapterRepository;
use App\Repository\ConversationMessageRepository;
use App\Repository\CourseRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cours')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'front_cours_list')]
    public function list(
        Request $request,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        if ($search || $category) {
            $courses = $courseRepository->searchCourses($search, $category, 'published');
        } else {
            $courses = $courseRepository->findPublishedCourses();
        }

        $categories = $courseRepository->getAvailableCategories();

        return $this->render('front/pages/cours_list.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
        ]);
    }

    #[Route('/{id}', name: 'front_cours_show', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        CourseRepository $courseRepository,
        ChapterRepository $chapterRepository
    ): Response {
        $course = $courseRepository->find($id);
        
        if (!$course || $course->getStatus() !== 'published') {
            throw $this->createNotFoundException('Cours introuvable');
        }

        $chapters = $chapterRepository->findByCourseOrdered($id);

        return $this->render('front/pages/cours_show.html.twig', [
            'course' => $course,
            'chapters' => $chapters,
        ]);
    }

    #[Route('/{id}/chapitre/{chapterId}', name: 'front_chapitre_show', requirements: ['id' => '\d+', 'chapterId' => '\d+'])]
    public function showChapter(
        int $id,
        int $chapterId,
        CourseRepository $courseRepository,
        ChapterRepository $chapterRepository
    ): Response {
        $course = $courseRepository->find($id);
        $chapter = $chapterRepository->find($chapterId);

        if (!$course || !in_array($course->getStatus(), ['published', 'publishe'])) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        if (!$chapter || $chapter->getCourse()?->getId() !== $id) {
            throw $this->createNotFoundException('Chapitre introuvable');
        }

        $chapters = $chapterRepository->findByCourseOrdered($id);

        return $this->render('front/pages/chapitre_show.html.twig', [
            'course' => $course,
            'chapter' => $chapter,
            'chapters' => $chapters,
        ]);
    }

    #[Route('/{id}/message', name: 'front_cours_message', requirements: ['id' => '\d+'])]
    public function message(
        int $id,
        Request $request,
        CourseRepository $courseRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $course = $courseRepository->find($id);
        
        if (!$course || !in_array($course->getStatus(), ['published', 'publishe'])) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        if ($request->isMethod('POST')) {
            $studentName = $request->request->get('student_name');
            $studentEmail = $request->request->get('student_email');
            $content = $request->request->get('content');

            if (!$studentName || !$content) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('front_cours_message', ['id' => $id]);
            }

            // Créer ou trouver la discussion
            $message = $messageRepository->findOrCreateDiscussion($course->getTitle(), $studentName);
            
            // Mettre à jour le message
            $message->setContent($content);
            $message->setStudent($studentName);
            $message->setTeacher($course->getTitle() . ' - Professeur');
            $message->updateLastMessage($content);
            $message->markAsUnread();

            $entityManager->flush();

            $this->addFlash('success', 'Votre message a été envoyé au professeur.');
            return $this->redirectToRoute('front_cours_show', ['id' => $id]);
        }

        return $this->render('front/pages/cours_message.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/student-reply/{messageId}', name: 'front_cours_student_reply', methods: ['POST'])]
    public function studentReply(
        Request $request,
        int $id,
        int $messageId,
        CourseRepository $courseRepository,
        MessageRepository $messageRepository,
        ConversationMessageRepository $conversationMessageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $course = $courseRepository->find($id);
        
        if (!$course || !in_array($course->getStatus(), ['published', 'publishe'])) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        $message = $messageRepository->find($messageId);
        
        if (!$message) {
            throw $this->createNotFoundException('Message introuvable');
        }

        $replyContent = $request->request->get('reply_content');
        
        if (!$replyContent) {
            $this->addFlash('error', 'Veuillez saisir une réponse.');
            return $this->redirectToRoute('front_cours_discussion', ['id' => $id]);
        }

        // Ajouter la réponse à la conversation
        $conversationMessageRepository->addReply($message, $message->getStudent(), 'student', $replyContent);
        
        // Mettre à jour les informations du message principal
        $message->setLastMessage($replyContent);
        $message->setLastMessageAt(new \DateTime());
        $message->setMessageCount($message->getMessageCount() + 1);
        $message->markAsUnread(); // Marquer comme non lu pour que le prof voie la nouvelle réponse

        $entityManager->flush();

        $this->addFlash('success', 'Votre réponse a été envoyée au professeur !');
        return $this->redirectToRoute('front_cours_discussion', ['id' => $id]);
    }

    #[Route('/{id}/discussion', name: 'front_cours_discussion', methods: ['GET'])]
    public function discussion(
        int $id,
        Request $request,
        CourseRepository $courseRepository,
        MessageRepository $messageRepository,
        ConversationMessageRepository $conversationMessageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $course = $courseRepository->find($id);
        
        if (!$course || !in_array($course->getStatus(), ['published', 'publishe'])) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        $studentName = $request->query->get('student');
        $messages = [];

        // Récupérer tous les messages du cours
        $messages = $messageRepository->findByCourseTitle($course->getTitle());
        
        // Initialiser les conversations pour chaque message
        foreach ($messages as $message) {
            $conversationMessageRepository->initializeConversation($message);
        }
        
        // Filtrer par étudiant si spécifié
        if ($studentName) {
            $messages = array_filter($messages, function($message) use ($studentName) {
                return $message->getStudent() === $studentName;
            });

            // Marquer les messages comme lus
            foreach ($messages as $message) {
                if (!$message->isRead()) {
                    $message->markAsRead();
                }
            }
            $entityManager->flush();
        }

        return $this->render('front/pages/cours_discussion.html.twig', [
            'course' => $course,
            'messages' => $messages,
            'studentName' => $studentName,
        ]);
    }
}
