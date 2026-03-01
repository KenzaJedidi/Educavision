<?php

namespace App\MetierAvancee;

use App\Entity\Utilisateur;
use App\MetierAvancee\FaceId\FaceIdDto;
use App\MetierAvancee\FaceId\FaceIdService;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service métier avancée pour la gestion des utilisateurs
 * Centralise la logique métier complexe
 */
class UserMetierAvancee
{
    public function __construct(
        private FaceIdService $faceIdService,
        private UtilisateurRepository $utilisateurRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Enregistrer le Face ID pour un utilisateur (première connexion)
     * L'utilisateur doit déjà être authentifié
     */
    public function registerFaceIdForAuthenticatedUser(Utilisateur $user, string $faceImageBase64): FaceIdDto
    {
        if (!$user->getId()) {
            throw new \Exception('L\'utilisateur doit être enregistré en base de données avant d\'enregistrer Face ID.');
        }

        $dto = new FaceIdDto($user->getEmail(), $faceImageBase64);
        $result = $this->faceIdService->enrollFaceForUser($user, $dto);
        
        // Persister les changements si l'enregistrement est réussi
        if ($result->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        
        return $result;
    }

    /**
     * Authentifier un utilisateur via Face ID
     */
    public function authenticateViaFaceId(string $email, string $faceImageBase64): ?Utilisateur
    {
        // Chercher l'utilisateur par email
        $user = $this->faceIdService->getUserByEmail($email);

        if (!$user) {
            return null;
        }

        // Vérifier que l'utilisateur a Face ID enregistré
        if (!$this->faceIdService->userHasFaceIdEnrolled($user)) {
            return null;
        }

        // Vérifier le visage
        $dto = new FaceIdDto($email, $faceImageBase64);
        $result = $this->faceIdService->verifyFaceForUser($user, $dto);

        // Retourner l'utilisateur si authentification réussie
        if ($result->isValid()) {
            return $user;
        }

        return null;
    }

    /**
     * Vérifier si un utilisateur peut utiliser Face ID
     */
    public function canUserUseFaceId(Utilisateur $user): bool
    {
        return $this->faceIdService->userHasFaceIdEnrolled($user);
    }

    /**
     * Supprimer l'enregistrement Face ID d'un utilisateur
     */
    public function removeFaceIdEnrollment(Utilisateur $user): bool
    {
        $result = $this->faceIdService->removeFaceIdEnrollment($user);
        
        // Persister les changements si la suppression est réussie
        if ($result) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        
        return $result;
    }

    /**
     * Lister tous les utilisateurs avec Face ID enregistré
     */
    public function getUsersWithFaceIdEnrolled(): array
    {
        return $this->utilisateurRepository->findBy(['faceIdEnrolled' => true]);
    }

    /**
     * Obtenir les statistiques Face ID
     */
    public function getFaceIdStatistics(): array
    {
        $totalUsers = count($this->utilisateurRepository->findAll());
        $usersWithFaceId = count($this->getUsersWithFaceIdEnrolled());

        return [
            'totalUsers' => $totalUsers,
            'usersWithFaceId' => $usersWithFaceId,
            'enrollmentPercentage' => $totalUsers > 0 ? round(($usersWithFaceId / $totalUsers) * 100, 2) : 0,
        ];
    }
}
