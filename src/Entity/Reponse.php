<?php

namespace App\Entity;

use App\Repository\ReponseRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Reclamation $reclamation;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu de la réponse est obligatoire.')]
    #[Assert\Length(min: 5, max: 2000, minMessage: 'La réponse doit contenir au moins {{ limit }} caractères.', maxMessage: 'La réponse ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $contenu;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateReponse;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation ?? null;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu ?? null;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse ?? null;
    }

    public function setDateReponse(\DateTimeInterface $dateReponse): self
    {
        $this->dateReponse = $dateReponse;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating;

    public function getRating(): ?int
    {
        return $this->rating ?? null;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}
