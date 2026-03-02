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
    private ?int $idquiz;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true, name: 'chapter_id')]
    private ?Chapter $chapter = null;

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

    #[ORM\Column(length: 50, nullable: true, options: ['default' => 'draft'])]
    private ?string $status = 'draft'; // draft, published

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $difficultyLevel = null; // Facile, Moyen, Difficile

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $timeLimit = 0; // en secondes

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $numberOfQuestions = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $attempts = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\OneToMany(mappedBy: "quiz", targetEntity: Result::class, orphanRemoval: true, fetch: 'LAZY')]
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

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): static
    {
        $this->chapter = $chapter;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, ['draft', 'published'])) {
            throw new \InvalidArgumentException('Invalid status: ' . $status);
        }
        $this->status = $status;
        return $this;
    }

    public function getDifficultyLevel(): ?string
    {
        return $this->difficultyLevel;
    }

    public function setDifficultyLevel(?string $difficultyLevel): static
    {
        if ($difficultyLevel && !in_array($difficultyLevel, ['Facile', 'Moyen', 'Difficile'])) {
            throw new \InvalidArgumentException('Invalid difficulty level: ' . $difficultyLevel);
        }
        $this->difficultyLevel = $difficultyLevel;
        return $this;
    }

    public function getTimeLimit(): int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }

    public function getNumberOfQuestions(): int
    {
        return $this->numberOfQuestions;
    }

    public function setNumberOfQuestions(int $numberOfQuestions): static
    {
        $this->numberOfQuestions = $numberOfQuestions;
        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Vérifie si le quiz est publié
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Retourne le nombre de résultats
     */
    public function getResultsCount(): int
    {
        return $this->results->count();
    }

    /**
     * Retourne le nombre de questions
     */
    public function getQuestionsCount(): int
    {
        return $this->questions->count();
    }

    /**
     * Vérifie si le quiz a une limite de temps
     */
    public function hasTimeLimit(): bool
    {
        return $this->timeLimit > 0;
    }

    /**
     * Retourne la durée formatée
     */
    public function getFormattedDuration(): string
    {
        if ($this->duree === null) {
            return 'Non définie';
        }
        
        $hours = floor($this->duree / 60);
        $minutes = $this->duree % 60;
        
        if ($hours > 0) {
            return $hours . 'h' . ($minutes > 0 ? $minutes . 'min' : '');
        }
        
        return $minutes . 'min';
    }

    /**
     * Incrémente le nombre de tentatives
     */
    public function incrementAttempts(): static
    {
        $this->attempts++;
        $this->updatedAt = new \DateTime();
        return $this;
    }
}
