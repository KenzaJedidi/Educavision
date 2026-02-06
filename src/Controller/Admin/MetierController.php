<?php

namespace App\Controller\Admin;

use App\Entity\Metier;
use App\Form\MetierType;
use App\Repository\MetierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/metier')]
class MetierController extends AbstractController
{
    public function __construct(
        private MetierRepository $metierRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'admin_metier_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search', '');

        // Recherche simple par titre/nom
        if ($search) {
            $metiers = $this->metierRepository->searchAdvanced($search, '', '');
        } else {
            $metiers = $this->metierRepository->findAll();
        }

        // Tri par défaut
        usort($metiers, fn($a, $b) => strcasecmp($a->getNom(), $b->getNom()));

        return $this->render('admin/metier/index.html.twig', [
            'metiers' => $metiers,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'admin_metier_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $metier = new Metier();
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($metier);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le métier a été créé avec succès !');

            return $this->redirectToRoute('admin_metier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/metier/new.html.twig', [
            'metier' => $metier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_metier_show', methods: ['GET'])]
    public function show(Metier $metier): Response
    {
        return $this->render('admin/metier/show.html.twig', [
            'metier' => $metier,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_metier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Metier $metier): Response
    {
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le métier a été modifié avec succès !');

            return $this->redirectToRoute('admin_metier_show', ['id' => $metier->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/metier/edit.html.twig', [
            'metier' => $metier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_metier_delete', methods: ['POST'])]
    public function delete(Request $request, Metier $metier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$metier->getId(), $request->getPayload()->get('_token'))) {
            $this->entityManager->remove($metier);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le métier a été supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_metier_index', [], Response::HTTP_SEE_OTHER);
    }
}
