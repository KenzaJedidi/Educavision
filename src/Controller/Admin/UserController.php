<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\MetierAvancee\UserMetierAvancee;


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

    #[Route('/{id}/ban', name: 'admin_user_ban', methods: ['POST'])]
    public function ban(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        if (!$this->isCsrfTokenValid('ban' . $utilisateur->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_user_show', ['id' => $utilisateur->getId()]);
        }

        $durationDays = (int) $request->request->get('duration_days', 7);
        $reason = trim((string) $request->request->get('reason', ''));

        $banUntil = new \DateTime();
        $banUntil->modify("+{$durationDays} days");

        $utilisateur->setBanUntil($banUntil);
        $utilisateur->setBanReason($reason ?: null);
        $utilisateur->setDateModification(new \DateTime());
        $entityManager->flush();

        // Send ban notification email
        try {
            $emailService->sendAccountBannedEmail($utilisateur);
        } catch (\Exception $e) {
            // Email failed silently — ban is still applied
        }

        $this->addFlash('success', "Le compte de {$utilisateur->getFullName()} a été suspendu pour {$durationDays} jour(s).");
        return $this->redirectToRoute('admin_user_show', ['id' => $utilisateur->getId()]);
    }

    #[Route('/{id}/unban', name: 'admin_user_unban', methods: ['GET'])]
    public function unban(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $utilisateur->setBanUntil(null);
        $utilisateur->setBanReason(null);
        $utilisateur->setDateModification(new \DateTime());
        $entityManager->flush();

        $this->addFlash('success', "La suspension du compte de {$utilisateur->getFullName()} a été levée.");
        return $this->redirectToRoute('admin_user_show', ['id' => $utilisateur->getId()]);
    }

    // ========== Routes Face ID ==========

    #[Route('/{id}/face-id', name: 'admin_user_face_id', methods: ['GET'])]
    public function faceid(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/user/face_id_enroll.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/face-id/register', name: 'admin_user_face_id_register', methods: ['POST'])]
    public function registerFaceId(Request $request, Utilisateur $utilisateur, UserMetierAvancee $userMetierAvancee): Response
    {
        $faceImageBase64 = $request->request->get('face_image');

        if (!$faceImageBase64) {
            return $this->json(['success' => false, 'error' => 'Aucune image du visage fournie.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $userMetierAvancee->registerFaceIdForAuthenticatedUser($utilisateur, $faceImageBase64);

            if ($result->isValid()) {
                $this->addFlash('success', 'Votre visage a été enregistré avec succès ! Vous pouvez maintenant vous connecter via Face ID.');
                return $this->json(['success' => true, 'token' => $result->getFaceIdToken()], Response::HTTP_OK);
            } else {
                return $this->json(['success' => false, 'error' => $result->getErrorMessage()], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/face-id/remove', name: 'admin_user_face_id_remove', methods: ['POST'])]
    public function removeFaceId(Utilisateur $utilisateur, UserMetierAvancee $userMetierAvancee): Response
    {
        if ($userMetierAvancee->removeFaceIdEnrollment($utilisateur)) {
            $this->addFlash('success', 'Votre enregistrement Face ID a été supprimé. Vous devrez vous reconnecter via email/mot de passe.');
            return $this->redirectToRoute('admin_user_show', ['id' => $utilisateur->getId()]);
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression de votre enregistrement Face ID.');
            return $this->redirectToRoute('admin_user_show', ['id' => $utilisateur->getId()]);
        }
    }

    #[Route('/{id}/face-id/status', name: 'admin_user_face_id_status', methods: ['GET'])]
    public function getFaceIdStatus(Utilisateur $utilisateur, UserMetierAvancee $userMetierAvancee): Response
    {
        $hasFaceId = $userMetierAvancee->canUserUseFaceId($utilisateur);
        $enrolledDate = $utilisateur->getFaceIdEnrollmentDate();

        return $this->json([
            'enrolled' => $hasFaceId,
            'enrolledDate' => $enrolledDate ? $enrolledDate->format('Y-m-d H:i:s') : null,
        ]);
    }
}
