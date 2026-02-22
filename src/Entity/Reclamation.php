<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-\']+$/u', message: 'Le nom ne doit contenir que des lettres, espaces ou tirets.')]
    private $nom;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-\']+$/u', message: 'Le prénom ne doit contenir que des lettres, espaces ou tirets.')]
    private $prenom;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    private $email;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Le rôle est obligatoire.')]
    #[Assert\Choice(choices: ['etudiant', 'professeur'], message: 'Le rôle doit être étudiant ou professeur.')]
    private $role; // 'etudiant' ou 'professeur'

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\Choice(choices: ['en cours de traitement', 'traiter'], message: 'Statut invalide.')]
    private $status = 'en cours de traitement'; // 'en cours de traitement' ou 'traiter'

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.')]
    private $titre;
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 5, minMessage: 'La description doit contenir au moins {{ limit }} caractères.', max: 2000, maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.')]
    private $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private $resumeAuto;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $category;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $sentimentAuto;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $tempsResolutionAuto;

    #[ORM\Column(type: 'datetime')]
    private $dateReclamation;
    public function getResumeAuto(): ?string
    {
        return $this->resumeAuto;
    }
    public function setResumeAuto(?string $resumeAuto): self
    {
        $this->resumeAuto = $resumeAuto;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getSentimentAuto(): ?string
    {
        return $this->sentimentAuto;
    }
    public function setSentimentAuto(?string $sentimentAuto): self
    {
        $this->sentimentAuto = $sentimentAuto;
        return $this;
    }

    public function getTempsResolutionAuto(): ?int
    {
        return $this->tempsResolutionAuto;
    }
    public function setTempsResolutionAuto(?int $tempsResolutionAuto): self
    {
        $this->tempsResolutionAuto = $tempsResolutionAuto;
        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'reclamation', targetEntity: Reponse::class, orphanRemoval: true)]
    private $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(\DateTimeInterface $dateReclamation): self
    {
        $this->dateReclamation = $dateReclamation;
        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }


    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setReclamation($this);
            $this->setStatus('traiter');
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
            // Si plus aucune réponse, repasser en cours de traitement
            if ($this->reponses->isEmpty()) {
                $this->setStatus('en cours de traitement');
            }
        }
        return $this;
    }
}
