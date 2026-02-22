<?php
namespace App\Service;

class SentimentService
{
    public function analyze(string $text): string
    {
        // TODO: Integrate with external sentiment analysis API
        // Placeholder logic
        $positiveWords = ['bon', 'excellent', 'satisfait', 'rapide', 'merci'];
        $negativeWords = ['lent', 'mauvais', 'problème', 'mécontent', 'retard'];
        $score = 0;
        foreach ($positiveWords as $word) {
            if (stripos($text, $word) !== false) $score++;
        }
        foreach ($negativeWords as $word) {
            if (stripos($text, $word) !== false) $score--;
        }
        if ($score > 0) return 'positif';
        if ($score < 0) return 'négatif';
        return 'neutre';
    }
}
