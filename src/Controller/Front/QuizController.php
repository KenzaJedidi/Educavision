<?php
namespace App\Controller\Front;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\Result;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/quiz')]
class QuizController extends AbstractController
{
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

        return $this->render('front/pages/quiz/result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage,
            'results' => $results,
            'resultId' => $result->getIdresult()
        ]);
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
