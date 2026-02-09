<?php

namespace App\Controller\Admin;

use App\Entity\OffreStage;
use App\Form\OffreStagEType;
use App\Repository\OffreStagERepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/offre-stage')]
class OffreStagEController extends AbstractController
{
    #[Route('/', name: 'admin_offre_stage_index', methods: ['GET'])]
    public function index(Request $request, OffreStagERepository $offreStagERepository): Response
    {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $offres_stage = $offreStagERepository->searchByTitre($search);
        } else {
            $offres_stage = $offreStagERepository->findAllOrderedByDate();
        }

        return $this->render('admin/offre_stage/index.html.twig', [
            'offres_stage' => $offres_stage,
            'search' => $search,
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
