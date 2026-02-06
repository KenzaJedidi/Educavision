<?php

namespace App\Entity;

use App\Repository\MetierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MetierRepository::class)]
class Metier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du métier est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le nom doit faire au minimum 3 caractères', maxMessage: 'Le nom ne peut pas dépasser 255 caractères')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(min: 10, minMessage: 'La description doit faire au minimum 10 caractères')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $salaireeMoyen = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $secteur = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['Bac', 'Bac+2', 'Bac+3', 'Bac+5'], message: 'Le niveau d\'étude doit être l\'un des suivants : Bac, Bac+2, Bac+3, Bac+5')]
    private ?string $niveauEtude = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['Bon', 'Moyen', 'Faible'], message: 'Les perspectives doivent être : Bon, Moyen ou Faible')]
    private ?string $perspectivesEmploi = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'metiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Filiere $filiere = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
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

    public function getSalaireeMoyen(): ?float
    {
        return $this->salaireeMoyen;
    }

    public function setSalaireeMoyen(?float $salaireeMoyen): static
    {
        $this->salaireeMoyen = $salaireeMoyen;
        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(?string $secteur): static
    {
        $this->secteur = $secteur;
        return $this;
    }

    public function getNiveauEtude(): ?string
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(?string $niveauEtude): static
    {
        $this->niveauEtude = $niveauEtude;
        return $this;
    }

    public function getPerspectivesEmploi(): ?string
    {
        return $this->perspectivesEmploi;
    }

    public function setPerspectivesEmploi(?string $perspectivesEmploi): static
    {
        $this->perspectivesEmploi = $perspectivesEmploi;
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

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;
        return $this;
    }
}
