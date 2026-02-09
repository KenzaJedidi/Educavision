<?php
// src/Controller/Front/FiliereController.php

namespace App\Controller\Front;

use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FiliereController extends AbstractController
{
    #[Route('/filieres', name: 'front_filieres')]
    public function index(FiliereRepository $filiereRepository): Response
    {
        $filieres = $filiereRepository->findAll();
        
        return $this->render('front/pages/filieres.html.twig', [
            'filieres' => $filieres,
        ]);
    }

    #[Route('/filiere/{id}', name: 'front_filiere_show')]
    public function show(int $id, FiliereRepository $filiereRepository): Response
    {
        $filiere = $filiereRepository->find($id);
        
        if (!$filiere) {
            throw $this->createNotFoundException('Filière non trouvée');
        }
        
        // Récupérer toutes les filières pour les suggestions
        $allFilieres = $filiereRepository->findAll();
        
        return $this->render('front/pages/filiere_show.html.twig', [
            'filiere' => $filiere,
            'allFilieres' => $allFilieres, // Pour les suggestions
        ]);
    }
}