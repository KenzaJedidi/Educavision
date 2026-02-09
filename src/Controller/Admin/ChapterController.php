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

#[Route('/admin/chapter')]
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

        $qb = $chapterRepository->createQueryBuilder('c')
            ->leftJoin('c.course', 'course')
            ->addSelect('course');

        if ($search) {
            $qb->andWhere('c.titre LIKE :search OR c.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($courseId) {
            $qb->andWhere('c.course = :courseId')
               ->setParameter('courseId', (int)$courseId);
        }

        $chapters = $qb->orderBy('c.ordre', 'ASC')
                      ->addOrderBy('c.created_at', 'DESC')
                      ->getQuery()
                      ->getResult();

        $courses = $courseRepository->findAll();

        return $this->render('admin/chapter/index.html.twig', [
            'chapters' => $chapters,
            'courses' => $courses,
            'search' => $search,
            'selectedCourse' => $courseId,
        ]);
    }

    #[Route('/new', name: 'admin_chapter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ChapterRepository $chapterRepository
    ): Response {
        $chapter = new Chapter();
        $chapter->setCreatedAt(new \DateTime());
        
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir l'ordre automatiquement si non spécifié
            if (!$chapter->getOrdre() && $chapter->getCourse()) {
                $nextOrder = $chapterRepository->getNextOrder($chapter->getCourse()->getId());
                $chapter->setOrdre($nextOrder);
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
    public function show(
        Chapter $chapter,
        MessageRepository $messageRepository
    ): Response {
        return $this->render('admin/chapter/show.html.twig', [
            'chapter' => $chapter,
            'messageRepository' => $messageRepository,
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

    #[Route('/{id}/delete', name: 'admin_chapter_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($chapter);
        $entityManager->flush();

        $this->addFlash('success', 'Le chapitre a été supprimé avec succès !');

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
