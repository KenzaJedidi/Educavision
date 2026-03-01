<?php

namespace App\Service\AI;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service d'Intelligence Artificielle pour les Quiz
 * Gère l'adaptation intelligente des quiz selon les performances
 */
class QuizAIService
{
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Détecte automatiquement le niveau de l'étudiant
     * Basé sur l'historique de ses résultats
     */
    public function detectStudentLevel(?Utilisateur $user): string
    {
        if (!$user) {
            return 'beginner'; // Niveau par défaut si pas connecté
        }
        
        // Note: L'entité Result stocke utilisateur comme string (IP ou identifiant)
        // Pour une vraie détection de niveau, il faudrait une relation ManyToOne avec Utilisateur
        // Pour l'instant, on retourne un niveau basé sur un calcul simple
        
        // Récupérer tous les résultats récents (on ne peut pas filtrer par utilisateur facilement)
        $results = $this->entityManager->getRepository(Result::class)
            ->findBy([], ['datepassage' => 'DESC'], 10);
        
        if (empty($results)) {
            return 'beginner'; // Nouveau utilisateur
        }
        
        // Calculer la moyenne des scores (approximation)
        $totalScore = 0;
        $count = 0;
        
        foreach ($results as $result) {
            $quiz = $result->getQuiz();
            $score = $result->getScore();
            
            // Calculer le pourcentage
            $totalPoints = 0;
            foreach ($quiz->getQuestions() as $question) {
                $totalPoints += $question->getPoints() ?: 10;
            }
            
            if ($totalPoints > 0) {
                $percentage = ($score / $totalPoints) * 100;
                $totalScore += $percentage;
                $count++;
            }
        }
        
        if ($count === 0) {
            return 'beginner';
        }
        
        $averageScore = $totalScore / $count;
        
        // Déterminer le niveau selon la moyenne
        if ($averageScore >= 85) {
            return 'expert';
        } elseif ($averageScore >= 70) {
            return 'intermediate';
        } elseif ($averageScore >= 50) {
            return 'beginner';
        } else {
            return 'novice';
        }
    }
    
    /**
     * Adapte la difficulté du prochain quiz selon le score
     */
    public function adaptDifficulty(float $score): array
    {
        $recommendations = [];
        
        if ($score >= 90) {
            $recommendations = [
                'level' => 'advanced',
                'message' => 'Excellent! Vous êtes prêt pour des défis plus complexes.',
                'nextQuizDifficulty' => 'hard',
                'suggestedTopics' => ['Concepts avancés', 'Cas pratiques complexes'],
                'motivationalMessage' => '🎉 Performance exceptionnelle! Continuez sur cette lancée!'
            ];
        } elseif ($score >= 70) {
            $recommendations = [
                'level' => 'intermediate',
                'message' => 'Bon travail! Vous maîtrisez bien les bases.',
                'nextQuizDifficulty' => 'medium',
                'suggestedTopics' => ['Approfondissement', 'Applications pratiques'],
                'motivationalMessage' => '👍 Très bien! Quelques révisions et vous serez au top!'
            ];
        } elseif ($score >= 40) {
            $recommendations = [
                'level' => 'beginner',
                'message' => 'Continuez vos efforts, vous progressez!',
                'nextQuizDifficulty' => 'easy',
                'suggestedTopics' => ['Révision des bases', 'Concepts fondamentaux'],
                'motivationalMessage' => '💪 Ne lâchez rien! La pratique mène à la perfection!'
            ];
        } else {
            $recommendations = [
                'level' => 'novice',
                'message' => 'Prenez le temps de réviser les fondamentaux.',
                'nextQuizDifficulty' => 'very_easy',
                'suggestedTopics' => ['Bases essentielles', 'Tutoriels guidés'],
                'motivationalMessage' => '🌱 Chaque expert a commencé comme débutant. Persévérez!',
                'additionalHelp' => [
                    'Revoir les cours de base',
                    'Pratiquer avec des exercices simples',
                    'Demander de l\'aide à un tuteur'
                ]
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Génère des recommandations personnalisées
     */
    public function generateRecommendations($result): array
    {
        // Pour l'instant, on retourne des recommandations basiques
        // Dans une vraie implémentation, on analyserait les résultats détaillés
        $score = is_numeric($result) ? $result : 50;
        
        return [
            'score' => $score,
            'level' => $this->detectStudentLevel(null),
            'recommendations' => $this->adaptDifficulty($score),
        ];
    }
    
    /**
     * Analyse les points faibles de l'étudiant
     */
    private function analyzeWeakPoints($result): array
    {
        // Implémentation simplifiée
        return [];
    }
    
    /**
     * Suggère les prochaines étapes d'apprentissage
     */
    private function suggestNextSteps(float $score, array $weakPoints): array
    {
        $steps = [];
        
        if ($score < 50) {
            $steps[] = 'Revoir les concepts de base';
            $steps[] = 'Pratiquer avec des quiz plus simples';
            $steps[] = 'Consulter les ressources pédagogiques';
        } elseif ($score < 75) {
            $steps[] = 'Approfondir les sujets mal maîtrisés';
            $steps[] = 'Pratiquer régulièrement';
            $steps[] = 'Tenter des quiz de niveau intermédiaire';
        } else {
            $steps[] = 'Explorer des sujets avancés';
            $steps[] = 'Relever des défis plus complexes';
            $steps[] = 'Partager vos connaissances avec d\'autres';
        }
        
        return $steps;
    }
    
    /**
     * Estime le temps nécessaire pour s'améliorer
     */
    private function estimateImprovementTime(float $score): string
    {
        if ($score >= 80) {
            return 'Vous êtes déjà au niveau expert!';
        } elseif ($score >= 60) {
            return 'Environ 2-3 semaines de pratique régulière';
        } elseif ($score >= 40) {
            return 'Environ 1-2 mois avec une pratique quotidienne';
        } else {
            return 'Environ 2-3 mois avec un apprentissage structuré';
        }
    }
    
    /**
     * Calcule un score de confiance pour une réponse
     * Utile pour les réponses ouvertes
     */
    public function calculateConfidenceScore(string $userAnswer, string $correctAnswer): float
    {
        // Normalisation des textes
        $userAnswer = strtolower(trim($userAnswer));
        $correctAnswer = strtolower(trim($correctAnswer));
        
        // Calcul de similarité (algorithme de Levenshtein)
        $maxLength = max(strlen($userAnswer), strlen($correctAnswer));
        if ($maxLength === 0) {
            return 0.0;
        }
        
        $distance = levenshtein($userAnswer, $correctAnswer);
        $similarity = 1 - ($distance / $maxLength);
        
        // Bonus si les mots-clés importants sont présents
        $keywords = $this->extractKeywords($correctAnswer);
        $keywordBonus = 0;
        
        foreach ($keywords as $keyword) {
            if (strpos($userAnswer, $keyword) !== false) {
                $keywordBonus += 0.1;
            }
        }
        
        return min(1.0, $similarity + $keywordBonus);
    }
    
    /**
     * Extrait les mots-clés d'un texte
     */
    private function extractKeywords(string $text): array
    {
        // Mots vides à ignorer
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'mais'];
        
        $words = explode(' ', strtolower($text));
        $keywords = [];
        
        foreach ($words as $word) {
            $word = trim($word, '.,;:!?');
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        
        return $keywords;
    }
}
