<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $search = $request->query->get('search');
        $role = $request->query->get('role');
        
        if ($search) {
            $utilisateurs = $utilisateurRepository->searchByFields($search);
        } elseif ($role) {
            $utilisateurs = $utilisateurRepository->findBy(['role' => $role], ['dateInscription' => 'DESC']);
        } else {
            $utilisateurs = $utilisateurRepository->findBy([], ['dateInscription' => 'DESC']);
        }

        return $this->render('admin/user/index.html.twig', [
            'utilisateurs' => $utilisateurs,
            'search' => $search,
            'current_role' => $role,
        ]);
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setDateInscription(new \DateTime());
        $utilisateur->setDateModification(new \DateTime());

        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $utilisateur->setMotDePasse(
                $passwordHasher->hashPassword($utilisateur, $plainPassword)
            );

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été créé avec succès !');
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $utilisateur->setMotDePasse(
                    $passwordHasher->hashPassword($utilisateur, $plainPassword)
                );
            }

            $utilisateur->setDateModification(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle', name: 'admin_user_toggle', methods: ['GET'])]
    public function toggleActive(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $utilisateur->setActif(!$utilisateur->isActif());
        $utilisateur->setDateModification(new \DateTime());
        $entityManager->flush();

        $status = $utilisateur->isActif() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Le compte a été {$status} !");
        return $this->redirectToRoute('admin_user_index');
    }
}
