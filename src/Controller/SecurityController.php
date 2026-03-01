<?php

namespace App\Controller;

use App\MetierAvancee\UserMetierAvancee;
use App\MetierAvancee\FaceId\JwtTokenService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    #[Route('/reinitialiser-mot-de-passe/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token, UtilisateurRepository $utilisateurRepository, \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $utilisateurRepository->findOneBy(['resetToken' => $token]);
        $error = null;
        $success = null;
        if (!$user) {
            $error = 'Lien invalide ou expiré.';
        } elseif ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            if ($password !== $passwordConfirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } else {
                $user->setMotDePasse($passwordHasher->hashPassword($user, $password));
                $user->setResetToken(null);
                $entityManager->persist($user);
                $entityManager->flush();
                $success = 'Votre mot de passe a été réinitialisé.';
            }
        }
        return $this->render('security/reset_password.html.twig', [
            'error' => $error,
            'success' => $success
        ]);
    }

    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password_request')]
    public function forgotPasswordRequest(
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        EmailService $emailService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $success = null;
        $error = null;
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('forgot_password', $request->request->get('_csrf_token'))) {
                $error = 'Jeton de sécurité invalide. Veuillez réessayer.';
                return $this->render('security/forgot_password.html.twig', [
                    'success' => null, 'error' => $error, 'email' => trim((string) $request->request->get('email', ''))
                ]);
            }
            $email = trim((string) $request->request->get('email', ''));
            $user = $utilisateurRepository->findOneBy(['email' => $email]);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
                try {
                    $emailService->sendPasswordResetEmail($user, $token);
                    $success = 'Un email de réinitialisation a été envoyé. Vérifiez votre boîte de réception (et les spams).';
                } catch (\Exception $e) {
                    $logger->error('Erreur envoi email récupération mot de passe', [
                        'email' => $user->getEmail(),
                        'error' => $e->getMessage(),
                    ]);
                    // En dev : afficher l'erreur pour faciliter le debug SMTP
                    if ($_ENV['APP_ENV'] === 'dev') {
                        $error = 'L\'email n\'a pas pu être envoyé. Configurez un SMTP réel (Gmail, etc.) dans .env.local - voir MAILER_SETUP.md. Erreur : ' . $e->getMessage();
                    } else {
                        $success = 'Un email de réinitialisation a été envoyé si votre adresse est connue.';
                    }
                }
            } else {
                $error = 'Aucun compte trouvé avec cette adresse email. Vérifiez votre saisie ou créez un compte.';
            }
        }
        return $this->render('security/forgot_password.html.twig', [
            'success' => $success,
            'error' => $error,
            'email' => trim((string) $request->request->get('email', '')),
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Already logged in? Redirect to the right interface
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            }
            if ($this->isGranted('ROLE_PROF')) {
                return $this->redirectToRoute('teacher_dashboard');
            }
            return $this->redirectToRoute('front_cours_list');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login/face-id', name: 'app_login_face_id', methods: ['POST'])]
    public function loginFaceId(
        Request $request,
        UserMetierAvancee $userMetierAvancee,
        UtilisateurRepository $utilisateurRepository,
        TokenStorageInterface $tokenStorage,
        JwtTokenService $jwtTokenService
    ): Response {
        $email = $request->request->get('email');
        $faceImage = $request->request->get('face_image');

        if (!$email || !$faceImage) {
            return $this->json([
                'success' => false,
                'error' => 'Email et image du visage requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verifier si l'utilisateur existe
        $user = $utilisateurRepository->findOneBy(['email' => $email]);
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Aucun compte trouve avec cet email.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verifier si Face ID est enregistre
        if (!$user->isFaceIdEnrolled()) {
            return $this->json([
                'success' => false,
                'error' => 'Face ID non enregistre pour ce compte. Veuillez d\'abord vous connecter avec email/mot de passe pour enregistrer votre Face ID.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verifier si le compte est actif
        if (!$user->isActif()) {
            return $this->json([
                'success' => false,
                'error' => 'Ce compte est desactive.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Authentifier via Face ID
        $authenticatedUser = $userMetierAvancee->authenticateViaFaceId($email, $faceImage);

        if (!$authenticatedUser) {
            return $this->json([
                'success' => false,
                'error' => 'Authentification Face ID echouee. Le visage ne correspond pas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Creer le token d'authentification session
        $token = new UsernamePasswordToken(
            $authenticatedUser,
            'main',
            $authenticatedUser->getRoles()
        );
        
        // Stocker le token
        $tokenStorage->setToken($token);
        
        // Stocker dans la session
        $request->getSession()->set('_security_main', serialize($token));

        // Generer le JWT token
        $jwtResponse = $jwtTokenService->generateAuthResponse($authenticatedUser);
        
        // Déterminer l'URL de redirection selon le rôle
        $redirectUrl = $this->generateUrl('front_cours_list');
        if (in_array('ROLE_ADMIN', $authenticatedUser->getRoles())) {
            $redirectUrl = $this->generateUrl('admin_dashboard');
        } elseif (in_array('ROLE_PROF', $authenticatedUser->getRoles())) {
            $redirectUrl = $this->generateUrl('teacher_dashboard');
        }

        return $this->json([
            'success' => true,
            'message' => 'Connexion reussie !',
            'redirect' => $redirectUrl,
            'jwt' => $jwtResponse['token'],
            'token_type' => 'Bearer',
            'expires_in' => $jwtResponse['expires_in'],
            'user' => [
                'id' => $authenticatedUser->getId(),
                'name' => $authenticatedUser->getFullName(),
                'email' => $authenticatedUser->getEmail(),
                'role' => $authenticatedUser->getRole(),
                'face_id_enrolled' => $authenticatedUser->isFaceIdEnrolled()
            ]
        ]);
    }

    /**
     * API endpoint for Face ID authentication (stateless JWT only)
     */
    #[Route('/api/login/face-id', name: 'api_login_face_id', methods: ['POST'])]
    public function apiLoginFaceId(
        Request $request,
        UserMetierAvancee $userMetierAvancee,
        UtilisateurRepository $utilisateurRepository,
        JwtTokenService $jwtTokenService
    ): Response {
        // Get JSON data
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? $request->request->get('email');
        $faceImage = $data['face_image'] ?? $request->request->get('face_image');

        if (!$email || !$faceImage) {
            return $this->json([
                'success' => false,
                'error' => 'Email et image du visage requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verifier si l'utilisateur existe
        $user = $utilisateurRepository->findOneBy(['email' => $email]);
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Aucun compte trouve avec cet email.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verifier si Face ID est enregistre
        if (!$user->isFaceIdEnrolled()) {
            return $this->json([
                'success' => false,
                'error' => 'Face ID non enregistre pour ce compte.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verifier si le compte est actif
        if (!$user->isActif()) {
            return $this->json([
                'success' => false,
                'error' => 'Ce compte est desactive.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Authentifier via Face ID
        $authenticatedUser = $userMetierAvancee->authenticateViaFaceId($email, $faceImage);

        if (!$authenticatedUser) {
            return $this->json([
                'success' => false,
                'error' => 'Authentification Face ID echouee.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Generer et retourner le JWT token
        $response = $jwtTokenService->generateAuthResponse($authenticatedUser);
        $response['redirect'] = $jwtTokenService->getRedirectUrl($authenticatedUser);
        
        return $this->json($response);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // ========== Routes Face ID Profil Utilisateur ==========

    #[Route('/profile/face-id', name: 'profile_face_id', methods: ['GET'])]
    public function profileFaceId(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('security/face_id_profile.html.twig', [
            'utilisateur' => $this->getUser(),
        ]);
    }

    #[Route('/profile/face-id/register', name: 'profile_face_id_register', methods: ['POST'])]
    public function profileRegisterFaceId(Request $request, UserMetierAvancee $userMetierAvancee): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $faceImageBase64 = $request->request->get('face_image');

        if (!$faceImageBase64) {
            return $this->json(['success' => false, 'error' => 'Aucune image du visage fournie.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $userMetierAvancee->registerFaceIdForAuthenticatedUser($this->getUser(), $faceImageBase64);

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

    #[Route('/profile/face-id/remove', name: 'profile_face_id_remove', methods: ['POST'])]
    public function profileRemoveFaceId(UserMetierAvancee $userMetierAvancee): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if ($userMetierAvancee->removeFaceIdEnrollment($this->getUser())) {
            $this->addFlash('success', 'Votre enregistrement Face ID a été supprimé.');
            return $this->redirectToRoute('profile_face_id');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression de votre enregistrement Face ID.');
            return $this->redirectToRoute('profile_face_id');
        }
    }

    #[Route('/profile/face-id/status', name: 'profile_face_id_status', methods: ['GET'])]
    public function profileFaceIdStatus(UserMetierAvancee $userMetierAvancee): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $hasFaceId = $userMetierAvancee->canUserUseFaceId($user);
        $enrolledDate = $user->getFaceIdEnrollmentDate();

        return $this->json([
            'enrolled' => $hasFaceId,
            'enrolledDate' => $enrolledDate ? $enrolledDate->format('Y-m-d H:i:s') : null,
        ]);
    }
}
