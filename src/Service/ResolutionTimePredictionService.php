<?php
namespace App\Service;

class ResolutionTimePredictionService
{
    /**
     * Prédit le temps de résolution d'une réclamation en heures.
     * @param string $text
     * @return int Temps estimé en heures
     */
    public function predict(string $text): int
    {
        // Improved heuristic:
        // - urgent keywords reduce predicted time (prioritization)
        // - long / complex descriptions increase estimated time
        // - severe keywords (incident, erreur) add extra buffer
        $textLower = mb_strtolower($text);

        $urgentKeywords = ['urgent', 'immédiat', 'asap', 'tout de suite'];
        foreach ($urgentKeywords as $k) {
            if (mb_strpos($textLower, $k) !== false) {
                return 2; // 2 hours for urgent (fast-track)
            }
        }

        $length = mb_strlen($text);
        // base in hours: proportional to length
        $baseHours = (int) max(1, ceil($length / 400) * 24); // each ~400 chars ~ 1 day

        // add buffer for technical keywords
        $extra = 0;
        $severityKeywords = ['incident', 'erreur', 'bug', 'panne', 'échec'];
        foreach ($severityKeywords as $k) {
            if (mb_strpos($textLower, $k) !== false) {
                $extra += 24;
            }
        }

        // slight penalty for negative sentiment words
        $negativeSignal = 0;
        if (mb_strpos($textLower, 'mécontent') !== false || mb_strpos($textLower, 'insatisfait') !== false) {
            $negativeSignal += 12;
        }

        $hours = $baseHours + $extra + $negativeSignal;
        // clamp between 2 hours and 168 hours (1 week)
        $hours = max(2, min(168, $hours));
        return $hours;
    }
}
