<?php

namespace App\Entity;

use App\Repository\DogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as AssertAssert;

#[ORM\Entity(repositoryClass: DogRepository::class)]
class Dog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:'To pole nie może być puste')]
    #[Assert\Length(max:30, maxMessage:'Imie jest zbyt długie (max 30 zanków)')]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max:255,maxMessage:'Nazwa Rasy jest zbyt długa (max 255 zanków)')]
    private ?string $race = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(max:30)]
    private ?string $sex = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Assert\Length(max:1000,maxMessage:'Opis jest zbyt długi (max 1000 znaków)')]
    private ?string $description = null;

    #[Assert\Length(max:255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'guardianOf')]
    #[Assert\Count(min:1,minMessage:"Wybierz chociaż 1 opiekuna")]
    private Collection $guardian;


    #[ORM\OneToMany(mappedBy: 'dog',targetEntity: AdoptionCase::class, cascade: ['persist', 'remove'],orphanRemoval: true)]
    private ?Collection $adoptionCase = null;

    #[ORM\Column]
    private ?bool $inAdoption = false;

    #[ORM\ManyToOne(inversedBy: 'DogStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column]
    private ?bool $archived = false;

    public function __construct()
    {
        $this->guardian = new ArrayCollection();
        $this->adoptionCase = new ArrayCollection();
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

    public function setName(?string $name): self
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

    // /**
    //  * @return Collection<int, User>
    //  */
    public function getGuardian(): Collection
    {
        return $this->guardian;
    }

    public function addGuardian(?User $guardian): self
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

    public function getAdoptionCase(): Collection
    {
        return $this->adoptionCase;
    }

    public function addAdoptionCase(AdoptionCase $adoptionCase): self
    {
        if (!$this->adoptionCase->contains($adoptionCase)) {
            $this->adoptionCase->add($adoptionCase);
            $adoptionCase->setDog($this);
        }
        return $this;
    }
    public function removeAdoptionCase(AdoptionCase $adoptionCase): self
    {
        if ($this->adoptionCase->removeElement($adoptionCase)) {
            // set the owning side to null (unless already changed)
            if ($adoptionCase->getDog() === $this) {
                $adoptionCase->setDog(null);
            }
        }

        return $this;
    }


    public function isInAdoption(): ?bool
    {
        return $this->inAdoption;
    }

    public function setInAdoption(?bool $inAdoption): self
    {
        $this->inAdoption = $inAdoption;

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
