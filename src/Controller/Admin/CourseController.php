<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\MessageRepository;
use App\Service\External\UnsplashService;
use App\Service\External\WikipediaService;
use App\Service\CourseScoringService;
use App\Service\CourseRecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/course')]
#[IsGranted('ROLE_ADMIN')]
class CourseController extends AbstractController
{
    private CourseScoringService $scoringService;
    private CourseRecommendationService $recommendationService;
    private UnsplashService $unsplashService;
    private WikipediaService $wikipediaService;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        CourseScoringService $scoringService,
        CourseRecommendationService $recommendationService,
        UnsplashService $unsplashService,
        WikipediaService $wikipediaService,
        ParameterBagInterface $parameterBag
    ) {
        $this->scoringService = $scoringService;
        $this->recommendationService = $recommendationService;
        $this->unsplashService = $unsplashService;
        $this->wikipediaService = $wikipediaService;
        $this->parameterBag = $parameterBag;
    }

    #[Route('/', name: 'admin_course_index', methods: ['GET'])]
    public function index(
        Request $request,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $status = $request->query->get('status');
        $sortBy = $request->query->get('sortBy', 'created_at');
        $sortOrder = $request->query->get('sortOrder', 'DESC');

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

        // Validation du champ de tri
        $allowedSortFields = ['created_at', 'titre', 'popularity_score', 'views', 'likes', 'category'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        $courses = $qb->orderBy('c.' . $sortBy, $sortOrder)
                      ->getQuery()
                      ->getResult();

        $categories = $courseRepository->getAvailableCategories();
        
        // Statistiques globales
        $statistics = $this->scoringService->getGlobalStatistics();
        
        // Top cours
        $topCourses = $this->scoringService->getTopCourses(5);
        
        // Cours trending
        $trendingCourses = $this->scoringService->getTrendingCourses(3);

        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedStatus' => $status,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'statistics' => $statistics,
            'topCourses' => $topCourses,
            'trendingCourses' => $trendingCourses,
        ]);
    }

    #[Route('/new', name: 'admin_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $course = new Course();
        $course->setCreatedAt(new \DateTime());
        $course->setStatus(1);
        
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Génération des mots-clés
            $keywords = $course->generateKeywords();
            $course->setKeywords($keywords);

            // Nettoyage du champ imageUrl pour éviter les doubles chemins
            if ($course->getImageUrl()) {
                $imageUrl = $course->getImageUrl();
                // Si le chemin commence déjà par /uploads/courses/, on le garde tel quel
                if (strpos($imageUrl, '/uploads/courses/') === 0) {
                    $course->setImageUrl('/uploads/courses/' . basename($imageUrl));
                }
            }

            // Récupération automatique d'image depuis Unsplash
            if (!$course->getImageUrl()) {
                $imageKeywords = $this->unsplashService->extractKeywords($course->getTitre());
                $imageData = $this->unsplashService->getRandomImage($imageKeywords);
                
                if ($imageData) {
                    $filename = $this->unsplashService->generateImageFilename($course->getTitre());
                    $savedImagePath = $this->unsplashService->downloadAndSaveImage($imageData['url'], $filename);
                    
                    if ($savedImagePath) {
                        $course->setImageUrl($savedImagePath);
                    }
                }
            }

            // Récupération automatique du résumé Wikipedia
            if (!$course->getWikipediaSummary() && $course->getDescription()) {
                $wikipediaData = $this->wikipediaService->getSummary($course->getTitre());
                
                if ($wikipediaData && $wikipediaData['summary']) {
                    $formattedSummary = $this->wikipediaService->formatSummary($wikipediaData['summary'], 1000);
                    $course->setWikipediaSummary($formattedSummary);
                }
            }

            // Gestion du fichier PDF
            $pdfFile = $form->get('pdfUpload')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.pdf';
                $pdfFile->move(
                    $this->parameterBag->resolveValue('%kernel.project_dir%') . '/public/uploads/courses/pdf',
                    $newFilename
                );
                $course->setPdfFile($newFilename);
            }

            // Calcul du score initial
            $course->calculatePopularityScore();

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
        // Enregistrement de la vue
        $this->scoringService->recordView($course);
        
        $courseMessages = $messageRepository->findByCourseTitle($course->getTitre());
        
        // Récupération des cours similaires
        $similarCourses = $this->recommendationService->getSimilarCourses($course, 3);
        
        // Statistiques du cours
        $courseStatistics = [
            'views' => $course->getViews(),
            'likes' => $course->getLikes(),
            'comments' => $course->getCommentsCount(),
            'score' => $course->getPopularityScore(),
            'chapters_count' => $course->getChaptersCount(),
            'is_trending' => $course->isTrending(),
        ];
        
        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
            'courseMessages' => $courseMessages,
            'similarCourses' => $similarCourses,
            'courseStatistics' => $courseStatistics,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour des mots-clés
            $keywords = $course->generateKeywords();
            $course->setKeywords($keywords);

            // Mise à jour de l'image si nécessaire
            if (!$course->getImageUrl()) {
                $imageKeywords = $this->unsplashService->extractKeywords($course->getTitre());
                $imageData = $this->unsplashService->getRandomImage($imageKeywords);
                
                if ($imageData) {
                    $filename = $this->unsplashService->generateImageFilename($course->getTitre());
                    $savedImagePath = $this->unsplashService->downloadAndSaveImage($imageData['url'], $filename);
                    
                    if ($savedImagePath) {
                        $course->setImageUrl($savedImagePath);
                    }
                }
            }

            // Mise à jour du résumé Wikipedia si nécessaire
            if (!$course->getWikipediaSummary() && $course->getDescription()) {
                $wikipediaData = $this->wikipediaService->getSummary($course->getTitre());
                
                if ($wikipediaData && $wikipediaData['summary']) {
                    $formattedSummary = $this->wikipediaService->formatSummary($wikipediaData['summary'], 1000);
                    $course->setWikipediaSummary($formattedSummary);
                }
            }

            // Gestion du fichier PDF
            $pdfFile = $form->get('pdfUpload')->getData();
            if ($pdfFile) {
                // Suppression de l'ancien PDF s'il existe
                if ($course->getPdfFile()) {
                    $oldPath = $this->parameterBag->resolveValue('%kernel.project_dir%') . '/public/uploads/courses/pdf/' . $course->getPdfFile();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.pdf';
                $pdfFile->move(
                    $this->parameterBag->resolveValue('%kernel.project_dir%') . '/public/uploads/courses/pdf',
                    $newFilename
                );
                $course->setPdfFile($newFilename);
            }

            // Recalcul du score
            $course->calculatePopularityScore();

            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été modifié avec succès !');

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/edit_original.html.twig', [
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

    // Routes API pour les fonctionnalités avancées

    #[Route('/api/search-unsplash', name: 'admin_course_search_unsplash', methods: ['POST'])]
    public function searchUnsplashImages(Request $request): JsonResponse
    {
        $query = $request->request->get('query');
        
        if (!$query) {
            return new JsonResponse(['error' => 'Query parameter is required'], 400);
        }

        $images = $this->unsplashService->searchImages($query, 1, 6);
        
        if ($images) {
            return new JsonResponse([
                'success' => true,
                'images' => $images['images']
            ]);
        }

        return new JsonResponse(['error' => 'No images found'], 404);
    }

    #[Route('/api/get-wikipedia-summary', name: 'admin_course_wikipedia_summary', methods: ['POST'])]
    public function getWikipediaSummary(Request $request): JsonResponse
    {
        $query = $request->request->get('query');
        
        if (!$query) {
            return new JsonResponse(['error' => 'Query parameter is required'], 400);
        }

        $summary = $this->wikipediaService->getSummary($query);
        
        if ($summary) {
            return new JsonResponse([
                'success' => true,
                'summary' => $summary
            ]);
        }

        return new JsonResponse(['error' => 'No summary found'], 404);
    }

    #[Route('/api/update-scores', name: 'admin_course_update_scores', methods: ['POST'])]
    public function updateAllScores(): JsonResponse
    {
        try {
            $updatedCount = $this->scoringService->updateAllCoursesScores();
            
            return new JsonResponse([
                'success' => true,
                'message' => "Scores updated for {$updatedCount} courses",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/get-recommendations/{id}', name: 'admin_course_get_recommendations', methods: ['GET'])]
    public function getRecommendations(Course $course): JsonResponse
    {
        try {
            $similarCourses = $this->recommendationService->getSimilarCourses($course, 5);
            
            $recommendations = [];
            foreach ($similarCourses as $similarCourse) {
                $recommendations[] = [
                    'id' => $similarCourse->getId(),
                    'title' => $similarCourse->getTitre(),
                    'description' => substr($similarCourse->getDescription() ?? '', 0, 150) . '...',
                    'category' => $similarCourse->getCategory(),
                    'popularity_score' => $similarCourse->getPopularityScore(),
                    'similarity_score' => $this->recommendationService->calculateSimilarityScore($course, $similarCourse)
                ];
            }
            
            return new JsonResponse([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/record-like/{id}', name: 'admin_course_record_like', methods: ['POST'])]
    public function recordLike(Course $course, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->scoringService->recordLike($course);
            
            return new JsonResponse([
                'success' => true,
                'likes' => $course->getLikes(),
                'score' => $course->getPopularityScore()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/record-comment/{id}', name: 'admin_course_record_comment', methods: ['POST'])]
    public function recordComment(Course $course, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->scoringService->recordComment($course);
            
            return new JsonResponse([
                'success' => true,
                'comments_count' => $course->getCommentsCount(),
                'score' => $course->getPopularityScore()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/statistics', name: 'admin_course_statistics', methods: ['GET'])]
    public function statistics(): Response
    {
        $globalStats = $this->scoringService->getGlobalStatistics();
        $topCourses = $this->scoringService->getTopCourses(10);
        $trendingCourses = $this->scoringService->getTrendingCourses(5);
        $coursesByCategory = $this->scoringService->getCoursesByCategoryWithScores();
        
        return $this->render('admin/course/statistics.html.twig', [
            'globalStats' => $globalStats,
            'topCourses' => $topCourses,
            'trendingCourses' => $trendingCourses,
            'coursesByCategory' => $coursesByCategory,
        ]);
    }

    #[Route('/dashboard', name: 'admin_course_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $globalStats = $this->scoringService->getGlobalStatistics();
        $topCourses = $this->scoringService->getTopCourses(5);
        $trendingCourses = $this->scoringService->getTrendingCourses(3);
        $mostViewedCourses = $this->scoringService->getMostViewedCourses(3);
        $mostLikedCourses = $this->scoringService->getMostLikedCourses(3);
        
        return $this->render('admin/course/dashboard.html.twig', [
            'globalStats' => $globalStats,
            'topCourses' => $topCourses,
            'trendingCourses' => $trendingCourses,
            'mostViewedCourses' => $mostViewedCourses,
            'mostLikedCourses' => $mostLikedCourses,
        ]);
    }
}
