<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 3, minMessage: 'Le titre doit faire au moins {{ limit }} caractères')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu est obligatoire')]
    private ?string $content = null;

    #[ORM\Column(name: 'position')]
    #[Assert\NotBlank(message: 'La position est obligatoire')]
    #[Assert\Type(type: 'integer', message: 'La position doit être un nombre entier')]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $teacher_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $teacher_email = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'chapters')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: false)]
    private ?Course $course = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @deprecated Use getTitle() instead
     */
    public function getTitre(): ?string
    {
        return $this->title;
    }

    /**
     * @deprecated Use setTitle() instead
     */
    public function setTitre(string $titre): static
    {
        $this->title = $titre;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @deprecated Use getContent() instead
     */
    public function getDescription(): ?string
    {
        return $this->content;
    }

    /**
     * @deprecated Use setContent() instead
     */
    public function setDescription(?string $description): static
    {
        $this->content = $description;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @deprecated Use getPosition() instead
     */
    public function getOrdre(): ?int
    {
        return $this->position;
    }

    /**
     * @deprecated Use setPosition() instead
     */
    public function setOrdre(?int $ordre): static
    {
        $this->position = $ordre;

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
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @deprecated Use getCreatedAt() instead
     */
    public function getCreated_at(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @deprecated Use setCreatedAt() instead
     */
    public function setCreated_at(\DateTime $created_at): static
    {
        $this->createdAt = $created_at;

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
}
