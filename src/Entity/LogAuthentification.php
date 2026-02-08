<?php

namespace App\Entity;

use App\Repository\LogAuthentificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogAuthentificationRepository::class)]
#[ORM\Table(name: 'logs_authentification')]
class LogAuthentification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $adresseIp = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateConnexion = null;

    #[ORM\Column(length: 10, nullable: true, options: ['default' => 'echec'])]
    private ?string $statut = 'echec';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeAction = null;

    public function __construct()
    {
        $this->dateConnexion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getAdresseIp(): ?string
    {
        return $this->adresseIp;
    }

    public function setAdresseIp(?string $adresseIp): static
    {
        $this->adresseIp = $adresseIp;
        return $this;
    }

    public function getDateConnexion(): ?\DateTime
    {
        return $this->dateConnexion;
    }

    public function setDateConnexion(\DateTime $dateConnexion): static
    {
        $this->dateConnexion = $dateConnexion;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getTypeAction(): ?string
    {
        return $this->typeAction;
    }

    public function setTypeAction(?string $typeAction): static
    {
        $this->typeAction = $typeAction;
        return $this;
    }
}
