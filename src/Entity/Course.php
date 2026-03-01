<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre du cours est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le titre doit contenir au moins 3 caractères', maxMessage: 'Le titre ne doit pas dépasser 255 caractères')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'La description ne doit pas dépasser 2000 caractères')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdf_file = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Le prix doit être un nombre positif ou nul')]
    private ?string $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: ['Développement', 'Design', 'Marketing', 'Business', 'Science', 'Langues', 'Autre'], message: 'Choisissez une catégorie valide')]
    private ?string $category = null;

    #[ORM\Column]
    #[Assert\Choice(choices: [0, 1], message: 'Le statut doit être 0 ou 1')]
    private ?int $status = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    // Nouveaux champs pour le système de scoring et de recommandation
    #[ORM\Column(options: ['default' => 0])]
    private int $views = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $likes = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $comments_count = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?string $popularity_score = '0';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $wikipedia_summary = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $last_accessed = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $keywords = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $teacher = null;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\OneToMany(targetEntity: Chapter::class, mappedBy: 'course', orphanRemoval: true)]
    private Collection $chapters;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->views = 0;
        $this->likes = 0;
        $this->comments_count = 0;
        $this->popularity_score = '0';
    }

    public function getTeacher(): ?Utilisateur
    {
        return $this->teacher;
    }

    public function setTeacher(?Utilisateur $teacher): static
    {
        $this->teacher = $teacher;
        return $this;
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

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getPdfFile(): ?string
    {
        return $this->pdf_file;
    }

    public function setPdfFile(?string $pdf_file): static
    {
        $this->pdf_file = $pdf_file;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, Chapter>
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): static
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setCourse($this);
        }

        return $this;
    }

    public function removeChapter(Chapter $chapter): static
    {
        if ($this->chapters->removeElement($chapter)) {
            // set the owning side to null (unless already changed)
            if ($chapter->getCourse() === $this) {
                $chapter->setCourse(null);
            }
        }

        return $this;
    }

    // Getters et setters pour les nouveaux champs
    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;
        return $this;
    }

    public function incrementViews(): static
    {
        $this->views++;
        $this->last_accessed = new \DateTime();
        return $this;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;
        return $this;
    }

    public function incrementLikes(): static
    {
        $this->likes++;
        return $this;
    }

    public function getCommentsCount(): int
    {
        return $this->comments_count;
    }

    public function setCommentsCount(int $comments_count): static
    {
        $this->comments_count = $comments_count;
        return $this;
    }

    public function incrementCommentsCount(): static
    {
        $this->comments_count++;
        return $this;
    }

    public function getPopularityScore(): ?string
    {
        return $this->popularity_score;
    }

    public function setPopularityScore(?string $popularity_score): static
    {
        $this->popularity_score = $popularity_score;
        return $this;
    }

    public function calculatePopularityScore(): string
    {
        // Score = (Vues × 0.5) + (Likes × 2) + (Commentaires × 1.5)
        $score = ($this->views * 0.5) + ($this->likes * 2) + ($this->comments_count * 1.5);
        $this->popularity_score = (string)$score;
        return $this->popularity_score;
    }

    public function getWikipediaSummary(): ?string
    {
        return $this->wikipedia_summary;
    }

    public function setWikipediaSummary(?string $wikipedia_summary): static
    {
        $this->wikipedia_summary = $wikipedia_summary;
        return $this;
    }

    public function getLastAccessed(): ?\DateTime
    {
        return $this->last_accessed;
    }

    public function setLastAccessed(?\DateTime $last_accessed): static
    {
        $this->last_accessed = $last_accessed;
        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * Méthode utilitaire pour extraire les mots-clés du titre et de la description
     */
    public function generateKeywords(): string
    {
        $text = strtolower($this->titre . ' ' . $this->description);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $words = explode(' ', $text);
        $stopWords = ['le', 'la', 'les', 'de', 'des', 'du', 'et', 'à', 'a', 'pour', 'dans', 'avec', 'sur', 'par', 'que', 'qui', 'quoi', 'où', 'quand', 'comment', 'pourquoi', 'ce', 'cette', 'ces', 'cet', 'une', 'un', 'vos', 'votre', 'nos', 'notre', 'leur', 'leurs', 'tout', 'tous', 'toute', 'toutes'];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        $keywords = array_count_values($keywords);
        arsort($keywords);
        
        return implode(', ', array_slice(array_keys($keywords), 0, 10));
    }

    /**
     * Vérifie si le cours est "trending" (créé il y a moins de 7 jours et score élevé)
     */
    public function isTrending(): bool
    {
        $now = new \DateTime();
        $interval = $now->diff($this->created_at);
        return $interval->days <= 7 && (float)$this->popularity_score > 50;
    }

    /**
     * Retourne le nombre de chapitres
     */
    public function getChaptersCount(): int
    {
        return $this->chapters->count();
    }

    /**
     * Retourne le prix formaté
     */
    public function getFormattedPrice(): string
    {
        if ($this->price === null || $this->price == 0) {
            return 'Gratuit';
        }
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    /**
     * Retourne le statut formaté
     */
    public function getFormattedStatus(): string
    {
        return $this->status === 1 ? 'Actif' : 'Inactif';
    }
}
