<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $duree = null;

    #[ORM\Column(length: 50)]
    private ?string $niveau = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $prerequisTexte = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $competencesAcquises = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $debouches = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Prerequis>
     */
    #[ORM\OneToMany(targetEntity: Prerequis::class, mappedBy: 'formation', orphanRemoval: true)]
    private Collection $prerequis;

    public function __construct()
    {
        $this->prerequis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getPrerequisTexte(): ?string
    {
        return $this->prerequisTexte;
    }

    public function setPrerequisTexte(?string $prerequisTexte): static
    {
        $this->prerequisTexte = $prerequisTexte;

        return $this;
    }

    public function getCompetencesAcquises(): ?string
    {
        return $this->competencesAcquises;
    }

    public function setCompetencesAcquises(?string $competencesAcquises): static
    {
        $this->competencesAcquises = $competencesAcquises;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Prerequis>
     */
    public function getPrerequis(): Collection
    {
        return $this->prerequis;
    }

    public function addPrerequi(Prerequis $prerequi): static
    {
        if (!$this->prerequis->contains($prerequi)) {
            $this->prerequis->add($prerequi);
            $prerequi->setFormation($this);
        }

        return $this;
    }

    public function removePrerequi(Prerequis $prerequi): static
    {
        if ($this->prerequis->removeElement($prerequi)) {
            // set the owning side to null (unless already changed)
            if ($prerequi->getFormation() === $this) {
                $prerequi->setFormation(null);
            }
        }

        return $this;
    }

    public function getDebouches(): ?string
    {
        return $this->debouches;
    }

    public function setDebouches(?string $debouches): static
    {
        $this->debouches = $debouches;

        return $this;
    }
}
