<?php

namespace App\Entity;

use App\Repository\OffreStagERepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreStagERepository::class)]
class OffreStage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le titre doit faire au minimum 3 caractères', maxMessage: 'Le titre ne peut pas dépasser 255 caractères')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(min: 10, minMessage: 'La description doit faire au minimum 10 caractères')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'entreprise est obligatoire')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le nom de l\'entreprise doit faire au minimum 2 caractères', maxMessage: 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères')]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire')]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire')]
    #[Assert\GreaterThan(propertyPath: 'dateDebut', message: 'La date de fin doit être après la date de début')]
    private ?\DateTime $dateFin = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La durée est obligatoire')]
    #[Assert\Positive(message: 'La durée doit être un nombre positif')]
    private ?int $dureeJours = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Ouvert', 'Fermé', 'Pourvu'], message: 'Le statut doit être l\'un des suivants : Ouvert, Fermé, Pourvu')]
    private ?string $statut = 'Ouvert';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $salaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDureeJours(): ?int
    {
        return $this->dureeJours;
    }

    public function setDureeJours(int $dureeJours): static
    {
        $this->dureeJours = $dureeJours;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): static
    {
        $this->salaire = $salaire;

        return $this;
    }
}
