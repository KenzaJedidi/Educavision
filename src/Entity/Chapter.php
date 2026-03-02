<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $ordre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $teacher_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $teacher_email = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $updated_at = null;

    #[ORM\Column(length: 50, nullable: false, options: ['default' => 'draft'])]
    private string $status = 'draft'; // draft ou published

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $enriched_content = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $difficulty_level = null; // débutant / intermédiaire / avancé

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $translations = null; // {'en': 'translated text', 'es': '...', ...}

    #[ORM\Column(nullable: true)]
    private ?int $position = null; // Pour l'ordre drag & drop

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $structured_outline = null; // Plan structuré généré par IA

    #[ORM\ManyToOne(inversedBy: 'chapters', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: true)]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'chapter', targetEntity: Quiz::class, cascade: ['remove'])]
    private Collection $quizzes;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->quizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getTeacherName(): ?string
    {
        return $this->teacher_name;
    }

    public function setTeacherName(?string $teacher_name): static
    {
        $this->teacher_name = $teacher_name;

        return $this;
    }

    public function getTeacherEmail(): ?string
    {
        return $this->teacher_email;
    }

    public function setTeacherEmail(?string $teacher_email): static
    {
        $this->teacher_email = $teacher_email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getStatus(): string
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

    public function getEnrichedContent(): ?string
    {
        return $this->enriched_content;
    }

    public function setEnrichedContent(?string $enriched_content): static
    {
        $this->enriched_content = $enriched_content;
        return $this;
    }

    public function getDifficultyLevel(): ?string
    {
        return $this->difficulty_level;
    }

    public function setDifficultyLevel(?string $difficulty_level): static
    {
        if ($difficulty_level && !in_array($difficulty_level, ['débutant', 'intermédiaire', 'avancé'])) {
            throw new \InvalidArgumentException('Invalid difficulty level: ' . $difficulty_level);
        }
        $this->difficulty_level = $difficulty_level;
        return $this;
    }

    public function getTranslations(): ?array
    {
        return $this->translations;
    }

    public function setTranslations(?array $translations): static
    {
        $this->translations = $translations;
        return $this;
    }

    public function getTranslation(string $language): ?string
    {
        return $this->translations[$language] ?? null;
    }

    public function addTranslation(string $language, string $content): static
    {
        if (!$this->translations) {
            $this->translations = [];
        }
        $this->translations[$language] = $content;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getStructuredOutline(): ?string
    {
        return $this->structured_outline;
    }

    public function setStructuredOutline(?string $structured_outline): static
    {
        $this->structured_outline = $structured_outline;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setChapter($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getChapter() === $this) {
                $quiz->setChapter(null);
            }
        }

        return $this;
    }
}
