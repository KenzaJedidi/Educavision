<?php
// src/Controller/Front/FormationController.php

namespace App\Controller\Front;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'front_formations')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();
        
        return $this->render('front/pages/formations.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/{id}', name: 'front_formation_show')]
    public function show(int $id, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->find($id);
        
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }
        
        // Récupérer toutes les formations pour les suggestions
        $allFormations = $formationRepository->findAll();
        
        return $this->render('front/pages/formation_show.html.twig', [
            'formation' => $formation,
            'allFormations' => $allFormations, // Nouveau: passer toutes les formations
        ]);
    }
}