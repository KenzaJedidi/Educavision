<?php

namespace App\Entity;

use App\Repository\LeconRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeconRepository::class)]
#[ORM\Table(name: 'lecons')]
class Lecon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: 'lecons')]
    #[ORM\JoinColumn(name: 'id_cours', referencedColumnName: 'id', nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenu = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $ordre = 0;

    #[ORM\Column(nullable: true)]
    private ?int $dureeMinutes = null;

    #[ORM\Column(length: 20, nullable: true, options: ['default' => 'texte'])]
    private ?string $typeContenu = 'texte';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlVideo = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateModification = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;
        return $this;
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getDureeMinutes(): ?int
    {
        return $this->dureeMinutes;
    }

    public function setDureeMinutes(?int $dureeMinutes): static
    {
        $this->dureeMinutes = $dureeMinutes;
        return $this;
    }

    public function getTypeContenu(): ?string
    {
        return $this->typeContenu;
    }

    public function setTypeContenu(?string $typeContenu): static
    {
        $this->typeContenu = $typeContenu;
        return $this;
    }

    public function getUrlVideo(): ?string
    {
        return $this->urlVideo;
    }

    public function setUrlVideo(?string $urlVideo): static
    {
        $this->urlVideo = $urlVideo;
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

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function __toString(): string
    {
        return $this->titre ?? '';
    }
}
