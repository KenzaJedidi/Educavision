<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/course')]
#[IsGranted('ROLE_ADMIN')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'admin_course_index', methods: ['GET'])]
    public function index(
        Request $request,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $status = $request->query->get('status');

        // Convertir le statut seulement s'il est défini
        $statusValue = null;
        if ($status !== null && $status !== '') {
            $statusValue = $status === '1' ? 'published' : 'draft';
        }

        $courses = $courseRepository->searchCourses($search, $category, $statusValue);
        $categories = $courseRepository->getAvailableCategories();

        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedStatus' => $status,
        ]);
    }

    #[Route('/new', name: 'admin_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été créé avec succès !');

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_course_show', methods: ['GET'])]
    public function show(Course $course, MessageRepository $messageRepository): Response
    {
        // Récupérer les messages associés à ce cours
        $courseMessages = $messageRepository->findBy(['course' => $course], ['created_at' => 'DESC']);
        
        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
            'courseMessages' => $courseMessages,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été modifié avec succès !');

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérer les messages associés à ce cours
        $courseMessages = $messageRepository->findBy(['course' => $course], ['created_at' => 'DESC']);

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
            'courseMessages' => $courseMessages,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_course_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Pour les requêtes GET (compatibilité), sauter la validation CSRF
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $entityManager->remove($course);
        $entityManager->flush();

        $this->addFlash('success', 'Le cours a été supprimé avec succès !');

        return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-status', name: 'admin_course_toggle_status', methods: ['POST', 'GET'])]
    public function toggleStatus(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // For GET requests (backward compatibility), skip CSRF validation
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('toggle'.$course->getId(), $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $newStatus = $course->getStatus() === 'published' ? 'draft' : 'published';
        $course->setStatus($newStatus);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Le cours a été %s avec succès !', 
            $newStatus === 'published' ? 'publié' : 'mis en brouillon'));

        return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
