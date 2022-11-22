<?php

namespace App\Entity;

use App\Repository\AdoptionCaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdoptionCaseRepository::class)]
class AdoptionCase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'adoptionCase', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dog $dog = null;

    #[ORM\OneToMany(mappedBy: 'adoptionCase', targetEntity: Documents::class)]
    private Collection $documents;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'employeeAdoptionCases')]
    private Collection $employee;

    #[ORM\OneToOne(inversedBy: 'clientAdoptionCases', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;













    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->employee = new ArrayCollection();
        
        
        
    }
    public function __toString()
    {
        return $this->getId();
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

    public function setClient(User $client): self
    {
        $this->client = $client;

        return $this;
    }







}
