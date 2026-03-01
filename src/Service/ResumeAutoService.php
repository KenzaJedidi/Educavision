<?php
namespace App\Service;

class ResumeAutoService
{
    // Improved extractive summarizer: choose top sentences by word-frequency scoring
    public function summarize(string $text, int $maxWords = 12): string
    {
        $clean = trim(strip_tags($text));
        if ($clean === '') {
            return '';
        }

        // Split into sentences
        $sentences = preg_split('/(?<=[.!?])\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        if (!$sentences) {
            // fallback to simple truncation
            $words = preg_split('/\s+/u', $clean);
            if (count($words) <= $maxWords) return implode(' ', $words);
            return implode(' ', array_slice($words, 0, $maxWords)) . '...';
        }

        // Build word frequency table
        $words = preg_split('/\W+/u', mb_strtolower($clean), -1, PREG_SPLIT_NO_EMPTY);
        $freq = [];
        foreach ($words as $w) {
            if (mb_strlen($w) < 3) continue; // ignore very short words
            $freq[$w] = ($freq[$w] ?? 0) + 1;
        }

        // Score sentences
        $scores = [];
        foreach ($sentences as $i => $s) {
            $sWords = preg_split('/\W+/u', mb_strtolower($s), -1, PREG_SPLIT_NO_EMPTY);
            $score = 0;
            foreach ($sWords as $sw) {
                if (isset($freq[$sw])) $score += $freq[$sw];
            }
            // slight boost for shorter (concise) sentences
            $scores[$i] = $score / max(1, count($sWords));
        }

        // sort sentence indices by score desc
        arsort($scores);
        $selected = [];
        $wordCount = 0;
        foreach ($scores as $idx => $score) {
            $s = trim($sentences[$idx]);
            $sWords = preg_split('/\s+/u', $s, -1, PREG_SPLIT_NO_EMPTY);
            $wc = count($sWords);
            if ($wordCount + $wc > $maxWords && $wordCount > 0) continue;
            $selected[$idx] = $s;
            $wordCount += $wc;
            if ($wordCount >= $maxWords) break;
        }

        // preserve original order
        ksort($selected);
        $summary = implode(' ', $selected);
        $summaryWords = preg_split('/\s+/u', $summary, -1, PREG_SPLIT_NO_EMPTY);

        // If summary equals original or is unexpectedly long, fallback to a condensed truncation
        $cleanWords = preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $totalWords = count($cleanWords);
        if (trim($summary) === trim($clean) || count($summaryWords) >= $totalWords) {
            if ($totalWords <= $maxWords) {
                // For short descriptions, produce a condensed summary (not identical to description)
                $n = (int) floor($totalWords * 0.6);
                $n = max(3, $n); // at least 3 words
                if ($n >= $totalWords) {
                    $n = max(1, $totalWords - 1);
                }
                if ($n <= 0) {
                    return substr($clean, 0, 60) . (mb_strlen($clean) > 60 ? '...' : '');
                }
                return implode(' ', array_slice($cleanWords, 0, $n)) . ( $n < $totalWords ? '...' : '' );
            }
            return implode(' ', array_slice($cleanWords, 0, $maxWords)) . '...';
        }

        if (count($summaryWords) > $maxWords) {
            $summary = implode(' ', array_slice($summaryWords, 0, $maxWords)) . '...';
        }
        return $summary;
    }
}
