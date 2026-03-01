<?php

namespace App\Service\AI;

/**
 * Service de génération automatique de questions
 * Utilise des templates et de l'IA pour créer des questions variées
 */
class QuestionGeneratorService
{
    private array $questionTemplates = [
        'definition' => [
            "Qu'est-ce que {concept}?",
            "Définissez {concept}.",
            "Expliquez le concept de {concept}."
        ],
        'application' => [
            "Comment utiliseriez-vous {concept} dans {context}?",
            "Donnez un exemple d'application de {concept}.",
            "Dans quel cas utilise-t-on {concept}?"
        ],
        'comparison' => [
            "Quelle est la différence entre {concept1} et {concept2}?",
            "Comparez {concept1} et {concept2}.",
            "Quels sont les avantages de {concept1} par rapport à {concept2}?"
        ],
        'analysis' => [
            "Analysez l'impact de {concept} sur {domain}.",
            "Quels sont les avantages et inconvénients de {concept}?",
            "Évaluez l'importance de {concept} dans {context}."
        ]
    ];
    
    /**
     * Génère une question basée sur un sujet et un niveau
     */
    public function generateQuestion(string $topic, string $difficulty = 'medium'): array
    {
        $type = $this->selectQuestionType($difficulty);
        $template = $this->selectTemplate($type);
        
        return [
            'text' => $this->fillTemplate($template, $topic),
            'type' => $type,
            'difficulty' => $difficulty,
            'topic' => $topic,
            'points' => $this->calculatePoints($difficulty),
            'options' => $this->generateOptions($topic, $type, $difficulty)
        ];
    }
    
    /**
     * Sélectionne le type de question selon la difficulté
     */
    private function selectQuestionType(string $difficulty): string
    {
        $types = [
            'very_easy' => ['definition'],
            'easy' => ['definition', 'application'],
            'medium' => ['definition', 'application', 'comparison'],
            'hard' => ['application', 'comparison', 'analysis'],
            'very_hard' => ['comparison', 'analysis']
        ];
        
        $availableTypes = $types[$difficulty] ?? $types['medium'];
        return $availableTypes[array_rand($availableTypes)];
    }
    
    /**
     * Sélectionne un template aléatoire
     */
    private function selectTemplate(string $type): string
    {
        $templates = $this->questionTemplates[$type] ?? $this->questionTemplates['definition'];
        return $templates[array_rand($templates)];
    }
    
    /**
     * Remplit le template avec le contenu
     */
    private function fillTemplate(string $template, string $topic): string
    {
        // Remplacer les placeholders
        $question = str_replace('{concept}', $topic, $template);
        $question = str_replace('{concept1}', $topic, $question);
        $question = str_replace('{concept2}', 'une alternative', $question);
        $question = str_replace('{context}', 'un projet réel', $question);
        $question = str_replace('{domain}', 'le domaine professionnel', $question);
        
        return $question;
    }
    
    /**
     * Calcule les points selon la difficulté
     */
    private function calculatePoints(string $difficulty): int
    {
        $points = [
            'very_easy' => 1,
            'easy' => 2,
            'medium' => 3,
            'hard' => 5,
            'very_hard' => 8
        ];
        
        return $points[$difficulty] ?? 3;
    }
    
    /**
     * Génère des options de réponse
     */
    private function generateOptions(string $topic, string $type, string $difficulty): array
    {
        // Pour l'instant, retourne des options génériques
        // Dans une vraie implémentation IA, cela utiliserait un modèle NLP
        return [
            ['text' => 'Option A - Réponse correcte', 'isCorrect' => true],
            ['text' => 'Option B - Réponse plausible', 'isCorrect' => false],
            ['text' => 'Option C - Réponse incorrecte', 'isCorrect' => false],
            ['text' => 'Option D - Réponse incorrecte', 'isCorrect' => false]
        ];
    }
    
    /**
     * Génère un quiz complet adapté au niveau
     */
    public function generateAdaptiveQuiz(string $level, string $topic, int $questionCount = 10): array
    {
        $difficulties = $this->getDifficultiesForLevel($level);
        $questions = [];
        
        for ($i = 0; $i < $questionCount; $i++) {
            $difficulty = $difficulties[array_rand($difficulties)];
            $questions[] = $this->generateQuestion($topic, $difficulty);
        }
        
        return [
            'title' => "Quiz adaptatif - $topic (Niveau: $level)",
            'description' => "Quiz généré automatiquement selon votre niveau",
            'level' => $level,
            'topic' => $topic,
            'questions' => $questions,
            'totalPoints' => array_sum(array_column($questions, 'points'))
        ];
    }
    
    /**
     * Retourne les difficultés appropriées pour un niveau
     */
    private function getDifficultiesForLevel(string $level): array
    {
        $levelDifficulties = [
            'novice' => ['very_easy', 'easy'],
            'beginner' => ['easy', 'medium'],
            'intermediate' => ['medium', 'hard'],
            'expert' => ['hard', 'very_hard']
        ];
        
        return $levelDifficulties[$level] ?? ['medium'];
    }
}
