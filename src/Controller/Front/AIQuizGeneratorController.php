<?php

namespace App\Controller\Front;

use App\Service\AI\QuizGeneratorFromTextService;
use App\Service\FileTextExtractorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quiz/ai')]
class AIQuizGeneratorController extends AbstractController
{
    public function __construct(
        private QuizGeneratorFromTextService $quizGenerator,
        private FileTextExtractorService $fileTextExtractor
    ) {}
    
    /**
     * Page de création de quiz par IA
     */
    #[Route('/create', name: 'ai_quiz_create')]
    public function create(): Response
    {
        return $this->render('front/pages/quiz/ai_create.html.twig');
    }
    
    /**
     * Génération du quiz à partir d'une pièce jointe (PDF ou TXT)
     */
    #[Route('/generate', name: 'ai_quiz_generate', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        $file = $request->files->get('courseFile');
        $result = $this->fileTextExtractor->extractFromUploadedFile($file);

        if ($result['error']) {
            $this->addFlash('error', $result['error']);
            return $this->redirectToRoute('ai_quiz_create');
        }

        $text = $result['text'];
        $title = $request->request->get('title', 'Quiz généré par IA');
        $description = $request->request->get('description', 'Quiz créé automatiquement');
        $questionCount = (int)$request->request->get('questionCount', 10);
        $difficulty = $request->request->get('difficulty', 'medium');
        $duration = (int)$request->request->get('duration', 30);
        $includeQCM = $request->request->get('includeQCM', 'on') === 'on';
        $includeTrueFalse = $request->request->get('includeTrueFalse', 'on') === 'on';
        $includeOpen = $request->request->get('includeOpen', 'off') === 'on';
        
        if ($questionCount < 5 || $questionCount > 50) {
            $this->addFlash('error', 'Le nombre de questions doit être entre 5 et 50.');
            return $this->redirectToRoute('ai_quiz_create');
        }
        
        // Générer le quiz
        try {
            $quizData = $this->quizGenerator->generateQuizFromText($text, [
                'title' => $title,
                'description' => $description,
                'questionCount' => $questionCount,
                'difficulty' => $difficulty,
                'duration' => $duration,
                'includeQCM' => $includeQCM,
                'includeTrueFalse' => $includeTrueFalse,
                'includeOpen' => $includeOpen
            ]);
            
            return $this->render('front/pages/quiz/ai_preview.html.twig', [
                'quizData' => $quizData,
                'originalText' => $text,
                'regenerateParams' => [
                    'title' => $title,
                    'description' => $description,
                    'questionCount' => $questionCount,
                    'difficulty' => $difficulty,
                    'duration' => $duration,
                    'includeQCM' => $includeQCM,
                    'includeTrueFalse' => $includeTrueFalse,
                    'includeOpen' => $includeOpen,
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la génération du quiz: ' . $e->getMessage());
            return $this->redirectToRoute('ai_quiz_create');
        }
    }
    
    /**
     * Régénère le quiz avec de nouvelles questions (même document, paramètres conservés)
     */
    #[Route('/regenerate', name: 'ai_quiz_regenerate', methods: ['POST'])]
    public function regenerate(Request $request): Response
    {
        $text = $request->request->get('originalText', '');
        if (strlen($text) < 100) {
            $this->addFlash('error', 'Texte source manquant. Veuillez regénérer à partir de la page de création.');
            return $this->redirectToRoute('ai_quiz_create');
        }

        $title = $request->request->get('title', 'Quiz généré par IA');
        $description = $request->request->get('description', 'Quiz créé automatiquement');
        $questionCount = (int) $request->request->get('questionCount', 10);
        $difficulty = $request->request->get('difficulty', 'medium');
        $duration = (int) $request->request->get('duration', 30);
        $includeQCM = $request->request->get('includeQCM', 'on') === 'on';
        $includeTrueFalse = $request->request->get('includeTrueFalse', 'on') === 'on';
        $includeOpen = $request->request->get('includeOpen', 'off') === 'on';

        if ($questionCount < 5 || $questionCount > 50) {
            $questionCount = 10;
        }

        try {
            $quizData = $this->quizGenerator->generateQuizFromText($text, [
                'title' => $title,
                'description' => $description,
                'questionCount' => $questionCount,
                'difficulty' => $difficulty,
                'duration' => $duration,
                'includeQCM' => $includeQCM,
                'includeTrueFalse' => $includeTrueFalse,
                'includeOpen' => $includeOpen
            ]);

            return $this->render('front/pages/quiz/ai_preview.html.twig', [
                'quizData' => $quizData,
                'originalText' => $text,
                'regenerateParams' => [
                    'title' => $title,
                    'description' => $description,
                    'questionCount' => $questionCount,
                    'difficulty' => $difficulty,
                    'duration' => $duration,
                    'includeQCM' => $includeQCM,
                    'includeTrueFalse' => $includeTrueFalse,
                    'includeOpen' => $includeOpen,
                ]
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la régénération : ' . $e->getMessage());
            return $this->redirectToRoute('ai_quiz_create');
        }
    }

    /**
     * Sauvegarde le quiz généré
     */
    #[Route('/save', name: 'ai_quiz_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $quizDataJson = $request->request->get('quizData');
        
        if (empty($quizDataJson)) {
            $this->addFlash('error', 'Données du quiz manquantes.');
            return $this->redirectToRoute('ai_quiz_create');
        }
        
        try {
            $quizData = json_decode($quizDataJson, true);
            
            $quiz = $this->quizGenerator->saveGeneratedQuiz($quizData);
            
            $this->addFlash('success', 'Quiz créé avec succès! Il contient ' . count($quizData['questions']) . ' questions.');
            
            return $this->redirectToRoute('quiz_index');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
            return $this->redirectToRoute('ai_quiz_create');
        }
    }
}
