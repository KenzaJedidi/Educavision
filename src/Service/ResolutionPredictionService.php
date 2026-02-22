<?php
namespace App\Service;

class ResolutionPredictionService
{
    public function predict(string $text): string
    {
        // TODO: Integrate with ML model or external API
        // Placeholder: estimate based on text length
        $length = strlen($text);
        if ($length < 100) return '1 jour';
        if ($length < 500) return '3 jours';
        return '7 jours';
    }
}
