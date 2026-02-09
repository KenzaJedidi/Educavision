<?php
namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idquiz", type: "integer")]
    private ?int $idquiz = null;


    #[ORM\Column(type: "string", length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: "boolean")]
    private bool $visible = false;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime", name: "datecreation")]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $duree = null; // Durée en minutes

    #[ORM\OneToMany(mappedBy: "quiz", targetEntity: Result::class, orphanRemoval: true)]
    private Collection $results;

    #[ORM\OneToMany(mappedBy: "quiz", targetEntity: Question::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $questions;

    public function __construct()
    {
        $this->datecreation = new \DateTime();
        $this->results = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->visible = false;
    }
    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }
        return $this;
    }

    public function getIdquiz(): ?int
    {
        return $this->idquiz;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;
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

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    /**
     * @return Collection<int, Result>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): static
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setQuiz($this);
        }
        return $this;
    }

    public function removeResult(Result $result): static
    {
        if ($this->results->removeElement($result)) {
            if ($result->getQuiz() === $this) {
                $result->setQuiz(null);
            }
        }
        return $this;
    }
}
