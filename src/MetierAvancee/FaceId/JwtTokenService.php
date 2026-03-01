<?php

namespace App\MetierAvancee\FaceId;

use App\Entity\Utilisateur;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Service to manage JWT tokens for Face ID authentication
 */
class JwtTokenService
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * Generate a JWT token for the authenticated user
     */
    public function generateToken(Utilisateur $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * Generate a token with custom claims
     */
    public function generateTokenWithClaims(Utilisateur $user, array $additionalClaims = []): string
    {
        // Default claims are added automatically by the bundle
        // You can add custom claims here if needed
        return $this->jwtManager->createFromPayload($user, $additionalClaims);
    }

    /**
     * Generate a full response array with token and user info
     */
    public function generateAuthResponse(Utilisateur $user): array
    {
        $token = $this->generateToken($user);
        
        return [
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600, // 1 hour (as configured in lexik_jwt_authentication.yaml)
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getNom() . ' ' . $user->getPrenom(),
                'roles' => $user->getRoles(),
                'face_id_enrolled' => $user->isFaceIdEnrolled(),
            ],
        ];
    }

    /**
     * Generate redirect URL based on user role
     */
    public function getRedirectUrl(Utilisateur $user): string
    {
        $roles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $roles)) {
            return '/admin';
        } elseif (in_array('ROLE_PROF', $roles)) {
            return '/teacher';
        }
        
        return '/cours';
    }
}
