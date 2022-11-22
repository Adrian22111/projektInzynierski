<?php

namespace App\Entity;

use App\Repository\DogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DogRepository::class)]
class Dog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $race = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $sex = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'guardianOf')]
    private Collection $guardian;

    #[ORM\OneToOne(mappedBy: 'dog', cascade: ['persist', 'remove'])]
    private ?AdoptionCase $adoptionCase = null;

    #[ORM\Column]
    private ?bool $inAdoption = null;

    public function __construct()
    {
        $this->guardian = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(?string $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, User>
     */
    public function getGuardian(): Collection
    {
        return $this->guardian;
    }

    public function addGuardian(User $guardian): self
    {
        if (!$this->guardian->contains($guardian)) {
            $this->guardian->add($guardian);
            $guardian->addGuardianOf($this);
        }

        return $this;
    }

    public function removeGuardian(User $guardian): self
    {
        if ($this->guardian->removeElement($guardian)) {
            $guardian->removeGuardianOf($this);
        }

        return $this;
    }

    public function getAdoptionCase(): ?AdoptionCase
    {
        return $this->adoptionCase;
    }

    public function setAdoptionCase(AdoptionCase $adoptionCase): self
    {
        // set the owning side of the relation if necessary
        if ($adoptionCase->getDog() !== $this) {
            $adoptionCase->setDog($this);
        }

        $this->adoptionCase = $adoptionCase;

        return $this;
    }

    public function isInAdoption(): ?bool
    {
        return $this->inAdoption;
    }

    public function setInAdoption(bool $inAdoption): self
    {
        $this->inAdoption = $inAdoption;

        return $this;
    }
}
