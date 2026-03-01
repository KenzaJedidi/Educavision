<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Chapter;
use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class QuizService
{
    public function __construct(
        private QuizRepository $quizRepository,
        private QuestionRepository $questionRepository,
        private EntityManagerInterface $entityManager,
        private QuizAIMockService $quizAIService,
        private LoggerInterface $logger
    ) {}

    /**
     * Génère automatiquement un quiz pour un chapitre
     */
    public function generateQuizForChapter(
        Chapter $chapter,
        int $numberOfQuestions = 5,
        ?int $timeLimit = 300
    ): Quiz {
        // Créer le quiz
        $quiz = new Quiz();
        $quiz->setChapter($chapter);
        $quiz->setTitre('Quiz: ' . $chapter->getTitre());
        $quiz->setDescription('Quiz généré automatiquement pour le chapitre: ' . $chapter->getTitre());
        $quiz->setStatus('published');
        $quiz->setNumberOfQuestions($numberOfQuestions);
        $quiz->setTimeLimit($timeLimit ?? 0);
        $quiz->setVisible(true);
        $quiz->setDatecreation(new \DateTime());

        // Générer les questions via IA
        $generatedQuestions = $this->quizAIService->generateQuestions(
            $chapter->getTitre(),
            $chapter->getDescription() ?? $chapter->getTitre(),
            $numberOfQuestions
        );

        // Détecter la difficulté
        $difficulty = $this->quizAIService->detectQuizDifficulty($generatedQuestions);
        $quiz->setDifficultyLevel($difficulty);

        // Créer les entités Question et Answer
        foreach ($generatedQuestions as $idx => $questionData) {
            $question = new Question();
            $question->setQuiz($quiz);
            $question->setTexte($questionData['text']);
            $question->setPosition($idx + 1);
            $question->setDifficulty($questionData['difficulty'] ?? 'Moyen');

            // Ajouter les réponses
            foreach ($questionData['answers'] as $answerIdx => $answerText) {
                $answer = new Answer();
                $answer->setQuestion($question);
                $answer->setTexte($answerText);
                $answer->setPosition($answerIdx + 1);
                $answer->setCorrect($answerIdx === $questionData['correctAnswer']);

                $question->addAnswer($answer);
            }

            $quiz->addQuestion($question);
        }

        // Persister et flush
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $this->logger->info('✅ Quiz généré automatiquement', [
            'quiz_id' => $quiz->getIdquiz(),
            'chapter_id' => $chapter->getId(),
            'questions_count' => count($generatedQuestions),
            'difficulty' => $difficulty,
        ]);

        return $quiz;
    }

    /**
     * Récupère ou crée un quiz pour un chapitre
     */
    public function getOrCreateQuiz(Chapter $chapter, int $numberOfQuestions = 5, ?int $timeLimit = 300): Quiz
    {
        $quiz = $this->quizRepository->findByChapter($chapter);

        if (!$quiz) {
            $quiz = $this->generateQuizForChapter($chapter, $numberOfQuestions, $timeLimit);
        }

        return $quiz;
    }

    /**
     * Publie un quiz (rend visible)
     */
    public function publishQuiz(Quiz $quiz): Quiz
    {
        $quiz->setStatus('published');
        $quiz->setVisible(true);
        $quiz->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        $this->logger->info('✅ Quiz publié', [
            'quiz_id' => $quiz->getIdquiz(),
        ]);

        return $quiz;
    }

    /**
     * Randomise les questions pour une session
     */
    public function getRandomizedQuestions(Quiz $quiz): array
    {
        $questions = $quiz->getQuestions()->toArray();
        return $this->quizAIService->randomizeQuestions($questions);
    }

    /**
     * Valide une réponse
     */
    public function validateAnswer(Question $question, int $answerIndex): array
    {
        // Trouver la bonne réponse
        $answers = $question->getAnswers()->toArray();
        $correctAnswer = null;

        foreach ($answers as $answer) {
            if ($answer->isCorrect()) {
                $correctAnswer = $answer;
                break;
            }
        }

        if (!$correctAnswer) {
            return [
                'isCorrect' => false,
                'score' => 0,
                'feedback' => '❌ Erreur: pas de bonne réponse définie'
            ];
        }

        $givenAnswer = $answers[$answerIndex] ?? null;
        $isCorrect = $givenAnswer && $givenAnswer->getId() === $correctAnswer->getId();

        return [
            'isCorrect' => $isCorrect,
            'score' => $isCorrect ? 100 : 0,
            'feedback' => $isCorrect ? '✅ Bonne réponse!' : '❌ Mauvaise réponse! La bonne réponse était: ' . $correctAnswer->getTexte()
        ];
    }

    /**
     * Calcule le score final d'un quiz
     */
    public function calculateFinalScore(array $answers): int
    {
        if (empty($answers)) {
            return 0;
        }

        $totalScore = 0;
        foreach ($answers as $answerResult) {
            if ($answerResult['isCorrect']) {
                $totalScore += 100;
            }
        }

        return (int) ($totalScore / count($answers));
    }

    /**
     * Met à jour le nombre de tentatives
     */
    public function incrementAttempts(Quiz $quiz): Quiz
    {
        $quiz->setAttempts($quiz->getAttempts() + 1);
        $this->entityManager->flush();

        return $quiz;
    }

    /**
     * Supprime un quiz
     */
    public function deleteQuiz(Quiz $quiz): void
    {
        $this->entityManager->remove($quiz);
        $this->entityManager->flush();

        $this->logger->info('✅ Quiz supprimé', [
            'quiz_id' => $quiz->getIdquiz(),
        ]);
    }
}
