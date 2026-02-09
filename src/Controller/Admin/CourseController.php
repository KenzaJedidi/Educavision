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
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/course')]
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

        $qb = $courseRepository->createQueryBuilder('c');

        if ($search) {
            $qb->andWhere('c.titre LIKE :search OR c.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $qb->andWhere('c.category = :category')
               ->setParameter('category', $category);
        }

        if ($status !== null && $status !== '') {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', (int)$status);
        }

        $courses = $qb->orderBy('c.created_at', 'DESC')
                      ->getQuery()
                      ->getResult();

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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $course = new Course();
        $course->setCreatedAt(new \DateTime());
        
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('pdfUpload')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.pdf';
                $pdfFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/courses/pdf',
                    $newFilename
                );
                $course->setPdfFile($newFilename);
            }

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
    public function show(
        Course $course,
        MessageRepository $messageRepository
    ): Response {
        $courseMessages = $messageRepository->findByCourseTitle($course->getTitre());
        
        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
            'courseMessages' => $courseMessages,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('pdfUpload')->getData();
            if ($pdfFile) {
                // Delete old PDF if exists
                if ($course->getPdfFile()) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/uploads/courses/pdf/' . $course->getPdfFile();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.pdf';
                $pdfFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/courses/pdf',
                    $newFilename
                );
                $course->setPdfFile($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été modifié avec succès !');

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_course_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($course);
        $entityManager->flush();

        $this->addFlash('success', 'Le cours a été supprimé avec succès !');

        return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-status', name: 'admin_course_toggle_status', methods: ['GET'])]
    public function toggleStatus(Course $course, EntityManagerInterface $entityManager): Response
    {
        $course->setStatus($course->getStatus() === 1 ? 0 : 1);
        $entityManager->flush();

        $status = $course->getStatus() === 1 ? 'activé' : 'désactivé';
        $this->addFlash('success', "Le cours a été {$status} avec succès !");

        return $this->redirectToRoute('admin_course_index');
    }
}
