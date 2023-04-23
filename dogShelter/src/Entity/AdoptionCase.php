<?php

namespace App\Entity;

use App\Repository\AdoptionCaseRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdoptionCaseRepository::class)]
class AdoptionCase
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adoptionCase')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dog $dog = null;

    #[ORM\OneToMany(mappedBy: 'adoptionCase', targetEntity: Documents::class)]
    private Collection $documents;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'employeeAdoptionCases')]
    private Collection $employee;

    // #[ORM\OneToMany(mappedBy: 'clientAdoptionCases', cascade: ['persist'])]
    #[ORM\ManyToOne(inversedBy: 'clientAdoptionCases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'CaseStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column]
    private ?bool $archived = false;


    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->employee = new ArrayCollection();
        
        
        
    }
    public function __toString()
    {
        return $this->getId().". ".$this->getDog();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDog(): ?Dog
    {
        return $this->dog;
    }

    public function setDog(Dog $dog): self
    {
        $this->dog = $dog;

        return $this;
    }

    /**
     * @return Collection<int, Documents>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setAdoptionCase($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAdoptionCase() === $this) {
                $document->setAdoptionCase(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getEmployee(): Collection
    {
        return $this->employee;
    }

    public function addEmployee(User $employee): self
    {
        if (!$this->employee->contains($employee)) {
            $this->employee->add($employee);
        }

        return $this;
    }

    public function removeEmployee(User $employee): self
    {
        $this->employee->removeElement($employee);

        return $this;
    }
    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

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
