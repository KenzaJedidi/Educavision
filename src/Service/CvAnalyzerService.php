<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;

/**
 * Service d'analyse de CV au format PDF.
 * Utilise smalot/pdfparser pour extraire le texte du PDF,
 * puis AiRecruitmentService pour analyser les compétences via IA.
 */
class CvAnalyzerService
{
    public function __construct(
        private AiRecruitmentService $aiService,
        private LoggerInterface $logger,
        private string $projectDir
    ) {}

    /**
     * Extraire le texte brut d'un fichier PDF
     */
    public function extractTextFromPdf(string $pdfPath): string
    {
        try {
            // Si le chemin est relatif (commence par /uploads), on le concat au projectDir
            if (str_starts_with($pdfPath, '/uploads')) {
                $pdfPath = $this->projectDir . '/public' . $pdfPath;
            }

            if (!file_exists($pdfPath)) {
                $this->logger->warning("CvAnalyzerService: Fichier PDF introuvable: {$pdfPath}");
                return '';
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            // Nettoyer le texte
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            // Limiter la taille pour l'API (max 3000 caractères)
            if (strlen($text) > 3000) {
                $text = substr($text, 0, 3000) . '...';
            }

            return $text;
        } catch (\Throwable $e) {
            $this->logger->error('CvAnalyzerService: Erreur extraction PDF: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Analyse complète d'un CV : extraction du texte + analyse IA
     * Retourne les compétences détectées et un résumé
     */
    public function analyserCv(string $pdfPath): array
    {
        $texte = $this->extractTextFromPdf($pdfPath);

        if (empty($texte)) {
            return [
                'texte_extrait' => '',
                'competences_techniques' => [],
                'competences_soft' => [],
                'langues' => [],
                'niveau_estime' => 'Non déterminé',
                'resume' => 'Impossible d\'extraire le texte du CV.',
            ];
        }

        $analyse = $this->aiService->analyserCvTexte($texte);
        $analyse['texte_extrait'] = $texte;

        return $analyse;
    }

    /**
     * Calcule un score de matching entre les compétences du CV et celles requises par l'offre
     * Score local (sans appel API) - complémentaire au score IA
     */
    public function calculerMatchingLocal(array $competencesCv, array $competencesOffre): int
    {
        if (empty($competencesOffre)) {
            return 50; // Pas de compétences requises = score neutre
        }

        $found = 0;
        $cv_lower = array_map('mb_strtolower', $competencesCv);

        foreach ($competencesOffre as $comp) {
            $comp_lower = mb_strtolower($comp);
            foreach ($cv_lower as $cv_comp) {
                // Matching partiel : si la compétence du CV contient celle de l'offre ou vice versa
                if (str_contains($cv_comp, $comp_lower) || str_contains($comp_lower, $cv_comp)) {
                    $found++;
                    break;
                }
            }
        }

        return (int) round(($found / count($competencesOffre)) * 100);
    }
}
