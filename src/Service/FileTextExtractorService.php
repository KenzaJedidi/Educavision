<?php

namespace App\Service;

use Smalot\PdfParser\Parser;

/**
 * Extrait le texte des fichiers uploadés (PDF, TXT) pour la génération de quiz.
 */
class FileTextExtractorService
{
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 Mo
    private const ALLOWED_EXTENSIONS = ['pdf', 'txt'];

    public function __construct()
    {
    }

    /**
     * Extrait le texte d'un fichier uploadé.
     * @return array{text: string, error: string|null}
     */
    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     */
    public function extractFromUploadedFile($file): array
    {
        if (!$file) {
            return ['text' => '', 'error' => 'Veuillez sélectionner un fichier (PDF ou TXT).'];
        }
        if (!$file->isValid()) {
            return ['text' => '', 'error' => 'Erreur lors du téléchargement du fichier.'];
        }

        if ($file->getSize() > self::MAX_SIZE) {
            return ['text' => '', 'error' => 'Le fichier ne doit pas dépasser 10 Mo.'];
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            return ['text' => '', 'error' => 'Format non supporté. Utilisez PDF ou TXT.'];
        }

        try {
            if ($ext === 'pdf') {
                $text = $this->extractFromPdf($file->getPathname());
            } else {
                $text = $this->extractFromTxt($file->getPathname());
            }

            $text = $this->cleanExtractedText($text);

            if (mb_strlen($text) < 100) {
                return ['text' => $text, 'error' => 'Le fichier contient trop peu de texte (minimum 100 caractères).'];
            }

            return ['text' => $text, 'error' => null];
        } catch (\Throwable $e) {
            return ['text' => '', 'error' => 'Erreur lors de la lecture du fichier : ' . $e->getMessage()];
        }
    }

    /**
     * Extrait le texte d'un fichier PDF à partir de son chemin absolu.
     * Utilisé pour résumer le PDF du cours.
     */
    public function extractTextFromPdfPath(string $absolutePath): string
    {
        if (!file_exists($absolutePath)) {
            return '';
        }
        try {
            $text = $this->extractFromPdf($absolutePath);
            return $this->cleanExtractedText($text);
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function extractFromPdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText() ?? '';
    }

    private function extractFromTxt(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            return '';
        }
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        return $content;
    }

    /**
     * Nettoie le texte extrait pour une meilleure analyse par l'IA.
     * Préserve la structure (paragraphes) et corrige les artefacts courants des PDF.
     */
    private function cleanExtractedText(string $text): string
    {
        // Normaliser les fins de ligne
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Réparer les césures PDF (mot- \n mot → mot)
        $text = preg_replace('/-\s*\n\s*/u', '', $text);

        // Collapser les retours à la ligne multiples en double saut (préserve les paragraphes)
        $text = preg_replace('/\n{3,}/u', "\n\n", $text);

        // Remplacer les espaces multiples par un seul, mais préserver \n\n
        $lines = explode("\n", $text);
        $lines = array_map(function (string $line) {
            return trim(preg_replace('/\s+/u', ' ', $line));
        }, $lines);
        $text = implode("\n", array_filter($lines, fn ($l) => $l !== ''));

        return trim($text);
    }
}
