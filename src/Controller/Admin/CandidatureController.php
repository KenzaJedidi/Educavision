<?php

namespace App\Controller\Admin;

use App\Entity\Candidature;
use App\Repository\CandidatureRepository;
use App\Service\AiRecruitmentService;
use App\Service\CvAnalyzerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/candidatures')]
class CandidatureController extends AbstractController
{
    #[Route('', name: 'admin_candidature_index', methods: ['GET'])]
    public function index(Request $request, CandidatureRepository $repository, PaginatorInterface $paginator): Response
    {
        $q = trim((string)$request->query->get('q'));
        $filterStatut = $request->query->get('statut', '');
        $filterFavori = $request->query->get('favori', '');

        $qb = $repository->createQueryBuilder('c')
            ->leftJoin('c.offreStage', 'o')->addSelect('o');

        if ($q) {
            $qb->andWhere('c.email LIKE :q OR c.nom LIKE :q OR c.prenom LIKE :q OR o.titre LIKE :q OR o.entreprise LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($filterStatut) {
            $qb->andWhere('c.statut = :statut')
               ->setParameter('statut', $filterStatut);
        }

        if ($filterFavori === '1') {
            $qb->andWhere('c.favori = true');
        }

        $qb->orderBy('c.dateCandidature', 'DESC');

        // Pagination
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            8
        );

        // Statistiques complètes
        $allCandidatures = $repository->findAll();
        $total = count($allCandidatures);
        $accepted = 0; $refused = 0; $pending = 0; $favoris = 0;
        $offerCounts = [];
        $candidaturesParMois = [];
        $scoresIa = [];

        foreach ($allCandidatures as $c) {
            $status = (string) $c->getStatut();
            if ($status === 'Accepté') { $accepted++; }
            elseif ($status === 'Refusé') { $refused++; }
            else { $pending++; }

            if ($c->isFavori()) { $favoris++; }

            $offerTitle = $c->getOffreStage() ? (string) $c->getOffreStage()->getTitre() : '—';
            if (!isset($offerCounts[$offerTitle])) { $offerCounts[$offerTitle] = 0; }
            $offerCounts[$offerTitle]++;

            // Stats par mois
            if ($c->getDateCandidature()) {
                $mois = $c->getDateCandidature()->format('Y-m');
                $candidaturesParMois[$mois] = ($candidaturesParMois[$mois] ?? 0) + 1;
            }

            // Scores IA
            if ($c->getScoreIa() !== null) {
                $scoresIa[] = $c->getScoreIa();
            }
        }

        $acceptanceRate = $total > 0 ? round(($accepted / $total) * 100) : 0;
        $scoreMoyen = !empty($scoresIa) ? round(array_sum($scoresIa) / count($scoresIa)) : null;

        arsort($offerCounts);
        $topOffers = [];
        foreach (array_slice($offerCounts, 0, 3, true) as $title => $count) {
            $topOffers[] = ['title' => $title, 'count' => $count];
        }

        ksort($candidaturesParMois);

        $statusBreakdown = [
            ['label' => 'Accepté', 'value' => $accepted, 'color' => '#2ecc71'],
            ['label' => 'En attente', 'value' => $pending, 'color' => '#f1c40f'],
            ['label' => 'Refusé', 'value' => $refused, 'color' => '#e74c3c'],
        ];

        return $this->render('admin/candidature/index.html.twig', [
            'pagination' => $pagination,
            'candidatures' => $pagination,
            'q' => $q,
            'filter_statut' => $filterStatut,
            'filter_favori' => $filterFavori,
            'total_candidatures' => $total,
            'accepted_candidatures' => $accepted,
            'refused_candidatures' => $refused,
            'pending_candidatures' => $pending,
            'favoris_count' => $favoris,
            'acceptance_rate' => $acceptanceRate,
            'score_moyen_ia' => $scoreMoyen,
            'status_breakdown' => $statusBreakdown,
            'top_offers' => $topOffers,
            'candidatures_par_mois' => $candidaturesParMois,
        ]);
    }

    #[Route('/{id}', name: 'admin_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('admin/candidature/show.html.twig', [
            'c' => $candidature,
        ]);
    }

    #[Route('/{id}/status', name: 'admin_candidature_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Candidature $candidature, EntityManagerInterface $em): Response
    {
        $token = (string) $request->request->get('token');
        if (!$this->isCsrfTokenValid('candidature_status_' . $candidature->getId(), $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
        }

        $status = trim((string) $request->request->get('status'));
        if (!in_array($status, ['En attente', 'Accepté', 'Refusé'], true)) {
            $this->addFlash('error', 'Statut non reconnu.');
            return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
        }

        $candidature->setStatut($status);
        $em->flush();

        $this->addFlash('success', 'Statut mis à jour : ' . $status);
        return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
    }

    /**
     * Analyser le CV + scorer la candidature via IA (Scénario A)
     */
    #[Route('/{id}/analyser-ia', name: 'admin_candidature_analyser_ia', methods: ['POST'])]
    public function analyserIa(
        Candidature $candidature,
        CvAnalyzerService $cvAnalyzer,
        AiRecruitmentService $aiService,
        EntityManagerInterface $em
    ): Response {
        $offre = $candidature->getOffreStage();

        // 1. Analyser le CV si disponible
        $competencesDetectees = [];
        if ($candidature->getCv()) {
            $analyseCv = $cvAnalyzer->analyserCv($candidature->getCv());
            $competencesDetectees = array_merge(
                $analyseCv['competences_techniques'] ?? [],
                $analyseCv['competences_soft'] ?? []
            );
            $candidature->setCompetencesDetectees($analyseCv);
        }

        // 2. Scorer la candidature
        if ($offre) {
            $scoring = $aiService->scoreCandidature($candidature, $offre);
            $candidature->setScoreIa((int) ($scoring['score'] ?? 0));

            // Résumé incluant l'analyse
            $analyse = $scoring['analyse'] ?? '';
            $pointsForts = isset($scoring['points_forts']) ? implode(', ', $scoring['points_forts']) : '';
            $pointsFaibles = isset($scoring['points_faibles']) ? implode(', ', $scoring['points_faibles']) : '';

            $resume = "Score: {$scoring['score']}/100\n";
            $resume .= "Analyse: {$analyse}\n";
            if ($pointsForts) $resume .= "Points forts: {$pointsForts}\n";
            if ($pointsFaibles) $resume .= "Points à améliorer: {$pointsFaibles}";

            $candidature->setResumeIa($resume);

            // 3. Décision automatique selon le score
            $score = $candidature->getScoreIa();
            if ($score >= 70) {
                $candidature->setStatut('Acceptée');
            } elseif ($score < 40) {
                $candidature->setStatut('Refusée');
            } else {
                $candidature->setStatut('En attente'); // Score moyen → révision manuelle
            }
        } else {
            // Juste le résumé IA sans scoring
            $resume = $aiService->genererResumeCandidature($candidature);
            $candidature->setResumeIa($resume);
        }

        $em->flush();

        $scoreVal = $candidature->getScoreIa();
        $statutMsg = $candidature->getStatut();
        $this->addFlash('success', "Analyse IA terminée ! Score: {$scoreVal}/100 → Statut: {$statutMsg}");
        return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
    }

    /**
     * Toggle favori d'une candidature
     */
    #[Route('/{id}/favori', name: 'admin_candidature_toggle_favori', methods: ['POST'])]
    public function toggleFavori(Request $request, Candidature $candidature, EntityManagerInterface $em): Response
    {
        $candidature->setFavori(!$candidature->isFavori());
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['favori' => $candidature->isFavori()]);
        }

        $this->addFlash('success', $candidature->isFavori() ? 'Ajouté aux favoris' : 'Retiré des favoris');
        return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
    }

    /**
     * Noter une candidature (1-5 étoiles) + commentaire admin
     */
    #[Route('/{id}/noter', name: 'admin_candidature_noter', methods: ['POST'])]
    public function noter(Request $request, Candidature $candidature, EntityManagerInterface $em): Response
    {
        $note = (int) $request->request->get('note', 0);
        $commentaire = trim((string) $request->request->get('commentaire', ''));

        if ($note < 1 || $note > 5) {
            $this->addFlash('error', 'La note doit être entre 1 et 5.');
            return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
        }

        $candidature->setNoteAdmin($note);
        if ($commentaire) {
            $candidature->setCommentaireAdmin($commentaire);
        }
        $em->flush();

        $this->addFlash('success', 'Note enregistrée : ' . $note . '/5');
        return $this->redirectToRoute('admin_candidature_show', ['id' => $candidature->getId()]);
    }

    /**
     * Export PDF des statistiques de recrutement (Scénario B)
     */
    #[Route('/export/stats-pdf', name: 'admin_candidature_export_stats_pdf', methods: ['GET'], priority: 10)]
    public function exportStatsPdf(CandidatureRepository $repository): Response
    {
        $candidatures = $repository->findAll();
        $total = count($candidatures);
        $accepted = 0; $refused = 0; $pending = 0;
        $offerCounts = [];

        foreach ($candidatures as $c) {
            $status = (string) $c->getStatut();
            if ($status === 'Accepté') { $accepted++; }
            elseif ($status === 'Refusé') { $refused++; }
            else { $pending++; }

            $offerTitle = $c->getOffreStage() ? (string) $c->getOffreStage()->getTitre() : '—';
            $offerCounts[$offerTitle] = ($offerCounts[$offerTitle] ?? 0) + 1;
        }

        arsort($offerCounts);
        $acceptanceRate = $total > 0 ? round(($accepted / $total) * 100) : 0;

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:Arial,sans-serif;margin:20px;color:#333}
            h1{color:#6a11cb;text-align:center;border-bottom:3px solid #6a11cb;padding-bottom:15px}
            .meta{text-align:center;margin:15px 0;color:#666;font-size:12px}
            .stats-grid{display:flex;justify-content:space-around;margin:20px 0}
            .stat-box{text-align:center;padding:15px;border-radius:10px;color:white;width:22%}
            .bg-total{background:#6a11cb}.bg-accepted{background:#2ecc71}.bg-pending{background:#f1c40f;color:#333}.bg-refused{background:#e74c3c}
            .stat-box h2{font-size:28px;margin:0}.stat-box p{margin:5px 0 0}
            table{width:100%;border-collapse:collapse;margin-top:20px}
            th{background:#6a11cb;color:white;padding:10px;text-align:left}
            td{padding:8px;border-bottom:1px solid #ddd}
            tr:nth-child(even){background:#f9f9f9}
            .footer{text-align:center;margin-top:30px;border-top:1px solid #ddd;padding-top:15px;font-size:10px;color:#999}
        </style></head><body>
        <h1>Rapport de Recrutement - Statistiques</h1>
        <div class="meta"><p>Généré le : ' . date('d/m/Y à H:i:s') . '</p></div>
        <table><tr><td style="text-align:center;padding:20px;background:#6a11cb;color:white;border-radius:5px"><h2>' . $total . '</h2><p>Total</p></td>
        <td style="text-align:center;padding:20px;background:#2ecc71;color:white;border-radius:5px"><h2>' . $accepted . '</h2><p>Acceptées</p></td>
        <td style="text-align:center;padding:20px;background:#f1c40f;color:#333;border-radius:5px"><h2>' . $pending . '</h2><p>En attente</p></td>
        <td style="text-align:center;padding:20px;background:#e74c3c;color:white;border-radius:5px"><h2>' . $refused . '</h2><p>Refusées</p></td></tr></table>
        <p style="text-align:center;font-size:18px;margin:20px 0">Taux d\'acceptation: <strong>' . $acceptanceRate . '%</strong></p>
        <h3>Top Offres par nombre de candidatures</h3>
        <table><thead><tr><th>Offre</th><th>Nombre de candidatures</th></tr></thead><tbody>';

        foreach (array_slice($offerCounts, 0, 10, true) as $title => $count) {
            $html .= '<tr><td>' . htmlspecialchars($title) . '</td><td>' . $count . '</td></tr>';
        }

        $html .= '</tbody></table>
        <h3>Détail des candidatures</h3>
        <table><thead><tr><th>Candidat</th><th>Email</th><th>Offre</th><th>Statut</th><th>Score IA</th><th>Date</th></tr></thead><tbody>';

        foreach ($candidatures as $c) {
            $scoreIa = $c->getScoreIa() !== null ? $c->getScoreIa() . '/100' : '—';
            $html .= '<tr><td>' . htmlspecialchars($c->getNom() . ' ' . $c->getPrenom()) . '</td>'
                . '<td>' . htmlspecialchars($c->getEmail()) . '</td>'
                . '<td>' . htmlspecialchars($c->getOffreStage() ? $c->getOffreStage()->getTitre() : '—') . '</td>'
                . '<td>' . $c->getStatut() . '</td>'
                . '<td>' . $scoreIa . '</td>'
                . '<td>' . ($c->getDateCandidature() ? $c->getDateCandidature()->format('d/m/Y') : '—') . '</td></tr>';
        }

        $html .= '</tbody></table>
        <div class="footer"><p>EducaVision - Rapport de Recrutement IA</p></div></body></html>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="rapport_recrutement_' . date('Y-m-d') . '.pdf"');

        return $response;
    }
}
