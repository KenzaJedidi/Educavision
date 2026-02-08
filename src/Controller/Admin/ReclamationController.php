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
        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'total_reclamations' => $total,
            'traite_reclamations' => $traite,
            'encours_reclamations' => $encours,
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
            'form' => $form->createView(),
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
            'form' => $form->createView(),
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
}
