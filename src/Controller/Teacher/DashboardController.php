<?php

namespace App\Controller\Teacher;

use App\Repository\CourseRepository;
use App\Repository\ChapterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'teacher_dashboard')]
    public function index(
        CourseRepository $courseRepository,
        ChapterRepository $chapterRepository
    ): Response {
        $teacher = $this->getUser();

        $courses = $courseRepository->findBy(
            ['teacher' => $teacher],
            ['created_at' => 'DESC']
        );

        $totalCourses = count($courses);
        $activeCourses = count(array_filter($courses, fn($c) => $c->getStatus() === 1));
        $inactiveCourses = $totalCourses - $activeCourses;

        // Count total chapters across teacher's courses
        $totalChapters = 0;
        foreach ($courses as $course) {
            $totalChapters += $course->getChapters()->count();
        }

        // Recent courses (last 5)
        $recentCourses = array_slice($courses, 0, 5);

        return $this->render('teacher/dashboard/index.html.twig', [
            'stats' => [
                'totalCourses' => $totalCourses,
                'activeCourses' => $activeCourses,
                'inactiveCourses' => $inactiveCourses,
                'totalChapters' => $totalChapters,
            ],
            'recentCourses' => $recentCourses,
        ]);
    }
}
