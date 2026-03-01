<?php

namespace App\Controller\Front;

use App\Repository\ChapterRepository;
use App\Repository\QuizRepository;
use App\Service\QuizPdfExportService;
use App\Service\QuizService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quiz')]
class QuizFrontController extends AbstractController
{
    public function __construct(
        private QuizService $quizService,
        private QuizRepository $quizRepository,
        private ChapterRepository $chapterRepository,
        private QuizPdfExportService $pdfExportService
    ) {}

    /**
     * Affiche la liste des chapitres pour générer des quiz
     */
    #[Route('/generator', name: 'quiz_generator', methods: ['GET'])]
    public function generator(ChapterRepository $chapterRepository): Response
    {
        $chapters = $chapterRepository->findAll();

        return $this->render('front/quiz/generator.html.twig', [
            'chapters' => $chapters,
        ]);
    }

    /**
     * Génère un quiz pour un chapitre (AJAX)
     */
    #[Route('/generate/{chapterId}', name: 'quiz_generate', methods: ['POST'])]
    public function generate(int $chapterId, Request $request): Response
    {
        try {
            $chapter = $this->chapterRepository->find($chapterId);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé'
                ], 404);
            }

            $numberOfQuestions = (int)($request->request->get('numberOfQuestions') ?? 5);
            $timeLimit = (int)($request->request->get('timeLimit') ?? 300);

            // Générer le quiz
            $quiz = $this->quizService->generateQuizForChapter($chapter, $numberOfQuestions, $timeLimit);

            return $this->json([
                'success' => true,
                'data' => [
                    'quizId' => $quiz->getIdquiz(),
                    'titre' => $quiz->getTitre(),
                    'difficulty' => $quiz->getDifficultyLevel(),
                    'numberOfQuestions' => $quiz->getQuestions()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Affiche un quiz avec les questions
     */
    #[Route('/{quizId}/play', name: 'quiz_play', methods: ['GET'])]
    public function play(int $quizId): Response
    {
        $quiz = $this->quizRepository->find($quizId);
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // L'accès est autorisé si:
        // 1. Le quiz est publié ET visible
        // 2. L'utilisateur est admin
        // 3. L'utilisateur est authentifié (a généré le quiz lui-même)
        $isPublished = $quiz->getStatus() === 'published' && $quiz->isVisible();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $isAuthenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');

        if (!$isPublished && !$isAdmin && !$isAuthenticated) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à ce quiz');
        }

        // Randomiser les questions si nécessaire
        $questions = $this->quizService->getRandomizedQuestions($quiz);

        return $this->render('front/quiz/play.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    /**
     * Soumet une réponse (AJAX)
     */
    #[Route('/{quizId}/submit-answer', name: 'quiz_submit_answer', methods: ['POST'])]
    public function submitAnswer(int $quizId, Request $request): Response
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json(['success' => false, 'error' => 'Quiz non trouvé'], 404);
            }

            $questionId = (int)$request->request->get('questionId');
            $answerIndex = (int)$request->request->get('answerIndex');

            $question = null;
            foreach ($quiz->getQuestions() as $q) {
                if ($q->getId() === $questionId) {
                    $question = $q;
                    break;
                }
            }

            if (!$question) {
                return $this->json(['success' => false, 'error' => 'Question non trouvée'], 404);
            }

            // Valider la réponse
            $result = $this->quizService->validateAnswer($question, $answerIndex);

            return $this->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Soumet les réponses du quiz et affiche les résultats (score + correction)
     */
    #[Route('/{quizId}/submit-complete', name: 'quiz_submit_complete', methods: ['POST'])]
    public function submitComplete(int $quizId, Request $request): Response
    {
        $quiz = $this->quizRepository->find($quizId);
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        $answersData = $request->request->all('answers');
        $answers = [];
        $correctAnswers = 0;
        $totalQuestions = $quiz->getQuestions()->count();

        foreach ($quiz->getQuestions() as $question) {
            $questionId = $question->getId();
            $selectedIndex = isset($answersData[$questionId]) ? (int) $answersData[$questionId] : -1;
            $isCorrect = false;

            if ($selectedIndex >= 0) {
                $result = $this->quizService->validateAnswer($question, $selectedIndex);
                $isCorrect = $result['isCorrect'];
            }

            $answers[$questionId] = [
                'isCorrect' => $isCorrect,
                'selectedIndex' => $selectedIndex,
            ];

            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        $finalScore = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Stocker les données pour l'export PDF
        $session = $request->getSession();
        $session->set('quiz_pdf_export', $this->pdfExportService->buildDataFromStudentAnswers(
            $quiz, $answers, $correctAnswers, $totalQuestions, $finalScore
        ));

        return $this->render('front/quiz/results.html.twig', [
            'quiz' => $quiz,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'finalScore' => $finalScore,
            'answers' => $answers,
        ]);
    }

    /**
     * Affiche les résultats après completion (GET pour liens directs - redirige si pas de données)
     */
    #[Route('/{quizId}/results', name: 'quiz_results', methods: ['GET'])]
    public function results(int $quizId, Request $request): Response
    {
        $quiz = $this->quizRepository->find($quizId);
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        $session = $request->getSession();
        $answers = $session->get('quiz_answers_' . $quizId, []);

        if (empty($answers)) {
            return $this->redirectToRoute('quiz_play', ['quizId' => $quizId]);
        }

        $correctAnswers = 0;
        $totalQuestions = $quiz->getQuestions()->count();
        foreach ($answers as $answerData) {
            if ($answerData['isCorrect']) {
                $correctAnswers++;
            }
        }
        $finalScore = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Stocker les données pour l'export PDF avant de supprimer la session
        $session->set('quiz_pdf_export', $this->pdfExportService->buildDataFromStudentAnswers(
            $quiz, $answers, $correctAnswers, $totalQuestions, $finalScore
        ));
        $session->remove('quiz_answers_' . $quizId);

        return $this->render('front/quiz/results.html.twig', [
            'quiz' => $quiz,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'finalScore' => $finalScore,
            'answers' => $answers,
        ]);
    }

    /**
     * Liste des quiz publiés
     */
    #[Route('/list', name: 'quiz_list', methods: ['GET'])]
    public function list(): Response
    {
        $quizzes = $this->quizRepository->findBy(['status' => 'published']);

        return $this->render('front/quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }
}
