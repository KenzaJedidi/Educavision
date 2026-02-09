<?php

namespace App\Controller\Teacher;

use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher/chapter')]
class ChapterController extends AbstractController
{
    #[Route('/', name: 'teacher_chapter_index', methods: ['GET'])]
    public function index(
        Request $request,
        ChapterRepository $chapterRepository,
        CourseRepository $courseRepository
    ): Response {
        $teacher = $this->getUser();
        $search = $request->query->get('search');
        $courseId = $request->query->get('course');

        $qb = $chapterRepository->createQueryBuilder('ch')
            ->leftJoin('ch.course', 'c')
            ->addSelect('c')
            ->where('c.teacher = :teacher')
            ->setParameter('teacher', $teacher);

        if ($search) {
            $qb->andWhere('ch.titre LIKE :search OR ch.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($courseId) {
            $qb->andWhere('ch.course = :courseId')
               ->setParameter('courseId', (int)$courseId);
        }

        $chapters = $qb->orderBy('ch.ordre', 'ASC')
                      ->addOrderBy('ch.created_at', 'DESC')
                      ->getQuery()
                      ->getResult();

        // Only teacher's courses for the filter dropdown
        $courses = $courseRepository->findBy(
            ['teacher' => $teacher],
            ['titre' => 'ASC']
        );

        return $this->render('teacher/chapter/index.html.twig', [
            'chapters' => $chapters,
            'courses' => $courses,
            'search' => $search,
            'selectedCourse' => $courseId,
        ]);
    }

    #[Route('/new', name: 'teacher_chapter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ChapterRepository $chapterRepository,
        CourseRepository $courseRepository
    ): Response {
        $teacher = $this->getUser();
        $chapter = new Chapter();
        $chapter->setCreatedAt(new \DateTime());
        $chapter->setTeacherName($teacher->getFullName());
        $chapter->setTeacherEmail($teacher->getEmail());

        // Get only teacher's courses for the form
        $teacherCourses = $courseRepository->findBy(['teacher' => $teacher]);

        $form = $this->createForm(ChapterType::class, $chapter, [
            'teacher_courses' => $teacherCourses,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verify the selected course belongs to this teacher
            if ($chapter->getCourse() && $chapter->getCourse()->getTeacher() !== $teacher) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas ajouter un chapitre à ce cours.');
            }

            // Auto-set order if not specified
            if (!$chapter->getOrdre() && $chapter->getCourse()) {
                $nextOrder = $chapterRepository->getNextOrder($chapter->getCourse()->getId());
                $chapter->setOrdre($nextOrder);
            }

            $entityManager->persist($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été créé avec succès !');

            return $this->redirectToRoute('teacher_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teacher/chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'teacher_chapter_show', methods: ['GET'])]
    public function show(Chapter $chapter): Response
    {
        // Ensure chapter belongs to teacher's course
        if (!$chapter->getCourse() || $chapter->getCourse()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce chapitre.');
        }

        return $this->render('teacher/chapter/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }

    #[Route('/{id}/edit', name: 'teacher_chapter_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Chapter $chapter,
        EntityManagerInterface $entityManager,
        CourseRepository $courseRepository
    ): Response {
        $teacher = $this->getUser();

        // Ensure chapter belongs to teacher's course
        if (!$chapter->getCourse() || $chapter->getCourse()->getTeacher() !== $teacher) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce chapitre.');
        }

        $teacherCourses = $courseRepository->findBy(['teacher' => $teacher]);

        $form = $this->createForm(ChapterType::class, $chapter, [
            'teacher_courses' => $teacherCourses,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verify the selected course belongs to this teacher
            if ($chapter->getCourse() && $chapter->getCourse()->getTeacher() !== $teacher) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas déplacer ce chapitre vers ce cours.');
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été modifié avec succès !');

            return $this->redirectToRoute('teacher_chapter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teacher/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'teacher_chapter_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        // Ensure chapter belongs to teacher's course
        if (!$chapter->getCourse() || $chapter->getCourse()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce chapitre.');
        }

        $entityManager->remove($chapter);
        $entityManager->flush();

        $this->addFlash('success', 'Le chapitre a été supprimé avec succès !');

        return $this->redirectToRoute('teacher_chapter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/course/{courseId}', name: 'teacher_chapter_by_course', methods: ['GET'])]
    public function byCourse(
        int $courseId,
        ChapterRepository $chapterRepository,
        CourseRepository $courseRepository
    ): Response {
        $teacher = $this->getUser();
        $course = $courseRepository->find($courseId);

        if (!$course) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        // Ensure course belongs to teacher
        if ($course->getTeacher() !== $teacher) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder aux chapitres de ce cours.');
        }

        $chapters = $chapterRepository->findByCourseOrdered($courseId);

        return $this->render('teacher/chapter/by_course.html.twig', [
            'course' => $course,
            'chapters' => $chapters,
        ]);
    }
}
