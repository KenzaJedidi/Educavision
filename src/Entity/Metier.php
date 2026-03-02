<?php

namespace App\Entity;

use App\Repository\MetierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierRepository::class)]
class Metier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'metiers', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Filiere $filiere = null;

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

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;

        return $this;
    }

    /**
     * Vérifie si le métier a une filière
     */
    public function hasFiliere(): bool
    {
        return $this->filiere !== null;
    }

    /**
     * Retourne le nom de la filière
     */
    public function getFiliereNom(): ?string
    {
        return $this->filiere?->getNom();
    }

    /**
     * Vérifie si le métier est complet
     */
    public function isComplete(): bool
    {
        return $this->nom !== null && 
               $this->description !== null && 
               $this->filiere !== null;
    }

    /**
     * Retourne une description courte (100 caractères max)
     */
    public function getShortDescription(): string
    {
        if ($this->description === null) {
            return '';
        }
        
        if (strlen($this->description) <= 100) {
            return $this->description;
        }
        
        return substr($this->description, 0, 97) . '...';
    }

    /**
     * Retourne les mots-clés du métier
     */
    public function getKeywords(): array
    {
        $keywords = [];
        
        if ($this->nom) {
            $keywords[] = strtolower($this->nom);
        }
        
        if ($this->filiere?->getNom()) {
            $keywords[] = strtolower($this->filiere->getNom());
        }
        
        return $keywords;
    }
}
