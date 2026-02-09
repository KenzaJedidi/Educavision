<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: OffreStage::class)]
    #[ORM\JoinColumn(name: 'offre_stage_id', referencedColumnName: 'id', nullable: false)]
    private ?OffreStage $offreStage = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    #[Assert\Email(message: 'Adresse e-mail invalide.')]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner votre numéro de téléphone.')]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez renseigner votre niveau d'étude.")]
    private ?string $niveauEtude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner votre lettre de motivation.')]
    private ?string $lettreMotivation = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'En attente';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $dateCandidature = null;

    public function getId(): ?int { return $this->id; }

    public function getOffreStage(): ?OffreStage { return $this->offreStage; }
    public function setOffreStage(OffreStage $offreStage): self { $this->offreStage = $offreStage; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getNiveauEtude(): ?string { return $this->niveauEtude; }
    public function setNiveauEtude(?string $niveauEtude): self { $this->niveauEtude = $niveauEtude; return $this; }

    public function getCv(): ?string { return $this->cv; }
    public function setCv(?string $cv): self { $this->cv = $cv; return $this; }

    public function getLettreMotivation(): ?string { return $this->lettreMotivation; }
    public function setLettreMotivation(?string $lettreMotivation): self { $this->lettreMotivation = $lettreMotivation; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getDateCandidature(): ?\DateTime { return $this->dateCandidature; }
    public function setDateCandidature(\DateTime $dateCandidature): self { $this->dateCandidature = $dateCandidature; return $this; }
}
