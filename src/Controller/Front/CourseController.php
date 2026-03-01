<?php

namespace App\Controller\Front;

use App\Entity\Course;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChapterRepository;
use App\Repository\ConversationMessageRepository;
use App\Repository\CourseRepository;
use App\Repository\MessageRepository;
use App\Service\CourseService;
use App\DTO\CourseAISummaryDTO;
use App\DTO\CourseKeywordsDTO;
use App\DTO\ComplementaryResourcesDTO;
use App\DTO\CourseSearchResponseDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cours')]
class CourseController extends AbstractController
{
    public function __construct(
        private CourseService $courseService
    ) {}

    #[Route('/', name: 'front_cours_list')]
    public function list(
        Request $request,
        CourseRepository $courseRepository
    ): Response {
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        if ($search || $category) {
            $courses = $courseRepository->searchCourses($search, $category);
        } else {
            $courses = $courseRepository->findActiveCourses();
        }

        $categories = $courseRepository->getAvailableCategories();

        return $this->render('front/pages/cours_list.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Recherche avancée avec pagination
     * Endpoint API: GET /api/courses/search?title=...&category=...&keywords=...&sort=...&page=...&limit=...
     */
    #[Route('/api/search', name: 'api_courses_search', methods: ['GET'])]
    public function apiSearch(Request $request): JsonResponse
    {
        try {
            $title = $request->query->get('title');
            $category = $request->query->get('category');
            $keywords = $request->query->get('keywords');
            $sortBy = $request->query->get('sort', 'date');
            $page = (int) $request->query->get('page', 1);
            $limit = (int) $request->query->get('limit', 12);

            // Validation du tri
            $validSortOptions = ['date', 'popularity', 'views', 'price_asc', 'price_desc'];
            if (!in_array($sortBy, $validSortOptions)) {
                $sortBy = 'date';
            }

            // Validation pagination
            $page = max(1, $page);
            $limit = min(100, max(1, $limit));

            $results = $this->courseService->searchCourses($title, $category, $keywords, $sortBy, $page, $limit);

            // Convertir les DTOs en arrays pour la réponse JSON
            $itemsData = [];
            foreach ($results['items'] as $item) {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    $itemsData[] = $item->toArray();
                } elseif (is_array($item)) {
                    $itemsData[] = $item;
                } else {
                    $itemsData[] = (array) $item;
                }
            }

            $response = new CourseSearchResponseDTO(
                $itemsData,
                $results['total'],
                $results['page'],
                $results['limit'],
                $results['pages']
            );

            return $this->json([
                'success' => true,
                'data' => $response->toArray(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'front_cours_show', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        CourseRepository $courseRepository,
        ChapterRepository $chapterRepository
    ): Response {
        $course = $courseRepository->find($id);
        
        if (!$course || $course->getStatus() !== 1) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        // Incrémente les vues
        $this->courseService->incrementViews($id);
        $this->courseService->updateLastAccessed($id);

        $chapters = $chapterRepository->findByCourseOrdered($course);

        // Récupère les ressources complémentaires
        $resourcesData = $this->courseService->getComplementaryResources($id);

        return $this->render('front/pages/cours_show.html.twig', [
            'course' => $course,
            'chapters' => $chapters,
            'complementaryResources' => $resourcesData['articles'] ?? [],
            'summary' => $course->getWikipediaSummary(),
            'keywords' => $course->getKeywords(),
        ]);
    }

    /**
     * API: Récupère un cours avec ses ressources complémentaires
     * Endpoint: GET /api/courses/{id}
     */
    #[Route('/api/{id}', name: 'api_course_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function apiShowCourse(int $id): JsonResponse
    {
        try {
            $data = $this->courseService->getCourseWithResources($id);
            
            $this->courseService->incrementViews($id);
            $this->courseService->updateLastAccessed($id);

            return $this->json([
                'success' => true,
                'data' => [
                    'course' => [
                        'id' => $data['course']->getId(),
                        'title' => $data['course']->getTitre(),
                        'description' => $data['course']->getDescription(),
                        'category' => $data['course']->getCategory(),
                        'price' => $data['course']->getPrice(),
                        'image_url' => $data['course']->getImageUrl(),
                        'summary' => $data['course']->getWikipediaSummary(),
                        'keywords' => $data['course']->getKeywords(),
                        'views' => $data['course']->getViews(),
                        'likes' => $data['course']->getLikes(),
                    ],
                    'complementary_resources' => $data['resources'],
                ],
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * API: Génère un résumé IA pour un cours
     * Endpoint: POST /api/courses/{id}/generate-summary
     */
    #[Route('/api/{id}/generate-summary', name: 'api_course_generate_summary', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function apiGenerateSummary(int $id): JsonResponse
    {
        set_time_limit(180);
        ignore_user_abort(false); // Arrêter le script si l'utilisateur ferme/rafraîchit la page

        try {
            if (connection_aborted()) {
                return $this->json(['success' => false, 'error' => 'Client déconnecté'], 499);
            }
            $result = $this->courseService->generateCourseSummary($id);

            if (!$result['success']) {
                return $this->json([
                    'success' => false,
                    'error' => $result['error'],
                ], Response::HTTP_BAD_REQUEST);
            }

            $dto = new CourseAISummaryDTO(
                $id,
                $result['success'],
                $result['summary'],
                $result['error']
            );

            return $this->json([
                'success' => true,
                'message' => 'Résumé IA généré avec succès',
                'data' => $dto->toArray(),
            ]);

        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (($_ENV['APP_ENV'] ?? '') === 'dev') {
                $msg .= ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
            }
            return $this->json([
                'success' => false,
                'error' => $msg,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API: Génère des mots-clés IA pour un cours
     * Endpoint: POST /api/courses/{id}/generate-keywords
     */
    #[Route('/api/{id}/generate-keywords', name: 'api_course_generate_keywords', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function apiGenerateKeywords(int $id): JsonResponse
    {
        set_time_limit(130);
        ignore_user_abort(false);
        try {
            if (connection_aborted()) {
                return $this->json(['success' => false, 'error' => 'Client déconnecté'], 499);
            }
            $result = $this->courseService->generateCourseKeywords($id);

            if (!$result['success']) {
                return $this->json([
                    'success' => false,
                    'error' => $result['error'],
                ], Response::HTTP_BAD_REQUEST);
            }

            $keywordsList = $result['keywords'] ? explode(',', $result['keywords']) : [];
            
            $dto = new CourseKeywordsDTO(
                $id,
                $result['success'],
                $result['keywords'],
                $keywordsList,
                $result['error']
            );

            return $this->json([
                'success' => true,
                'message' => 'Mots-clés générés avec succès',
                'data' => $dto->toArray(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API: Récupère les ressources complémentaires Wikipedia pour un cours
     * Endpoint: GET /api/courses/{id}/resources
     */
    #[Route('/api/{id}/resources', name: 'api_course_resources', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function apiCourseResources(int $id): JsonResponse
    {
        try {
            $result = $this->courseService->getComplementaryResources($id);

            if (!$result['success']) {
                return $this->json([
                    'success' => false,
                    'error' => $result['error'],
                ], Response::HTTP_BAD_REQUEST);
            }

            $dto = new ComplementaryResourcesDTO(
                $result['success'],
                $result['articles'],
                count($result['articles']),
                $result['error']
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API: Incrémente les likes d'un cours
     * Endpoint: POST /api/courses/{id}/like
     */
    #[Route('/api/{id}/like', name: 'api_course_like', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function apiLikeCourse(int $id): JsonResponse
    {
        try {
            $this->courseService->incrementLikes($id);

            return $this->json([
                'success' => true,
                'message' => 'Cours liké avec succès',
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API: Récupère les cours les plus populaires
     * Endpoint: GET /api/courses/popular
     */
    #[Route('/api/popular', name: 'api_courses_popular', methods: ['GET'])]
    public function apiPopularCourses(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query->get('limit', 6);
            $limit = min(20, max(1, $limit));

            $courses = $this->courseService->getMostPopularCourses($limit);

            return $this->json([
                'success' => true,
                'data' => array_map(function($course) {
                    return [
                        'id' => $course->getId(),
                        'title' => $course->getTitre(),
                        'category' => $course->getCategory(),
                        'popularity_score' => $course->getPopularityScore(),
                        'views' => $course->getViews(),
                    ];
                }, $courses),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API: Récupère les cours les plus récents
     * Endpoint: GET /api/courses/latest
     */
    #[Route('/api/latest', name: 'api_courses_latest', methods: ['GET'])]
    public function apiLatestCourses(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query->get('limit', 6);
            $limit = min(20, max(1, $limit));

            $courses = $this->courseService->getLatestCourses($limit);

            return $this->json([
                'success' => true,
                'data' => array_map(function($course) {
                    return [
                        'id' => $course->getId(),
                        'title' => $course->getTitre(),
                        'category' => $course->getCategory(),
                        'image_url' => $course->getImageUrl(),
                        'created_at' => $course->getCreatedAt()?->format('Y-m-d H:i:s'),
                    ];
                }, $courses),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

        if (!$course || $course->getStatus() !== 1) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        if (!$chapter || $chapter->getCourse()?->getId() !== $id) {
            throw $this->createNotFoundException('Chapitre introuvable');
        }

        $chapters = $chapterRepository->findByCourseOrdered($course);

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
        
        if (!$course || $course->getStatus() !== 1) {
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
            $message = $messageRepository->findOrCreateDiscussion($course->getTitre(), $studentName);
            
            // Mettre à jour le message
            $message->setContent($content);
            $message->setStudent($studentName);
            $message->setTeacher($course->getTitre() . ' - Professeur');
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
        
        if (!$course || $course->getStatus() !== 1) {
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
        
        if (!$course || $course->getStatus() !== 1) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        $studentName = $request->query->get('student');
        $messages = [];

        // Récupérer tous les messages du cours
        $messages = $messageRepository->findByCourseTitle($course->getTitre());
        
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
