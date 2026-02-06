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
    private ?int $idresult = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: "results")]
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
        return $this->idresult;
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
}
