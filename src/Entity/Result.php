<?php
namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idresult", type: "integer")]
    private ?int $idresult;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: "results", fetch: 'LAZY')]
    #[ORM\JoinColumn(name: "idquiz", referencedColumnName: "idquiz", nullable: false, onDelete: "CASCADE")]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $utilisateur = null;

    #[ORM\Column(type: "integer")]
    private ?int $score = null;

    #[ORM\Column(type: "datetime", name: "datepassage")]
    private ?\DateTimeInterface $datepassage = null;

    public function __construct()
    {
        $this->datepassage = new \DateTime();
    }

    public function getIdresult(): ?int
    {
        return $this->idresult ?? null;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(string $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getDatepassage(): ?\DateTimeInterface
    {
        return $this->datepassage;
    }

    public function setDatepassage(\DateTimeInterface $datepassage): static
    {
        $this->datepassage = $datepassage;
        return $this;
    }

    /**
     * Vérifie si le score est réussi (>= 50%)
     */
    public function isSuccessful(): bool
    {
        return $this->score !== null && $this->score >= 50;
    }

    /**
     * Vérifie si le score est excellent (>= 90%)
     */
    public function isExcellent(): bool
    {
        return $this->score !== null && $this->score >= 90;
    }

    /**
     * Retourne le score formaté en pourcentage
     */
    public function getFormattedScore(): string
    {
        return $this->score . '%';
    }

    /**
     * Retourne la date de passage formatée
     */
    public function getFormattedDate(): string
    {
        return $this->datepassage->format('d/m/Y H:i');
    }

    /**
     * Retourne l'évaluation du score
     */
    public function getGrade(): string
    {
        if ($this->score === null) {
            return 'Non noté';
        }

        return match (true) {
            $this->score >= 90 => 'Excellent',
            $this->score >= 80 => 'Très bien',
            $this->score >= 70 => 'Bien',
            $this->score >= 60 => 'Assez bien',
            $this->score >= 50 => 'Passable',
            default => 'Insuffisant'
        };
    }
}
