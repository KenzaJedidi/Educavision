<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Service Mock pour génération automatique de Questions de Quiz
 * Crée des QCM réalistes sans appeler une API externe
 */
class QuizAIMockService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Génère des questions basées sur le contenu d'un chapitre
     */
    public function generateQuestions(string $chapterTitle, string $chapterContent, int $numberOfQuestions = 5): array
    {
        sleep(1); // Simule le temps de génération
        
        $questions = [];
        
        // Banque de questions pour programmation
        $questionBank = [
            // Programmation basique
            [
                'text' => 'Qu\'est-ce qu\'une variable en programmation?',
                'answers' => [
                    'Un conteneur pour stocker une valeur',
                    'Une fonction mathématique',
                    'Un type de commentaire',
                    'Un opérateur logique'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'une boucle for?',
                'answers' => [
                    'Une structure permettant de répéter un bloc de code',
                    'Une variable temporaire',
                    'Un type de tableau',
                    'Une méthode de classe'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Quel est le rôle d\'une condition if?',
                'answers' => [
                    'Exécuter du code selon une condition',
                    'Déclarer une variable',
                    'Créer une boucle infinie',
                    'Importer une librairie'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'un tableau?',
                'answers' => [
                    'Une structure de données contenant plusieurs valeurs',
                    'Un type de commentaire',
                    'Une variable locale',
                    'Une fonction prédéfinie'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'une fonction?',
                'answers' => [
                    'Un bloc de code réutilisable effectuant une tâche',
                    'Un symbole mathématique',
                    'Un type de classe',
                    'Une boucle infinie'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Quel est le type de données pour un nombre entier?',
                'answers' => [
                    'int',
                    'string',
                    'boolean',
                    'float'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'un pointeur?',
                'answers' => [
                    'Une variable stockant une adresse mémoire',
                    'Un symbole graphique',
                    'Un type de boucle',
                    'Une méthode de classe'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'une classe en programmation orientée objet?',
                'answers' => [
                    'Un modèle pour créer des objets',
                    'Un type de tableau',
                    'Une boucle spéciale',
                    'Un fichier de code'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Quel opérateur est utilisé pour la comparaison d\'égalité?',
                'answers' => [
                    '==',
                    '=',
                    '===',
                    '=>'
                ],
                'correctAnswer' => 0
            ],
            [
                'text' => 'Qu\'est-ce qu\'une exception?',
                'answers' => [
                    'Une erreur ou situation anormale pendant l\'exécution',
                    'Un type de classe',
                    'Une variable globale',
                    'Une méthode abstraite'
                ],
                'correctAnswer' => 0
            ]
        ];

        // Sélectionner les questions demandées
        $numberOfQuestions = min($numberOfQuestions, count($questionBank));
        $selectedQuestions = array_slice(
            $questionBank, 
            0, 
            $numberOfQuestions
        );

        // Shuffler les réponses (sauf la bonne qui doit rester à sa place pour la correction)
        foreach ($selectedQuestions as $idx => $question) {
            $answers = $question['answers'];
            $correctAnswer = $answers[$question['correctAnswer']];
            
            // Mélanger uniquement pour la sérialisation
            $shuffled = $answers;
            shuffle($shuffled);
            
            // Trouver le nouvel index de la bonne réponse
            $newCorrectIndex = array_search($correctAnswer, $shuffled);
            
            $questions[] = [
                'text' => $question['text'],
                'answers' => $shuffled,
                'correctAnswer' => $newCorrectIndex,
                'difficulty' => ['Facile', 'Moyen', 'Difficile'][rand(0, 2)]
            ];
        }

        $this->logger->info('📊 (MOCK) Questions de quiz générées', [
            'chapter' => $chapterTitle,
            'count' => count($questions),
        ]);

        return $questions;
    }

    /**
     * Détecte le niveau de difficulté du quiz basé sur les questions
     */
    public function detectQuizDifficulty(array $questions): string
    {
        sleep(1); // Simule l'analyse IA
        
        if (empty($questions)) {
            return 'Moyen';
        }

        $difficultyCount = [
            'Facile' => 0,
            'Moyen' => 0,
            'Difficile' => 0
        ];

        foreach ($questions as $question) {
            $difficulty = $question['difficulty'] ?? 'Moyen';
            if (isset($difficultyCount[$difficulty])) {
                $difficultyCount[$difficulty]++;
            }
        }

        // Déterminer le niveau global
        if ($difficultyCount['Difficile'] >= count($questions) / 2) {
            $level = 'Difficile';
        } elseif ($difficultyCount['Facile'] >= count($questions) / 2) {
            $level = 'Facile';
        } else {
            $level = 'Moyen';
        }

        $this->logger->info('📊 (MOCK) Niveau de difficulté détecté', [
            'level' => $level,
            'stats' => $difficultyCount,
        ]);

        return $level;
    }

    /**
     * Randomise l'ordre des questions pour une session
     */
    public function randomizeQuestions(array $questions): array
    {
        $shuffled = $questions;
        shuffle($shuffled);
        
        $this->logger->info('📊 (MOCK) Questions randomisées', ['count' => count($shuffled)]);
        
        return $shuffled;
    }

    /**
     * Valide une réponse et retourne le score
     */
    public function validateAnswer(string $question, int $givenAnswerIndex, int $correctAnswerIndex): array
    {
        $isCorrect = $givenAnswerIndex === $correctAnswerIndex;
        
        return [
            'isCorrect' => $isCorrect,
            'score' => $isCorrect ? 100 : 0,
            'feedback' => $isCorrect ? '✅ Bonne réponse!' : '❌ Mauvaise réponse!'
        ];
    }
}
