<?php
namespace App\Service;

class SentimentAutoService
{
    // Simple sentiment analysis: positive, negative, or neutre
    public function analyze(string $text): string
    {
        $result = $this->analyzeDetailed($text);
        return $result['sentiment'];
    }

    // Returns detailed sentiment analysis with score and confidence
    public function analyzeDetailed(string $text): array
    {
        $positiveWords = ['bien', 'merci', 'satisfait', 'excellent', 'rapide', 'parfait', 'super', 'bon', 'résolu', 'heureux', 'agréable'];
        $negativeWords = ['problème', 'mauvais', 'lent', 'délai', 'erreur', 'mécontent', 'insatisfait', 'nul', 'jamais', 'attente', 'urgent', 'impossible'];
        $textLower = mb_strtolower($text);
        $score = 0;
        foreach ($positiveWords as $word) {
            if (mb_strpos($textLower, $word) !== false) {
                $score += 1;
            }
        }
        foreach ($negativeWords as $word) {
            if (mb_strpos($textLower, $word) !== false) {
                $score -= 1;
            }
        }

        // Strength based on absolute occurrences
        $strength = min(10, max(0, abs($score)));
        $sentiment = 'neutre';
        if ($score > 0) $sentiment = 'positif';
        if ($score < 0) $sentiment = 'négatif';

        // confidence heuristic: more matching words -> higher confidence
        $confidence = $strength / 10.0;

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'confidence' => round($confidence, 2),
        ];
    }
}
