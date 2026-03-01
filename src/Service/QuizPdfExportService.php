<?php

namespace App\Service;

use App\Entity\Quiz;
use Dompdf\Dompdf;
use Dompdf\Options;

class QuizPdfExportService
{
    /**
     * Construit les données pour l'export PDF à partir des résultats (quiz professeur)
     */
    public function buildDataFromProfessorResults(Quiz $quiz, array $results, int $score, int $totalPoints, int $totalQuestions): array
    {
        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 1) : 0;

        $pdfResults = [];
        foreach ($results as $r) {
            $userAnswerText = 'Non répondue';
            $correctAnswerText = '';
            foreach ($r['question']->getAnswers() as $a) {
                if ($r['userAnswerId'] && $a->getId() === $r['userAnswerId']) {
                    $userAnswerText = $a->getTexte();
                }
                if ($a->isCorrect()) {
                    $correctAnswerText = $a->getTexte();
                }
            }

            $pdfResults[] = [
                'questionText' => $r['question']->getTexte(),
                'userAnswer' => $userAnswerText,
                'correctAnswer' => $correctAnswerText,
                'isCorrect' => $r['isCorrect'],
                'points' => $r['points'],
                'earnedPoints' => $r['earnedPoints'],
            ];
        }

        return [
            'quizTitle' => $quiz->getTitre(),
            'date' => (new \DateTime())->format('d/m/Y H:i'),
            'score' => $score,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage,
            'correctAnswers' => count(array_filter($results, fn($r) => $r['isCorrect'])),
            'totalQuestions' => $totalQuestions,
            'results' => $pdfResults,
        ];
    }

    /**
     * Construit les données pour l'export PDF à partir des réponses (quiz étudiant)
     */
    public function buildDataFromStudentAnswers(Quiz $quiz, array $answers, int $correctAnswers, int $totalQuestions, int $finalScore): array
    {
        $totalPoints = 100;
        $score = $finalScore;

        $pdfResults = [];
        foreach ($quiz->getQuestions() as $question) {
            $questionId = $question->getId();
            $answerData = $answers[$questionId] ?? ['selectedIndex' => -1, 'isCorrect' => false];
            $selectedIndex = $answerData['selectedIndex'] ?? -1;

            $userAnswerText = 'Non répondue';
            $correctAnswerText = '';
            $answersList = $question->getAnswers()->toArray();
            $points = 10;
            $earnedPoints = ($answerData['isCorrect'] ?? false) ? 10 : 0;

            foreach ($answersList as $idx => $a) {
                if ($a->isCorrect()) {
                    $correctAnswerText = $a->getTexte();
                }
                if ($selectedIndex === $idx) {
                    $userAnswerText = $a->getTexte();
                }
            }

            $pdfResults[] = [
                'questionText' => $question->getTexte(),
                'userAnswer' => $userAnswerText,
                'correctAnswer' => $correctAnswerText,
                'isCorrect' => $answerData['isCorrect'] ?? false,
                'points' => $points,
                'earnedPoints' => $earnedPoints,
            ];
        }

        return [
            'quizTitle' => $quiz->getTitre(),
            'date' => (new \DateTime())->format('d/m/Y H:i'),
            'score' => $score,
            'totalPoints' => $totalPoints,
            'percentage' => $finalScore,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'results' => $pdfResults,
        ];
    }

    public function generateFromSessionData(array $data): string
    {
        $html = $this->buildHtml($data);
        return $this->renderPdf($html);
    }

    private function buildHtml(array $data): string
    {
        $quizTitle = $data['quizTitle'] ?? 'Quiz';
        $date = $data['date'] ?? date('d/m/Y H:i');
        $score = $data['score'] ?? 0;
        $totalPoints = $data['totalPoints'] ?? 0;
        $percentage = $data['percentage'] ?? 0;
        $correctAnswers = $data['correctAnswers'] ?? 0;
        $totalQuestions = $data['totalQuestions'] ?? 0;
        $results = $data['results'] ?? [];

        $resultsHtml = '';
        foreach ($results as $idx => $r) {
            $num = $idx + 1;
            $status = ($r['isCorrect'] ?? false) ? 'Correct' : 'Incorrect';
            $statusColor = ($r['isCorrect'] ?? false) ? '#28a745' : '#dc3545';
            $questionText = htmlspecialchars($r['questionText'] ?? '');
            $userAnswer = htmlspecialchars($r['userAnswer'] ?? 'Non répondue');
            $correctAnswer = htmlspecialchars($r['correctAnswer'] ?? '');
            $points = $r['points'] ?? 0;
            $earnedPoints = $r['earnedPoints'] ?? 0;

            $resultsHtml .= "
            <div style='margin-bottom: 20px; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px;'>
                <div style='display: flex; justify-content: space-between; margin-bottom: 10px;'>
                    <strong>Question {$num}</strong>
                    <span style='color: {$statusColor}; font-weight: bold;'>{$status}</span>
                </div>
                <p style='margin: 5px 0;'><strong>Question:</strong> {$questionText}</p>
                <p style='margin: 5px 0; color: #666;'><strong>Votre réponse:</strong> {$userAnswer}</p>";
            if (!empty($correctAnswer) && ($r['isCorrect'] ?? false) === false) {
                $resultsHtml .= "<p style='margin: 5px 0; color: #28a745;'><strong>Bonne réponse:</strong> {$correctAnswer}</p>";
            }
            $resultsHtml .= "
                <p style='margin: 5px 0; font-size: 12px;'>Points: {$earnedPoints} / {$points}</p>
            </div>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Résultats - {$quizTitle}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 20px; }
        h1 { color: #ff6b35; font-size: 22px; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #ff6b35; }
        .score-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; text-align: center; }
        .score-value { font-size: 36px; font-weight: bold; color: #ff6b35; }
        .stats { display: table; width: 100%; margin-bottom: 25px; }
        .stats-row { display: table-row; }
        .stats-cell { display: table-cell; padding: 10px; }
        h2 { color: #2d3748; font-size: 16px; margin-top: 25px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Résultats du Quiz - EducaVision</h1>
        <h2>{$quizTitle}</h2>
        <p>Date: {$date}</p>
    </div>

    <div class="score-box">
        <p style="margin: 0; font-size: 14px;">Score</p>
        <p class="score-value">{$percentage}%</p>
        <p style="margin: 5px 0 0 0;">{$score} / {$totalPoints} points | {$correctAnswers} / {$totalQuestions} bonnes réponses</p>
    </div>

    <h2>Détail des réponses corrigées</h2>
    {$resultsHtml}
</body>
</html>
HTML;
    }

    private function renderPdf(string $html): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
