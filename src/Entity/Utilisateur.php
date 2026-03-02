<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $banUntil = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $banReason = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $resetToken = null;

    public function getBanUntil(): ?\DateTimeInterface
    {
        return $this->banUntil;
    }

    public function setBanUntil(?\DateTimeInterface $banUntil): static
    {
        $this->banUntil = $banUntil;
        return $this;
    }

    public function getBanReason(): ?string
    {
        return $this->banReason;
    }

    public function setBanReason(?string $banReason): static
    {
        $this->banReason = $banReason;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
        return $this;
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 20, options: ['default' => 'etudiant'])]
    #[Assert\Choice(choices: ['etudiant', 'professeur', 'admin'], message: 'Le rôle doit être étudiant, professeur ou admin.')]
    private ?string $role = 'etudiant';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true, options: ['default' => true])]
    private ?bool $actif = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateModification = null;

    // Face ID Fields
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $faceIdToken = null;

    #[ORM\Column(nullable: true, options: ['default' => false])]
    private ?bool $faceIdEnrolled = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $faceIdEnrollmentDate = null;

    // ========== Getters / Setters ==========

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getNom(): ?string
    {
        return $this->nom ?? null;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom ?? null;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email ?? null;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse ?? null;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role ?? null;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone ?? null;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse ?? null;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif ?? null;
    }

    public function setActif(?bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription ?? null;
    }

    public function setDateInscription(\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification ?? null;
    }

    public function setDateModification(\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    // ========== Helper ==========

    public function getFullName(): string
    {
        $prenom = $this->prenom ?? '';
        $nom = $this->nom ?? '';
        return trim($prenom . ' ' . $nom);
    }

    // ========== UserInterface (Symfony Security) ==========

    public function getUserIdentifier(): string
    {
        return (string) ($this->email ?? '');
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->role === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($this->role === 'professeur') {
            $roles[] = 'ROLE_PROF';
        } elseif ($this->role === 'etudiant') {
            $roles[] = 'ROLE_ETUDIANT';
        }

        return array_unique($roles);
    }

    public function getPassword(): ?string
    {
        return $this->motDePasse ?? null;
    }

    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data if needed
    }

    // ========== Face ID Methods ==========

    public function getFaceIdToken(): ?string
    {
        return $this->faceIdToken ?? null;
    }

    public function setFaceIdToken(?string $faceIdToken): static
    {
        $this->faceIdToken = $faceIdToken;
        return $this;
    }

    public function isFaceIdEnrolled(): ?bool
    {
        return $this->faceIdEnrolled ?? null;
    }

    public function setFaceIdEnrolled(?bool $faceIdEnrolled): static
    {
        $this->faceIdEnrolled = $faceIdEnrolled;
        return $this;
    }

    public function getFaceIdEnrollmentDate(): ?\DateTime
    {
        return $this->faceIdEnrollmentDate ?? null;
    }

    public function setFaceIdEnrollmentDate(?\DateTime $faceIdEnrollmentDate): static
    {
        $this->faceIdEnrollmentDate = $faceIdEnrollmentDate;
        return $this;
    }
}
