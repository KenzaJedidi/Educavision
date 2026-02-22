<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $student = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $teacher = null;

    #[ORM\Column]
    private ?int $is_read = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $last_message_at = null;

    #[ORM\Column]
    private ?int $message_count = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $last_message = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: true)]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: ConversationMessage::class, cascade: ['persist', 'remove'])]
    private Collection $conversationMessages;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->is_read = 0;
        $this->message_count = 1;
        $this->conversationMessages = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStudent(): ?string
    {
        return $this->student;
    }

    public function setStudent(?string $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getIsRead(): ?int
    {
        return $this->is_read;
    }

    public function setIsRead(?int $is_read): static
    {
        $this->is_read = $is_read;

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

    public function getLastMessageAt(): ?\DateTime
    {
        return $this->last_message_at;
    }

    public function setLastMessageAt(?\DateTime $last_message_at): static
    {
        $this->last_message_at = $last_message_at;

        return $this;
    }

    public function getMessageCount(): ?int
    {
        return $this->message_count;
    }

    public function setMessageCount(?int $message_count): static
    {
        $this->message_count = $message_count;

        return $this;
    }

    public function getLastMessage(): ?string
    {
        return $this->last_message;
    }

    public function setLastMessage(?string $last_message): static
    {
        $this->last_message = $last_message;

        return $this;
    }

    /**
     * Marque le message comme lu
     */
    public function markAsRead(): static
    {
        $this->is_read = 1;

        return $this;
    }

    /**
     * Marque le message comme non lu
     */
    public function markAsUnread(): static
    {
        $this->is_read = 0;

        return $this;
    }

    /**
     * Met à jour les informations du dernier message
     */
    public function updateLastMessage(string $content): static
    {
        $this->last_message = $content;
        $this->last_message_at = new \DateTime();
        $this->message_count++;

        return $this;
    }

    /**
     * Vérifie si le message est lu
     */
    public function isRead(): bool
    {
        return $this->is_read === 1;
    }

    /**
     * @return Collection<int, ConversationMessage>
     */
    public function getConversationMessages(): Collection
    {
        return $this->conversationMessages;
    }

    public function addConversationMessage(ConversationMessage $conversationMessage): static
    {
        if (!$this->conversationMessages->contains($conversationMessage)) {
            $this->conversationMessages->add($conversationMessage);
            $conversationMessage->setMessage($this);
        }

        return $this;
    }

    public function removeConversationMessage(ConversationMessage $conversationMessage): static
    {
        if ($this->conversationMessages->removeElement($conversationMessage)) {
            // set the owning side to null (unless already changed)
            if ($conversationMessage->getMessage() === $this) {
                $conversationMessage->setMessage(null);
            }
        }

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
