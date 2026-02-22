<?php
namespace App\Service;

class SentimentAnalysisService
{
    /**
     * Analyse le sentiment d'un texte.
     * @param string $text
     * @return string Sentiment détecté (positif, négatif, neutre)
     */
    public function analyze(string $text): string
    {
        // TODO: Intégrer une API NLP (OpenAI, HuggingFace, etc.)
        // Détection simple : positif si "merci", négatif si "problème", neutre sinon
        $text = mb_strtolower($text);
        if (strpos($text, 'merci') !== false) {
            return 'positif';
        }
        if (strpos($text, 'problème') !== false || strpos($text, 'délai') !== false) {
            return 'négatif';
        }
        return 'neutre';
    }
}
