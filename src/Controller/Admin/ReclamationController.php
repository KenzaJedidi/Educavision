<?php

namespace App\Controller\Admin;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/admin/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $search = $request->query->get('search');
        $reclamations = $search
            ? $reclamationRepository->searchByFields($search)
            : $reclamationRepository->findAll();
        $total = $reclamationRepository->countBy();
        $traite = $reclamationRepository->countByStatus('traiter');
        $encours = $reclamationRepository->countByStatus('en cours de traitement');
        $percentTraite = $total > 0 ? round(($traite / $total) * 100) : 0;
        $percentEncours = $total > 0 ? round(($encours / $total) * 100) : 0;
        $statusBreakdown = [
            ['label' => 'Traité', 'value' => $traite, 'percent' => $percentTraite, 'color' => '#2ecc71'],
            ['label' => 'En cours', 'value' => $encours, 'percent' => $percentEncours, 'color' => '#f1c40f'],
        ];
        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'total_reclamations' => $total,
            'traite_reclamations' => $traite,
            'encours_reclamations' => $encours,
            'status_breakdown' => $statusBreakdown,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setDateReclamation(new \DateTime());
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reclamation);
            $em->flush();
            return $this->redirectToRoute('reclamation_index');
        }

        return $this->render('admin/reclamation/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('admin/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('reclamation_index');
        }

        return $this->render('admin/reclamation/edit.html.twig', [
            'form' => $form,
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}', name: 'reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $em->remove($reclamation);
            $em->flush();
        }
        return $this->redirectToRoute('reclamation_index');
    }

    #[Route('/export/pdf', name: 'reclamation_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $search = $request->query->get('search');
        $reclamations = $search
            ? $reclamationRepository->searchByFields($search)
            : $reclamationRepository->findAll();

        $html = $this->renderView('admin/reclamation/export.pdf.twig', [
            'reclamations' => $reclamations,
            'generatedAt' => new \DateTime(),
            'search' => $search,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'reclamations_'.date('Ymd_His').'.pdf';
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }
}
