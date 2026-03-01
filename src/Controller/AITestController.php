<?php

namespace App\Controller;

use App\Service\AI\QuizAIService;
use App\Service\AI\OpenAnswerCorrectorService;
use App\Service\AI\QuestionGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ai-test', name: 'ai_test_')]
class AITestController extends AbstractController
{
    public function __construct(
        private QuizAIService $aiService,
        private OpenAnswerCorrectorService $corrector,
        private QuestionGeneratorService $generator
    ) {}
    
    /**
     * Page principale de test IA
     */
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('ai_test/index.html.twig');
    }
    
    /**
     * Test de détection de niveau
     */
    #[Route('/test-level', name: 'level')]
    public function testLevel(Request $request): Response
    {
        $results = [];
        
        if ($request->isMethod('POST')) {
            $scores = [
                (float) $request->request->get('score1', 0),
                (float) $request->request->get('score2', 0),
                (float) $request->request->get('score3', 0),
            ];
            
            $averageScore = array_sum($scores) / count($scores);
            
            // Simulation de détection de niveau
            if ($averageScore >= 80) {
                $level = 'expert';
            } elseif ($averageScore >= 60) {
                $level = 'intermediate';
            } elseif ($averageScore >= 40) {
                $level = 'beginner';
            } else {
                $level = 'novice';
            }
            
            $results = [
                'scores' => $scores,
                'average' => $averageScore,
                'level' => $level,
                'recommendations' => $this->aiService->adaptDifficulty($averageScore)
            ];
        }
        
        return $this->render('ai_test/level.html.twig', [
            'results' => $results
        ]);
    }
    
    /**
     * Test de correction de réponse ouverte
     */
    #[Route('/test-correction', name: 'correction')]
    public function testCorrection(Request $request): Response
    {
        $result = null;
        
        if ($request->isMethod('POST')) {
            $userAnswer = $request->request->get('user_answer', '');
            $expectedAnswer = $request->request->get('expected_answer', '');
            $keywordsStr = $request->request->get('keywords', '');
            
            $keywords = array_filter(array_map('trim', explode(',', $keywordsStr)));
            
            $result = $this->corrector->correctOpenAnswer(
                $userAnswer,
                $expectedAnswer,
                $keywords
            );
            
            // Analyse de structure
            $structure = $this->corrector->analyzeAnswerStructure($userAnswer);
            $result['structure'] = $structure;
            
            // Suggestions d'amélioration
            $missingKeywords = array_diff($keywords, $this->extractFoundKeywords($userAnswer, $keywords));
            $result['suggestions'] = $this->corrector->suggestImprovements($userAnswer, $missingKeywords);
        }
        
        return $this->render('ai_test/correction.html.twig', [
            'result' => $result
        ]);
    }
    
    /**
     * Test de génération de questions
     */
    #[Route('/test-generation', name: 'generation')]
    public function testGeneration(Request $request): Response
    {
        $generatedQuiz = null;
        
        if ($request->isMethod('POST')) {
            $topic = $request->request->get('topic', 'Intelligence Artificielle');
            $level = $request->request->get('level', 'intermediate');
            $questionCount = (int) $request->request->get('question_count', 5);
            
            $generatedQuiz = $this->generator->generateAdaptiveQuiz($level, $topic, $questionCount);
        }
        
        return $this->render('ai_test/generation.html.twig', [
            'quiz' => $generatedQuiz
        ]);
    }
    
    /**
     * Test d'adaptation de difficulté
     */
    #[Route('/test-adaptation', name: 'adaptation')]
    public function testAdaptation(Request $request): Response
    {
        $adaptations = [];
        
        if ($request->isMethod('POST')) {
            $score = (float) $request->request->get('score', 50);
            
            $adaptations = [
                'score' => $score,
                'recommendations' => $this->aiService->adaptDifficulty($score)
            ];
        }
        
        return $this->render('ai_test/adaptation.html.twig', [
            'adaptations' => $adaptations
        ]);
    }
    
    /**
     * Démonstration complète
     */
    #[Route('/demo', name: 'demo')]
    public function demo(): Response
    {
        // Exemples de tests
        $examples = [
            'level_detection' => [
                'scores' => [85, 90, 88],
                'detected_level' => 'expert'
            ],
            'correction' => $this->corrector->correctOpenAnswer(
                "L'IA permet aux machines d'apprendre et de s'adapter automatiquement",
                "L'intelligence artificielle permet aux machines d'apprendre et de s'adapter",
                ['intelligence artificielle', 'machines', 'apprendre', 'adapter']
            ),
            'generation' => $this->generator->generateQuestion('Machine Learning', 'medium'),
            'adaptation' => $this->aiService->adaptDifficulty(75)
        ];
        
        return $this->render('ai_test/demo.html.twig', [
            'examples' => $examples
        ]);
    }
    
    private function extractFoundKeywords(string $text, array $keywords): array
    {
        $found = [];
        $text = strtolower($text);
        
        foreach ($keywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $found[] = $keyword;
            }
        }
        
        return $found;
    }
}
