<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
class Documents
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $documentName = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?AdoptionCase $adoptionCase = null;

    #[ORM\Column(length: 255)]
    private ?string $documentSource = null;

    #[ORM\ManyToOne(inversedBy: 'DocumentStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    public function __toString()
    {
        return $this->documentName;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentName(): ?string
    {
        return $this->documentName;
    }

    public function setDocumentName(string $documentName): self
    {
        $this->documentName = $documentName;

        return $this;
    }

    public function getAdoptionCase(): ?AdoptionCase
    {
        return $this->adoptionCase;
    }

    public function setAdoptionCase(?AdoptionCase $adoptionCase): self
    {
        $this->adoptionCase = $adoptionCase;

        return $this;
    }

    public function getDocumentSource(): ?string
    {
        return $this->documentSource;
    }

    public function setDocumentSource(string $documentSource): self
    {
        $this->documentSource = $documentSource;

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
}
