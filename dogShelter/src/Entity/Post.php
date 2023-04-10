<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Tytuł nie może być pusty')]
    #[Assert\Length(max:255, maxMessage:'Tytuł zbyt długi (max 255 znaków)')]
    private ?string $title = null;

    #[ORM\Column(length: 5400)]
    #[Assert\NotBlank(message:'Brak treści')]
    #[Assert\Length(max:5400, maxMessage:'Tekst zbyd długi (max 5400 znaków)')]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max:255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $postOwner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'PostStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column]
    private ?bool $archived = false;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function __toString()
    {
        return $this->title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPostOwner(): ?User
    {
        return $this->postOwner;
    }

    public function setPostOwner(?User $postOwner): self
    {
        $this->postOwner = $postOwner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isarchived(): ?bool
    {
        return $this->archived;
    }

    public function setarchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
