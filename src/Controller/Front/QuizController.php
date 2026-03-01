<?php
namespace App\Controller\Front;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\Result;
use App\Repository\QuizRepository;
use App\Service\AI\QuizAIService;
use App\Service\QuizPdfExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    public function __construct(
        private QuizAIService $aiService,
        private QuizPdfExportService $pdfExportService
    ) {}

    #[Route('/results', name: 'front_quiz_results', methods: ['GET'])]
    public function results(EntityManagerInterface $em, Request $request): Response
    {
        $resultRepository = $em->getRepository(Result::class);
        $qb = $resultRepository->createQueryBuilder('r')
            ->leftJoin('r.quiz', 'q')
            ->leftJoin('q.questions', 'qs')
            ->addSelect('q', 'qs')
            ->orderBy('r.datepassage', 'DESC');

        $search = $request->query->get('search');
        $quizId = $request->query->get('quizId');
        $performance = $request->query->get('performance');
        if ($quizId) {
            $qb->andWhere('q.idquiz = :quizId')->setParameter('quizId', $quizId);
        }
        if ($search) {
            $qb->andWhere('(q.titre LIKE :search OR r.utilisateur LIKE :search)')
               ->setParameter('search', '%' . $search . '%');
        }
        $results = $qb->getQuery()->getResult();
        if ($performance) {
            $filtered = [];
            foreach ($results as $result) {
                $totalQ = $result->getQuiz()->getQuestions()->count();
                if ($totalQ > 0) {
                    $pct = ($result->getScore() / $totalQ) * 100;
                    if (($performance === 'excellent' && $pct >= 80) ||
                        ($performance === 'bon' && $pct >= 60 && $pct < 80) ||
                        ($performance === 'ameliorer' && $pct < 60)) {
                        $filtered[] = $result;
                    }
                }
            }
            $results = $filtered;
        }
        $quizzes = $em->getRepository(Quiz::class)->findBy(['visible' => true], ['titre' => 'ASC']);

        return $this->render('front/pages/quiz/results.html.twig', [
            'results' => $results,
            'search' => $search,
            'quizId' => $quizId,
            'performance' => $performance,
            'quizzes' => $quizzes
        ]);
    }

    #[Route('/', name: 'quiz_index')]
    public function index(QuizRepository $quizRepository): Response
    {
        $quizzes = $quizRepository->findBy(['visible' => true], ['datecreation' => 'DESC']);
        
        return $this->render('front/pages/quiz/index.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/{id}', name: 'quiz_take', requirements: ['id' => '\d+'])]
    public function take(Quiz $quiz): Response
    {
        if (!$quiz->isVisible()) {
            throw $this->createNotFoundException('Quiz not found');
        }

        // Vérifier si le quiz a des questions
        if ($quiz->getQuestions()->isEmpty()) {
            throw $this->createNotFoundException('Quiz has no questions');
        }

        // Vérifier si toutes les questions ont des réponses
        foreach ($quiz->getQuestions() as $question) {
            if ($question->getAnswers()->isEmpty()) {
                throw $this->createNotFoundException('Quiz has questions without answers');
            }
        }

        return $this->render('front/pages/quiz/pass.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/submit', name: 'quiz_submit', methods: ['POST'])]
    public function submit(Quiz $quiz, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        if (!$quiz->isVisible()) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $answers = $request->request->all('answers');
        $score = 0;
        $totalQuestions = count($quiz->getQuestions());
        $totalPoints = 0; // Score total possible
        $results = [];

        foreach ($quiz->getQuestions() as $question) {
            $questionId = $question->getId();
            $userAnswers = $answers[$questionId] ?? [];
            $correctAnswers = [];
            $isCorrect = false;
            
            // Récupérer les points de la question (défaut: 10 si non défini)
            $questionPoints = $question->getPoints();
            if ($questionPoints === null || $questionPoints === 0) {
                $questionPoints = 10; // Points par défaut si non défini
            }
            $totalPoints += $questionPoints;

            // Récupérer toutes les réponses correctes
            foreach ($question->getAnswers() as $answer) {
                if ($answer->isCorrect()) {
                    $correctAnswers[] = $answer->getId();
                }
            }

            // Vérifier si l'utilisateur a sélectionné au moins une bonne réponse
            foreach ($userAnswers as $userAnswerId) {
                if (in_array($userAnswerId, $correctAnswers)) {
                    $isCorrect = true;
                    break;
                }
            }

            // Ajouter les points seulement si la réponse est correcte
            if ($isCorrect) {
                $score += $questionPoints;
            }

            $results[] = [
                'question' => $question,
                'userAnswerId' => $userAnswers[0] ?? null, // Garder la première réponse pour l'affichage
                'correctAnswerId' => $correctAnswers[0] ?? null, // Garder la première bonne réponse pour l'affichage
                'isCorrect' => $isCorrect,
                'userAnswers' => $userAnswers,
                'correctAnswers' => $correctAnswers,
                'points' => $questionPoints,
                'earnedPoints' => $isCorrect ? $questionPoints : 0
            ];
        }

        // Save result to database
        $result = new Result();
        $result->setQuiz($quiz);
        $result->setScore($score);
        $result->setUtilisateur($request->getClientIp());
        $result->setDatepassage(new \DateTime());
        
        $em->persist($result);
        $em->flush();

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 1) : 0;

        // Recommandations IA
        $aiRecommendations = $this->aiService->adaptDifficulty($percentage);
        $userLevel = $this->aiService->detectStudentLevel($this->getUser());
        $weakTopics = [];
        foreach (array_filter($results, fn($r) => !$r['isCorrect']) as $r) {
            $weakTopics[] = substr($r['question']->getTexte(), 0, 50) . '...';
        }
        $weakTopics = array_slice(array_unique($weakTopics), 0, 5);
        if (!empty($weakTopics)) {
            $aiRecommendations['suggestedTopics'] = array_merge(
                $aiRecommendations['suggestedTopics'] ?? [],
                $weakTopics
            );
            $aiRecommendations['suggestedTopics'] = array_slice(array_unique($aiRecommendations['suggestedTopics']), 0, 5);
        }
        if ($percentage < 80) {
            $aiRecommendations['improvementTime'] = $percentage >= 60 ? 'Environ 2-3 semaines de pratique régulière'
                : ($percentage >= 40 ? 'Environ 1-2 mois avec une pratique quotidienne' : 'Environ 2-3 mois avec un apprentissage structuré');
        }
        $similarQuizzes = $em->getRepository(Quiz::class)->findBy(
            ['visible' => true],
            ['datecreation' => 'DESC'],
            4
        );
        $similarQuizzes = array_values(array_filter($similarQuizzes, fn($q) => $q->getIdquiz() !== $quiz->getIdquiz()));
        $similarQuizzes = array_slice($similarQuizzes, 0, 3);

        // Stocker les données pour l'export PDF
        $session = $request->getSession();
        $session->set('quiz_pdf_export', $this->pdfExportService->buildDataFromProfessorResults(
            $quiz, $results, $score, $totalPoints, $totalQuestions
        ));

        return $this->render('front/pages/quiz/result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage,
            'results' => $results,
            'resultId' => $result->getIdresult(),
            'aiRecommendations' => $aiRecommendations,
            'userLevel' => $userLevel,
            'similarQuizzes' => $similarQuizzes,
            'weakTopics' => $weakTopics
        ]);
    }

    #[Route('/export-results-pdf', name: 'quiz_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request): Response
    {
        $session = $request->getSession();
        $data = $session->get('quiz_pdf_export');

        if (!$data) {
            $this->addFlash('warning', 'Aucune donnée de résultats à exporter. Veuillez refaire le quiz.');
            return $this->redirectToRoute('quiz_index');
        }

        $session->remove('quiz_pdf_export');

        $pdfContent = $this->pdfExportService->generateFromSessionData($data);
        $filename = 'resultats-quiz-' . date('Y-m-d-His') . '.pdf';

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');

        return $response;
    }

    #[Route('/results/{id}', name: 'quiz_results_detail')]
    public function resultsDetail(Result $result): Response
    {
        $quiz = $result->getQuiz();
        
        if (!$quiz->isVisible()) {
            throw $this->createNotFoundException('Quiz not found');
        }

        return $this->render('front/pages/quiz/results_detail.html.twig', [
            'result' => $result,
            'quiz' => $quiz,
        ]);
    }
}
