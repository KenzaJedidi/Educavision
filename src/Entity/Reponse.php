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
    private $id;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $reclamation;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu de la réponse est obligatoire.')]
    #[Assert\Length(min: 5, max: 2000, minMessage: 'La réponse doit contenir au moins {{ limit }} caractères.', maxMessage: 'La réponse ne doit pas dépasser {{ limit }} caractères.')]
    private $contenu;

    #[ORM\Column(type: 'datetime')]
    private $dateReponse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(\DateTimeInterface $dateReponse): self
    {
        $this->dateReponse = $dateReponse;
        return $this;
    }
}
