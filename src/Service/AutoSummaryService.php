<?php
namespace App\Service;

class AutoSummaryService
{
    /**
     * Résume automatiquement un texte long.
     * @param string $text
     * @return string Résumé généré
     */
    public function summarize(string $text): string
    {
        // TODO: Intégrer une API NLP (OpenAI, HuggingFace, etc.)
        // Pour l'instant, on retourne les 300 premiers caractères
        return mb_substr($text, 0, 300) . (mb_strlen($text) > 300 ? '...' : '');
    }
}
