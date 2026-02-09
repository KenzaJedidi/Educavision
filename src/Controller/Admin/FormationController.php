<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/admin/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'admin_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('admin/formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();

            $this->addFlash('success', 'La formation a été créée avec succès !');

            return $this->redirectToRoute('admin_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('admin/formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La formation a été modifiée avec succès !');

            return $this->redirectToRoute('admin_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_formation_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($formation);
        $entityManager->flush();

        $this->addFlash('success', 'La formation a été supprimée avec succès !');

        return $this->redirectToRoute('admin_formation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/export/pdf', name: 'admin_formation_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, Formation $formation): Response
    {
        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        
        // Instantiate Dompdf
        $dompdf = new Dompdf($pdfOptions);
        
        // Generate HTML content
        $html = $this->renderView('admin/formation/export_pdf.html.twig', [
            'formation' => $formation,
            'prerequis' => $formation->getPrerequis()
        ]);
        
        // Load HTML
        $dompdf->loadHtml($html);
        
        // Setup paper
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Generate filename
        $filename = 'formation-' . $formation->getId() . '-' . date('Y-m-d') . '.pdf';
        
        // Output PDF
        $dompdf->stream($filename, [
            "Attachment" => true
        ]);
        
        exit;
    }

    #[Route('/{id}/export/csv', name: 'admin_formation_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, Formation $formation): Response
    {
        // Create CSV content
        $csvContent = '';
        
        // BOM for UTF-8 (pour Excel)
        $csvContent = chr(239) . chr(187) . chr(191);
        
        // Header row
        $headers = ['ID', 'Nom', 'Niveau', 'Durée', 'Description', 'Compétences Acquises', 'Prérequis Texte', 'Nombre de prérequis'];
        $csvContent .= '"' . implode('";"', $headers) . '"' . "\r\n";
        
        // Data row
        $row = [
            $formation->getId(),
            $formation->getNom(),
            $formation->getNiveau() ?? 'Non défini',
            $formation->getDuree() ?? 'Non définie',
            str_replace(['"', "\r", "\n"], ['""', ' ', ' '], strip_tags($formation->getDescription() ?? '')),
            str_replace(['"', "\r", "\n"], ['""', ' ', ' '], $formation->getCompetencesAcquises() ?? ''),
            str_replace(['"', "\r", "\n"], ['""', ' ', ' '], $formation->getPrerequisTexte() ?? ''),
            count($formation->getPrerequis())
        ];
        $csvContent .= '"' . implode('";"', $row) . '"' . "\r\n";
        
        // Create response
        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="formation-' . $formation->getId() . '-' . date('Y-m-d') . '.csv"');
        
        return $response;
    }
}