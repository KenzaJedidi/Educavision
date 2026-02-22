<?php

namespace App\Controller\Admin;

use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use App\Repository\CourseRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/chapter')]
#[IsGranted('ROLE_ADMIN')]
class ChapterController extends AbstractController
{
    #[Route('/', name: 'admin_chapter_index', methods: ['GET'])]
    public function index(
        Request $request,
        ChapterRepository $chapterRepository,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $courseId = $request->query->get('course');

        $chapters = $chapterRepository->searchByTitle($search ?? '', $courseId ? (int)$courseId : null);
        $courses = $courseRepository->findAll();

        return $this->render('admin/chapter/index.html.twig', [
            'chapters' => $chapters,
            'courses' => $courses,
            'search' => $search,
            'selectedCourse' => $courseId,
        ]);
    }

    #[Route('/new', name: 'admin_chapter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ChapterRepository $chapterRepository): Response
    {
        $chapter = new Chapter();
        
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Auto-generate position if not provided
            if (!$chapter->getPosition()) {
                $course = $chapter->getCourse();
                if ($course) {
                    $nextPosition = $chapterRepository->getNextPosition($course->getId());
                    $chapter->setPosition($nextPosition);
                }
            }

            $entityManager->persist($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été créé avec succès !');

            return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_chapter_show', methods: ['GET'])]
    public function show(Chapter $chapter, MessageRepository $messageRepository): Response
    {
        // Récupérer les messages du cours associé au chapitre
        $courseMessages = [];
        if ($chapter->getCourse()) {
            $courseMessages = $messageRepository->findBy(['course' => $chapter->getCourse()], ['created_at' => 'DESC']);
        }

        return $this->render('admin/chapter/show.html.twig', [
            'chapter' => $chapter,
            'courseMessages' => $courseMessages,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_chapter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été modifié avec succès !');

            return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_chapter_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        // Pour les requêtes GET (compatibilité), sauter la validation CSRF
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete'.$chapter->getId(), $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $entityManager->remove($chapter);
        $entityManager->flush();

        $this->addFlash('success', 'Le chapitre a été supprimé avec succès !');

        return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/move-up', name: 'admin_chapter_move_up', methods: ['POST'])]
    public function moveUp(Request $request, Chapter $chapter, ChapterRepository $chapterRepository): Response
    {
        if ($this->isCsrfTokenValid('move_up'.$chapter->getId(), $request->request->get('_token'))) {
            if ($chapterRepository->moveUp($chapter->getId())) {
                $this->addFlash('success', 'Le chapitre a été déplacé vers le haut avec succès !');
            } else {
                $this->addFlash('error', 'Impossible de déplacer ce chapitre vers le haut.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/move-down', name: 'admin_chapter_move_down', methods: ['POST'])]
    public function moveDown(Request $request, Chapter $chapter, ChapterRepository $chapterRepository): Response
    {
        if ($this->isCsrfTokenValid('move_down'.$chapter->getId(), $request->request->get('_token'))) {
            if ($chapterRepository->moveDown($chapter->getId())) {
                $this->addFlash('success', 'Le chapitre a été déplacé vers le bas avec succès !');
            } else {
                $this->addFlash('error', 'Impossible de déplacer ce chapitre vers le bas.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_chapter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/course/{courseId}', name: 'admin_chapter_by_course', methods: ['GET'])]
    public function byCourse(
        int $courseId,
        ChapterRepository $chapterRepository,
        CourseRepository $courseRepository
    ): Response {
        $course = $courseRepository->find($courseId);
        
        if (!$course) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        $chapters = $chapterRepository->findByCourseOrdered($courseId);

        return $this->render('admin/chapter/by_course.html.twig', [
            'course' => $course,
            'chapters' => $chapters,
        ]);
    }
}
