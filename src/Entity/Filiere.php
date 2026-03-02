<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $responsable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    /**
     * @var Collection<int, Metier>
     */
    #[ORM\OneToMany(targetEntity: Metier::class, mappedBy: 'filiere', orphanRemoval: true, fetch: 'LAZY')]
    private Collection $metiers;

    public function __construct()
    {
        $this->metiers = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
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

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(?string $responsable): static
    {
        $this->responsable = $responsable;

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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, Metier>
     */
    public function getMetiers(): Collection
    {
        return $this->metiers;
    }

    public function addMetier(Metier $metier): static
    {
        if (!$this->metiers->contains($metier)) {
            $this->metiers->add($metier);
            $metier->setFiliere($this);
        }

        return $this;
    }

    public function removeMetier(Metier $metier): static
    {
        if ($this->metiers->removeElement($metier)) {
            // set the owning side to null (unless already changed)
            if ($metier->getFiliere() === $this) {
                $metier->setFiliere(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le nombre de métiers
     */
    public function getMetiersCount(): int
    {
        return $this->metiers->count();
    }

    /**
     * Vérifie si la filière a des métiers
     */
    public function hasMetiers(): bool
    {
        return $this->metiers->count() > 0;
    }

    /**
     * Retourne la date de création formatée
     */
    public function getFormattedDate(): string
    {
        return $this->dateCreation->format('d/m/Y');
    }

    /**
     * Vérifie si la filière a une image
     */
    public function hasImage(): bool
    {
        return $this->image !== null && $this->image !== '';
    }

    /**
     * Retourne le chemin de l'image
     */
    public function getImagePath(): ?string
    {
        return $this->image ? '/uploads/filieres/' . $this->image : null;
    }
}
