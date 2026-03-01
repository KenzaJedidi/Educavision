<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Check if account is inactive (disabled by admin)
        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte a été désactivé. Contactez l\'administrateur.'
            );
        }

        // Check if user is banned
        if ($user->getBanUntil() !== null) {
            $now = new \DateTime();
            if ($user->getBanUntil() > $now) {
                $until = $user->getBanUntil()->format('d/m/Y à H:i');
                $reason = $user->getBanReason() ? ' Raison : ' . $user->getBanReason() : '';
                throw new CustomUserMessageAccountStatusException(
                    "Votre compte est suspendu jusqu'au {$until}.{$reason}"
                );
            } else {
                // Ban expired — it will be cleaned up automatically, but we still allow login
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Nothing needed post-auth
    }
}
