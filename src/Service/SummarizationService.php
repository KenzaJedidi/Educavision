<?php
namespace App\Service;

class SummarizationService
{
    public function summarize(string $text): string
    {
        // TODO: Integrate with external summarization API (e.g., HuggingFace, OpenAI)
        // For now, return a placeholder
        if (strlen($text) > 200) {
            return substr($text, 0, 200) . '...';
        }
        return $text;
    }
}
