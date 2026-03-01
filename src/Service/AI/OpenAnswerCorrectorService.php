<?php

namespace App\Service\AI;

/**
 * Service de correction intelligente des réponses ouvertes
 * Utilise le NLP (Natural Language Processing) pour évaluer les réponses
 */
class OpenAnswerCorrectorService
{
    /**
     * Corrige une réponse ouverte de manière intelligente
     */
    public function correctOpenAnswer(string $userAnswer, string $expectedAnswer, array $keywords = []): array
    {
        $userAnswer = $this->normalizeText($userAnswer);
        $expectedAnswer = $this->normalizeText($expectedAnswer);
        
        // 1. Calcul de similarité textuelle
        $similarityScore = $this->calculateSimilarity($userAnswer, $expectedAnswer);
        
        // 2. Analyse des mots-clés
        $keywordScore = $this->analyzeKeywords($userAnswer, $keywords);
        
        // 3. Analyse sémantique
        $semanticScore = $this->analyzeSemantics($userAnswer, $expectedAnswer);
        
        // 4. Calcul du score final
        $finalScore = ($similarityScore * 0.3) + ($keywordScore * 0.4) + ($semanticScore * 0.3);
        
        // 5. Génération du feedback
        $feedback = $this->generateFeedback($finalScore, $userAnswer, $expectedAnswer, $keywords);
        
        return [
            'score' => round($finalScore * 100, 2),
            'isCorrect' => $finalScore >= 0.7,
            'partiallyCorrect' => $finalScore >= 0.5 && $finalScore < 0.7,
            'feedback' => $feedback,
            'details' => [
                'similarityScore' => round($similarityScore * 100, 2),
                'keywordScore' => round($keywordScore * 100, 2),
                'semanticScore' => round($semanticScore * 100, 2)
            ]
        ];
    }
    
    /**
     * Normalise le texte pour l'analyse
     */
    private function normalizeText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\w\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    /**
     * Calcule la similarité entre deux textes
     */
    private function calculateSimilarity(string $text1, string $text2): float
    {
        // Algorithme de Levenshtein normalisé
        $maxLength = max(strlen($text1), strlen($text2));
        if ($maxLength === 0) {
            return 1.0;
        }
        
        $distance = levenshtein($text1, $text2);
        return 1 - ($distance / $maxLength);
    }
    
    /**
     * Analyse la présence des mots-clés importants
     */
    private function analyzeKeywords(string $userAnswer, array $keywords): float
    {
        if (empty($keywords)) {
            return 0.5; // Score neutre si pas de mots-clés définis
        }
        
        $foundKeywords = 0;
        $totalKeywords = count($keywords);
        
        foreach ($keywords as $keyword) {
            $keyword = $this->normalizeText($keyword);
            if (strpos($userAnswer, $keyword) !== false) {
                $foundKeywords++;
            }
        }
        
        return $foundKeywords / $totalKeywords;
    }
    
    /**
     * Analyse sémantique basique
     */
    private function analyzeSemantics(string $userAnswer, string $expectedAnswer): float
    {
        // Extraction des mots significatifs
        $userWords = $this->extractSignificantWords($userAnswer);
        $expectedWords = $this->extractSignificantWords($expectedAnswer);
        
        if (empty($expectedWords)) {
            return 0.5;
        }
        
        // Calcul du chevauchement
        $commonWords = array_intersect($userWords, $expectedWords);
        $overlap = count($commonWords) / count($expectedWords);
        
        return $overlap;
    }
    
    /**
     * Extrait les mots significatifs (sans mots vides)
     */
    private function extractSignificantWords(string $text): array
    {
        $stopWords = [
            'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'mais',
            'est', 'sont', 'a', 'ont', 'dans', 'sur', 'pour', 'par', 'avec',
            'ce', 'cette', 'ces', 'qui', 'que', 'quoi', 'dont', 'où'
        ];
        
        $words = explode(' ', $text);
        $significantWords = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stopWords)) {
                $significantWords[] = $word;
            }
        }
        
        return array_unique($significantWords);
    }
    
    /**
     * Génère un feedback personnalisé
     */
    private function generateFeedback(float $score, string $userAnswer, string $expectedAnswer, array $keywords): string
    {
        if ($score >= 0.9) {
            return "✅ Excellente réponse! Vous avez parfaitement compris le concept.";
        } elseif ($score >= 0.7) {
            return "✅ Bonne réponse! Votre compréhension est correcte.";
        } elseif ($score >= 0.5) {
            return "⚠️ Réponse partiellement correcte. Vous avez saisi l'idée générale mais certains détails manquent.";
        } elseif ($score >= 0.3) {
            return "❌ Réponse incomplète. Revoyez les concepts clés: " . implode(', ', $keywords);
        } else {
            return "❌ Réponse incorrecte. Réponse attendue: " . $expectedAnswer;
        }
    }
    
    /**
     * Analyse la structure de la réponse
     */
    public function analyzeAnswerStructure(string $answer): array
    {
        $sentences = preg_split('/[.!?]+/', $answer);
        $sentences = array_filter($sentences, fn($s) => strlen(trim($s)) > 0);
        
        $words = str_word_count($answer);
        
        return [
            'sentenceCount' => count($sentences),
            'wordCount' => $words,
            'averageWordsPerSentence' => count($sentences) > 0 ? $words / count($sentences) : 0,
            'hasIntroduction' => count($sentences) >= 3,
            'hasConclusion' => count($sentences) >= 3,
            'isWellStructured' => count($sentences) >= 2 && $words >= 20
        ];
    }
    
    /**
     * Suggère des améliorations pour la réponse
     */
    public function suggestImprovements(string $userAnswer, array $missingKeywords): array
    {
        $suggestions = [];
        
        $structure = $this->analyzeAnswerStructure($userAnswer);
        
        if ($structure['wordCount'] < 20) {
            $suggestions[] = "Développez davantage votre réponse (minimum 20 mots recommandés).";
        }
        
        if (!$structure['isWellStructured']) {
            $suggestions[] = "Structurez mieux votre réponse avec plusieurs phrases.";
        }
        
        if (!empty($missingKeywords)) {
            $suggestions[] = "Pensez à mentionner: " . implode(', ', $missingKeywords);
        }
        
        return $suggestions;
    }
}
