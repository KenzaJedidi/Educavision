<?php

namespace App\Controller\Admin;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('admin/reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }

    #[Route('/new/{reclamation}', name: 'reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, Reclamation $reclamation): Response
    {
        $reponse = new Reponse();
        $reponse->setReclamation($reclamation);
        $reponse->setDateReponse(new \DateTime());
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reponse);
            // Met à jour le statut de la réclamation
            $reclamation->setStatus('traiter');
            $em->persist($reclamation);
            $em->flush();
            return $this->redirectToRoute('reponse_index');
        }

        return $this->render('admin/reponse/new.html.twig', [
            'form' => $form,
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}', name: 'reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('admin/reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('reponse_index');
        }

        return $this->render('admin/reponse/edit.html.twig', [
            'form' => $form,
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}', name: 'reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $reclamation = $reponse->getReclamation();
            $em->remove($reponse);
            $em->flush();
            // Vérifie s'il reste des réponses pour cette réclamation
            if ($reclamation && $reclamation->getReponses()->isEmpty()) {
                $reclamation->setStatus('en cours de traitement');
                $em->persist($reclamation);
                $em->flush();
            }
        }
        return $this->redirectToRoute('reponse_index');
    }
}
