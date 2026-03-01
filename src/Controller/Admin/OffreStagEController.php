<?php

namespace App\Controller\Admin;

use App\Entity\OffreStage;
use App\Form\OffreStagEType;
use App\Repository\OffreStagERepository;
use App\Service\AiRecruitmentService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/offre-stage')]
class OffreStagEController extends AbstractController
{
    #[Route('/', name: 'admin_offre_stage_index', methods: ['GET'])]
    public function index(Request $request, OffreStagERepository $offreStagERepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $query = $offreStagERepository->searchByTitre($search);
        } else {
            $query = $offreStagERepository->findAllOrderedByDate();
        }

        // Pagination avec KnpPaginator
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // 6 éléments par page
        );

        // Statistiques pour les graphiques Chart.js
        $allOffres = $offreStagERepository->findAll();
        $statsParMois = [];
        $statuts = ['Ouvert' => 0, 'Fermé' => 0, 'Pourvu' => 0];
        $entreprises = [];
        $salaires = [];
        
        foreach ($allOffres as $offre) {
            // Stats par mois
            if ($offre->getDateCreation()) {
                $mois = $offre->getDateCreation()->format('Y-m');
                $statsParMois[$mois] = ($statsParMois[$mois] ?? 0) + 1;
            }
            // Stats par statut
            $statuts[$offre->getStatut()] = ($statuts[$offre->getStatut()] ?? 0) + 1;
            // Stats par entreprise
            $ent = $offre->getEntreprise();
            $entreprises[$ent] = ($entreprises[$ent] ?? 0) + 1;
            // Salaires
            if ($offre->getSalaire()) {
                $salaires[] = (float) $offre->getSalaire();
            }
        }

        ksort($statsParMois);
        arsort($entreprises);
        $topEntreprises = array_slice($entreprises, 0, 5, true);
        $salaireMoyen = !empty($salaires) ? round(array_sum($salaires) / count($salaires), 2) : 0;

        return $this->render('admin/offre_stage/index.html.twig', [
            'offres_stage' => is_array($query) ? $query : $allOffres,
            'pagination' => $pagination,
            'search' => $search,
            'stats_par_mois' => $statsParMois,
            'statuts' => $statuts,
            'top_entreprises' => $topEntreprises,
            'salaire_moyen' => $salaireMoyen,
            'total_offres' => count($allOffres),
        ]);
    }

    #[Route('/new', name: 'admin_offre_stage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreStage = new OffreStage();
        $offreStage->setDateCreation(new \DateTime());
        
        $form = $this->createForm(OffreStagEType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offreStage);
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre de stage a été créée avec succès !');

            return $this->redirectToRoute('admin_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/offre_stage/new.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
        ]);
    }

    #[Route('/export-csv', name: 'admin_offre_stage_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, OffreStagERepository $offreStagERepository): Response
    {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $offres_stage = $offreStagERepository->searchByTitre($search);
        } else {
            $offres_stage = $offreStagERepository->findAllOrderedByDate();
        }

        // Créer le CSV avec séparateur point-virgule (format français)
        // Ajouter BOM UTF-8 pour Excel
        $csv = "\xEF\xBB\xBF"; // BOM UTF-8
        $csv .= "Titre;Entreprise;Lieu;Date Début;Date Fin;Durée (j);Salaire (DT);Statut\n";
        
        foreach ($offres_stage as $offre) {
            // Fonction pour échapper les valeurs CSV
            $titre = str_replace('"', '""', $offre->getTitre());
            $entreprise = str_replace('"', '""', $offre->getEntreprise());
            $lieu = str_replace('"', '""', $offre->getLieu() ?? '-');
            $dateDebut = $offre->getDateDebut() ? $offre->getDateDebut()->format('d/m/Y') : '-';
            $dateFin = $offre->getDateFin() ? $offre->getDateFin()->format('d/m/Y') : '-';
            $duree = $offre->getDureeJours();
            $salaire = $offre->getSalaire() ? number_format($offre->getSalaire(), 2, ',', ' ') . ' DT' : '-';
            $statut = $offre->getStatut();
            
            // Construire la ligne CSV avec guillemets autour de chaque champ
            $csv .= '"' . $titre . '";';
            $csv .= '"' . $entreprise . '";';
            $csv .= '"' . $lieu . '";';
            $csv .= '"' . $dateDebut . '";';
            $csv .= '"' . $dateFin . '";';
            $csv .= '"' . $duree . '";';
            $csv .= '"' . $salaire . '";';
            $csv .= '"' . $statut . "\"\n";
        }

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="offres_stage_' . date('Y-m-d_H-i-s') . '.csv"');

        return $response;
    }

    #[Route('/export-pdf', name: 'admin_offre_stage_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, OffreStagERepository $offreStagERepository): Response
    {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $offres_stage = $offreStagERepository->searchByTitre($search);
        } else {
            $offres_stage = $offreStagERepository->findAllOrderedByDate();
        }

        // Créer un HTML formaté
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                h1 { color: #007BFF; text-align: center; border-bottom: 3px solid #007BFF; padding-bottom: 15px; }
                .meta { text-align: center; margin: 15px 0; color: #666; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background-color: #007BFF; color: white; padding: 12px; text-align: left; font-weight: bold; }
                td { padding: 10px; border-bottom: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .status { padding: 6px 12px; border-radius: 4px; color: white; font-weight: bold; font-size: 12px; }
                .status-ouvert { background-color: #28a745; }
                .status-ferme { background-color: #dc3545; }
                .status-pourvu { background-color: #ffc107; color: #333; }
                .footer { text-align: center; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; font-size: 10px; color: #999; }
            </style>
        </head>
        <body>
            <h1>📋 Rapport des Offres de Stage</h1>
            <div class="meta">
                <p><strong>Généré le :</strong> ' . (new \DateTime())->format('d/m/Y à H:i:s') . '</p>
                <p><strong>Total :</strong> ' . count($offres_stage) . ' offre(s)</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Entreprise</th>
                        <th>Lieu</th>
                        <th>Date Début</th>
                        <th>Durée (j)</th>
                        <th>Salaire (DT)</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($offres_stage as $offre) {
            $statusClass = 'status-ouvert';
            if ($offre->getStatut() === 'Fermé') {
                $statusClass = 'status-ferme';
            } elseif ($offre->getStatut() === 'Pourvu') {
                $statusClass = 'status-pourvu';
            }
            
            $html .= '<tr>
                        <td><strong>' . htmlspecialchars($offre->getTitre()) . '</strong></td>
                        <td>' . htmlspecialchars($offre->getEntreprise()) . '</td>
                        <td>' . htmlspecialchars($offre->getLieu() ?? '-') . '</td>
                        <td>' . ($offre->getDateDebut() ? $offre->getDateDebut()->format('d/m/Y') : '-') . '</td>
                        <td>' . $offre->getDureeJours() . '</td>
                        <td>' . ($offre->getSalaire() ? number_format($offre->getSalaire(), 2, ',', ' ') . ' DT' : '-') . '</td>
                        <td><span class="status ' . $statusClass . '">' . $offre->getStatut() . '</span></td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            <div class="footer">
                <p>EducaVision - Système de Gestion des Offres de Stage</p>
            </div>
        </body>
        </html>';

        // Générer le PDF avec Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner le PDF en téléchargement
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="offres_stage_' . date('Y-m-d_H-i-s') . '.pdf"');

        return $response;
    }

    /**
     * API AJAX : Générer une description d'offre via IA (Scénario C)
     * IMPORTANT : cette route doit être AVANT les routes /{id} pour éviter le conflit
     */
    #[Route('/api/generer-description', name: 'admin_offre_stage_generer_description', methods: ['POST'])]
    public function genererDescription(Request $request, AiRecruitmentService $aiService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $titre = $data['titre'] ?? '';
        $entreprise = $data['entreprise'] ?? '';
        $motsCles = $data['mots_cles'] ?? '';
        $lieu = $data['lieu'] ?? '';

        if (empty($titre) || empty($entreprise)) {
            return new JsonResponse(['error' => 'Le titre et l\'entreprise sont obligatoires'], 400);
        }

        if (!$aiService->isConfigured()) {
            return new JsonResponse(['error' => 'API IA non configurée. Ajoutez GEMINI_API_KEY dans .env'], 500);
        }

        $description = $aiService->genererDescriptionOffre($titre, $entreprise, $motsCles, $lieu);

        if ($description === 'Description IA non disponible.') {
            $erreur = $aiService->getLastError() ?: 'Erreur inconnue';
            return new JsonResponse(['error' => 'Erreur IA : ' . $erreur], 500);
        }

        return new JsonResponse([
            'success' => true,
            'description' => $description,
        ]);
    }

    #[Route('/{id}', name: 'admin_offre_stage_show', methods: ['GET'])]
    public function show(OffreStage $offreStage): Response
    {
        return $this->render('admin/offre_stage/show.html.twig', [
            'offre_stage' => $offreStage,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_offre_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreStagEType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre de stage a été modifiée avec succès !');

            return $this->redirectToRoute('admin_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/offre_stage/edit.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_offre_stage_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($offreStage);
        $entityManager->flush();

        $this->addFlash('success', 'L\'offre de stage a été supprimée avec succès !');

        return $this->redirectToRoute('admin_offre_stage_index', [], Response::HTTP_SEE_OTHER);
    }
}
