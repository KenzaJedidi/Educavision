<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        \App\Repository\OffreStagERepository $offreRepo, 
        \App\Repository\CandidatureRepository $candRepo, 
        \App\Repository\ReclamationRepository $reclRepo, 
        \App\Repository\ReponseRepository $repRepo,
        \App\Repository\FiliereRepository $filiereRepo,
        \App\Repository\FormationRepository $formationRepo
    ): Response
    {
        // Compute dynamic counts for dashboard metrics
        $offresTotal = $offreRepo->count([]);
        $offresOuvertes = $offreRepo->count(['statut' => 'Ouvert']);
        $pctOffresOuvertes = $offresTotal > 0 ? (int) round(($offresOuvertes * 100) / $offresTotal) : 0;

        $candidaturesTotal = $candRepo->count([]);
        $candidaturesEnAttente = $candRepo->count(['statut' => 'En attente']);
        $pctCandidaturesEnAttente = $candidaturesTotal > 0 ? (int) round(($candidaturesEnAttente * 100) / $candidaturesTotal) : 0;

        // Réclamations & Réponses
        $reclamationsTotal = $reclRepo->count([]);
        $reclamationsEnCours = $reclRepo->count(['status' => 'en cours de traitement']);
        $reclamationsTraitees = $reclRepo->count(['status' => 'traiter']);
        $pctReclamationsEnCours = $reclamationsTotal > 0 ? (int) round(($reclamationsEnCours * 100) / $reclamationsTotal) : 0;
        $pctReclamationsTraitees = $reclamationsTotal > 0 ? (int) round(($reclamationsTraitees * 100) / $reclamationsTotal) : 0;

        $reponsesTotal = $repRepo->count([]);

        // Aggregated rating metrics for responses
        $avgRating = 0;
        $ratedCount = 0;
        $percentRated = 0;
        $ratingBuckets = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        // Average rating
        try {
            $avg = $repRepo->createQueryBuilder('r')
                ->select('AVG(r.rating) as avgRating')
                ->where('r.rating IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult();
            $avgRating = $avg !== null ? round((float) $avg, 2) : 0;
        } catch (\Throwable $e) {
            $avgRating = 0;
        }

        // Count rated and buckets
        try {
            $rows = $repRepo->createQueryBuilder('r')
                ->select('r.rating as rating, COUNT(r.id) as cnt')
                ->where('r.rating IS NOT NULL')
                ->groupBy('r.rating')
                ->getQuery()
                ->getResult();
            foreach ($rows as $row) {
                $rating = (int) $row['rating'];
                $cnt = (int) $row['cnt'];
                if (isset($ratingBuckets[$rating])) {
                    $ratingBuckets[$rating] = $cnt;
                }
                $ratedCount += $cnt;
            }
        } catch (\Throwable $e) {
            // ignore, keep defaults
        }

        if ($reponsesTotal > 0) {
            $percentRated = (int) round(($ratedCount * 100) / $reponsesTotal);
        }
        // Nouveaux compteurs pour les formations et filières
        $totalFilieres = $filiereRepo->count([]);
        $totalFormations = $formationRepo->count([]);

        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'stats' => [
                'offres' => [
                    'total' => $offresTotal,
                    'ouvertes' => $offresOuvertes,
                    'pct' => $pctOffresOuvertes,
                ],
                'candidatures' => [
                    'total' => $candidaturesTotal,
                    'enAttente' => $candidaturesEnAttente,
                    'pct' => $pctCandidaturesEnAttente,
                ],
                'reclamations' => [
                    'total' => $reclamationsTotal,
                    'enCours' => $reclamationsEnCours,
                    'pct' => $pctReclamationsEnCours,
                    'traiteesPct' => $pctReclamationsTraitees,
                ],
                'reponses' => [
                    'total' => $reponsesTotal,
                    'pctTraitees' => $pctReclamationsTraitees,
                    'avgRating' => $avgRating,
                    'ratedCount' => $ratedCount,
                    'percentRated' => $percentRated,
                    'ratingBuckets' => $ratingBuckets,
                ],
                // Ajout des nouvelles statistiques
                'filieres' => [
                    'total' => $totalFilieres,
                    'pct' => 100 // Vous pouvez ajuster si vous avez un champ 'actif'
                ],
                'formations' => [
                    'total' => $totalFormations,
                    'pct' => 100 // Vous pouvez ajuster si vous avez un champ 'disponible'
                ],
            ],
        ]);
    }
}