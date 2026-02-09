<?php

namespace App\Controller\Admin;

use App\Entity\Candidature;
use App\Repository\CandidatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/candidatures')]
class CandidatureController extends AbstractController
{
    #[Route('', name: 'admin_candidature_index', methods: ['GET'])]
    public function index(Request $request, CandidatureRepository $repository): Response
    {
        $q = trim((string)$request->query->get('q'));
        if ($q) {
            $candidatures = $repository->createQueryBuilder('c')
                ->leftJoin('c.offreStage', 'o')->addSelect('o')
                ->andWhere('c.email LIKE :q OR c.nom LIKE :q OR c.prenom LIKE :q OR o.titre LIKE :q OR o.entreprise LIKE :q')
                ->setParameter('q', '%' . $q . '%')
                ->orderBy('c.dateCandidature', 'DESC')
                ->getQuery()->getResult();
        } else {
            $candidatures = $repository->createQueryBuilder('c')
                ->leftJoin('c.offreStage', 'o')->addSelect('o')
                ->orderBy('c.dateCandidature', 'DESC')
                ->getQuery()->getResult();
        }

        // Compute simple statistics for a compact, creative dashboard
        $total = \count($candidatures);
        $accepted = 0; $refused = 0; $pending = 0;
        $offerCounts = [];
        foreach ($candidatures as $c) {
            $status = (string) $c->getStatut();
            if ($status === 'Accepté') { $accepted++; }
            elseif ($status === 'Refusé') { $refused++; }
            else { $pending++; }

            $offerTitle = $c->getOffreStage() ? (string) $c->getOffreStage()->getTitre() : '—';
            if (!isset($offerCounts[$offerTitle])) { $offerCounts[$offerTitle] = 0; }
            $offerCounts[$offerTitle]++;
        }
        $acceptanceRate = $total > 0 ? round(($accepted / $total) * 100) : 0;
        // Top 3 offers by number of candidatures
        arsort($offerCounts);
        $topOffers = [];
        foreach (array_slice($offerCounts, 0, 3, true) as $title => $count) {
            $topOffers[] = ['title' => $title, 'count' => $count];
        }

        $statusBreakdown = [
            ['label' => 'Accepté', 'value' => $accepted, 'color' => '#2ecc71'],
            ['label' => 'En attente', 'value' => $pending, 'color' => '#f1c40f'],
            ['label' => 'Refusé', 'value' => $refused, 'color' => '#e74c3c'],
        ];

        return $this->render('admin/candidature/index.html.twig', [
            'candidatures' => $candidatures,
            'q' => $q,
            'total_candidatures' => $total,
            'accepted_candidatures' => $accepted,
            'refused_candidatures' => $refused,
            'pending_candidatures' => $pending,
            'acceptance_rate' => $acceptanceRate,
            'status_breakdown' => $statusBreakdown,
            'top_offers' => $topOffers,
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
}
