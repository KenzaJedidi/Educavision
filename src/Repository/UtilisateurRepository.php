<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setMotDePasse($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Compte le nombre d'utilisateurs par rôle
     */
    public function countByRole(string $role): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche des utilisateurs par nom, prénom ou email
     */
    public function searchByFields(string $term): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.nom LIKE :term OR u.prenom LIKE :term OR u.email LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs actifs
     */
    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.actif = true')
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs par rôle
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', $role)
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs par email
     */
    public function findByEmail(string $email): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email): bool
    {
        $count = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Retourne les utilisateurs bannis
     */
    public function findBannedUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.banUntil IS NOT NULL')
            ->andWhere('u.banUntil > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('u.banUntil', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs avec Face ID activé
     */
    public function findFaceIdUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.faceIdEnrolled = true')
            ->orderBy('u.faceIdEnrollmentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs récents (derniers X jours)
     */
    public function findRecent(int $days = 30): array
    {
        $date = new \DateTime("-{$days} days");
        
        return $this->createQueryBuilder('u')
            ->where('u.dateInscription >= :date')
            ->setParameter('date', $date)
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les utilisateurs inactifs
     */
    public function findInactiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.actif = false')
            ->orderBy('u.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée avec filtres multiples
     */
    public function searchWithFilters(?string $term = null, ?string $role = null, ?bool $actif = null, ?bool $faceIdEnrolled = null): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere('(u.nom LIKE :term OR u.prenom LIKE :term OR u.email LIKE :term)')
               ->setParameter('term', '%' . $term . '%');
        }

        if ($role) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $role);
        }

        if ($actif !== null) {
            $qb->andWhere('u.actif = :actif')
               ->setParameter('actif', $actif);
        }

        if ($faceIdEnrolled !== null) {
            $qb->andWhere('u.faceIdEnrolled = :faceIdEnrolled')
               ->setParameter('faceIdEnrolled', $faceIdEnrolled);
        }

        return $qb->orderBy('u.dateInscription', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Compte le nombre total d'utilisateurs
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre d'utilisateurs actifs
     */
    public function countActive(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.actif = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre d'utilisateurs bannis
     */
    public function countBanned(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.banUntil IS NOT NULL')
            ->andWhere('u.banUntil > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre d'utilisateurs avec Face ID
     */
    public function countFaceIdUsers(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.faceIdEnrolled = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve un utilisateur par token de réinitialisation
     */
    public function findByResetToken(string $token): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->where('u.resetToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Nettoie les tokens de réinitialisation expirés
     */
    public function cleanExpiredResetTokens(): int
    {
        // Supposons que les tokens expirent après 24h
        $expiryDate = new \DateTime('-24 hours');
        
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.resetToken', 'NULL')
            ->where('u.resetToken IS NOT NULL')
            ->andWhere('u.dateModification < :expiryDate')
            ->setParameter('expiryDate', $expiryDate)
            ->getQuery()
            ->execute();
    }

    /**
     * Désactive les utilisateurs bannis dont la date est expirée
     */
    public function reactivateExpiredBans(): int
    {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.banUntil', 'NULL')
            ->set('u.banReason', 'NULL')
            ->where('u.banUntil IS NOT NULL')
            ->andWhere('u.banUntil <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->execute();
    }
}
