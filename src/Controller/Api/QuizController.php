<?php

namespace App\Controller\Api;

use App\Entity\Quiz;
use App\Entity\Chapter;
use App\Entity\Question;
use App\Service\QuizService;
use App\Repository\QuizRepository;
use App\Repository\ChapterRepository;
use App\Repository\QuestionRepository;
use App\DTO\QuizGenerationDTO;
use App\DTO\QuizListItemDTO;
use App\DTO\QuizDetailDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/quizzes')]
class QuizController extends AbstractController
{
    public function __construct(
        private QuizService $quizService,
        private QuizRepository $quizRepository,
        private ChapterRepository $chapterRepository,
        private QuestionRepository $questionRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Génère automatiquement un quiz pour un chapitre
     * POST /api/quizzes/chapter/{chapterId}/generate
     */
    #[Route('/chapter/{chapterId}/generate', name: 'api_quiz_generate', methods: ['POST'])]
    public function generateQuiz(int $chapterId, Request $request): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($chapterId);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            // Parse request
            $data = json_decode($request->getContent(), true);
            $numberOfQuestions = $data['numberOfQuestions'] ?? 5;
            $timeLimit = $data['timeLimit'] ?? 300;

            // Générer le quiz
            $quiz = $this->quizService->generateQuizForChapter($chapter, $numberOfQuestions, $timeLimit);

            $dto = new QuizGenerationDTO(
                $quiz->getIdquiz(),
                true,
                $quiz->getQuestions()->count(),
                $quiz->getDifficultyLevel()
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Récupère les détails d'un quiz avec questions et réponses
     * GET /api/quizzes/{quizId}
     */
    #[Route('/{quizId}', name: 'api_quiz_detail', methods: ['GET'])]
    public function getQuizDetail(int $quizId): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json([
                    'success' => false,
                    'error' => 'Quiz non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            // Construire les questions avec réponses
            $questionsData = [];
            foreach ($quiz->getQuestions() as $question) {
                $answersData = [];
                foreach ($question->getAnswers() as $answer) {
                    $answersData[] = [
                        'id' => $answer->getId(),
                        'text' => $answer->getTexte(),
                        'position' => $answer->getPosition(),
                        // N'exposer pas 'correct' au client
                    ];
                }

                $questionsData[] = [
                    'id' => $question->getId(),
                    'text' => $question->getTexte(),
                    'position' => $question->getPosition(),
                    'difficulty' => $question->getDifficulty(),
                    'answers' => $answersData,
                ];
            }

            $dto = new QuizDetailDTO(
                $quiz->getIdquiz(),
                $quiz->getTitre(),
                $quiz->getDescription(),
                $quiz->getStatus(),
                $quiz->getDifficultyLevel(),
                $quiz->getNumberOfQuestions(),
                $quiz->getTimeLimit(),
                $quiz->isVisible(),
                $quiz->getAttempts(),
                $questionsData
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Liste tous les quiz d'un chapitre
     * GET /api/chapters/{chapterId}/quizzes
     */
    #[Route('/chapter/{chapterId}/list', name: 'api_quiz_list', methods: ['GET'])]
    public function listChapterQuizzes(int $chapterId): JsonResponse
    {
        try {
            $chapter = $this->chapterRepository->find($chapterId);
            if (!$chapter) {
                return $this->json([
                    'success' => false,
                    'error' => 'Chapitre non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $quizzes = $this->quizRepository->findAllByChapter($chapter);

            $items = array_map(function(Quiz $quiz) {
                return new QuizListItemDTO(
                    $quiz->getIdquiz(),
                    $quiz->getTitre(),
                    $quiz->getDescription(),
                    $quiz->getStatus(),
                    $quiz->getDifficultyLevel(),
                    $quiz->getNumberOfQuestions(),
                    $quiz->getTimeLimit(),
                    $quiz->isVisible()
                );
            }, $quizzes);

            return $this->json([
                'success' => true,
                'data' => array_map(fn($item) => $item->toArray(), $items),
                'count' => count($items),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Publie un quiz
     * POST /api/quizzes/{quizId}/publish
     */
    #[Route('/{quizId}/publish', name: 'api_quiz_publish', methods: ['POST'])]
    public function publishQuiz(int $quizId): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json([
                    'success' => false,
                    'error' => 'Quiz non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $quiz = $this->quizService->publishQuiz($quiz);

            $dto = new QuizListItemDTO(
                $quiz->getIdquiz(),
                $quiz->getTitre(),
                $quiz->getDescription(),
                $quiz->getStatus(),
                $quiz->getDifficultyLevel(),
                $quiz->getNumberOfQuestions(),
                $quiz->getTimeLimit(),
                $quiz->isVisible()
            );

            return $this->json([
                'success' => true,
                'data' => $dto->toArray(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Randomise les questions d'un quiz
     * POST /api/quizzes/{quizId}/randomize
     */
    #[Route('/{quizId}/randomize', name: 'api_quiz_randomize', methods: ['POST'])]
    public function randomizeQuestions(int $quizId): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json([
                    'success' => false,
                    'error' => 'Quiz non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $randomizedQuestions = $this->quizService->getRandomizedQuestions($quiz);

            $questionsData = array_map(function(Question $question) {
                $answersData = [];
                foreach ($question->getAnswers() as $answer) {
                    $answersData[] = [
                        'id' => $answer->getId(),
                        'text' => $answer->getTexte(),
                    ];
                }

                return [
                    'id' => $question->getId(),
                    'text' => $question->getTexte(),
                    'answers' => $answersData,
                ];
            }, $randomizedQuestions);

            return $this->json([
                'success' => true,
                'data' => [
                    'quizId' => $quizId,
                    'questions' => $questionsData,
                    'totalQuestions' => count($questionsData),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Valide une réponse
     * POST /api/quizzes/{quizId}/validate-answer
     * Body: { "questionId": 1, "answerIndex": 0 }
     */
    #[Route('/{quizId}/validate-answer', name: 'api_quiz_validate', methods: ['POST'])]
    public function validateAnswer(int $quizId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $questionId = $data['questionId'] ?? null;
            $answerIndex = $data['answerIndex'] ?? null;

            if ($questionId === null || $answerIndex === null) {
                return $this->json([
                    'success' => false,
                    'error' => 'questionId et answerIndex sont requis',
                ], Response::HTTP_BAD_REQUEST);
            }

            $question = $this->questionRepository->find($questionId);
            if (!$question) {
                return $this->json([
                    'success' => false,
                    'error' => 'Question non trouvée',
                ], Response::HTTP_NOT_FOUND);
            }

            $result = $this->quizService->validateAnswer($question, $answerIndex);

            return $this->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Démarre une session de quiz
     * POST /api/quizzes/{quizId}/start
     */
    #[Route('/{quizId}/start', name: 'api_quiz_start', methods: ['POST'])]
    public function startQuiz(int $quizId): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json([
                    'success' => false,
                    'error' => 'Quiz non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($quiz->getStatus() !== 'published') {
                return $this->json([
                    'success' => false,
                    'error' => 'Le quiz n\'est pas encore publié',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Incrémenter les tentatives
            $quiz = $this->quizService->incrementAttempts($quiz);

            // Récupérer les questions randomisées
            $questions = $this->quizService->getRandomizedQuestions($quiz);

            $questionsData = array_map(function(Question $question) {
                $answersData = [];
                foreach ($question->getAnswers() as $answer) {
                    $answersData[] = [
                        'id' => $answer->getId(),
                        'text' => $answer->getTexte(),
                    ];
                }

                return [
                    'id' => $question->getId(),
                    'text' => $question->getTexte(),
                    'answers' => $answersData,
                ];
            }, $questions);

            return $this->json([
                'success' => true,
                'data' => [
                    'sessionStarted' => true,
                    'quizId' => $quizId,
                    'titre' => $quiz->getTitre(),
                    'timeLimit' => $quiz->getTimeLimit(),
                    'totalQuestions' => count($questionsData),
                    'questions' => $questionsData,
                    'attempts' => $quiz->getAttempts(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Supprime un quiz
     * DELETE /api/quizzes/{quizId}
     */
    #[Route('/{quizId}', name: 'api_quiz_delete', methods: ['DELETE'])]
    public function deleteQuiz(int $quizId): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($quizId);
            if (!$quiz) {
                return $this->json([
                    'success' => false,
                    'error' => 'Quiz non trouvé',
                ], Response::HTTP_NOT_FOUND);
            }

            $this->quizService->deleteQuiz($quiz);

            return $this->json([
                'success' => true,
                'message' => 'Quiz supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
