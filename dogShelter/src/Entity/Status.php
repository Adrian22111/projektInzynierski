<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusName = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: AdoptionCase::class)]
    private Collection $CaseStatus;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Dog::class)]
    private Collection $DogStatus;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: User::class)]
    private Collection $UserStatus;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Documents::class)]
    private Collection $DocumentStatus;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Post::class)]
    private Collection $PostStatus;

    public function __construct()
    {
        $this->CaseStatus = new ArrayCollection();
        $this->DogStatus = new ArrayCollection();
        $this->UserStatus = new ArrayCollection();
        $this->DocumentStatus = new ArrayCollection();
        $this->PostStatus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusName(): ?string
    {
        return $this->StatusName;
    }

    public function setStatusName(string $StatusName): self
    {
        $this->StatusName = $StatusName;

        return $this;
    }

    /**
     * @return Collection<int, AdoptionCase>
     */
    public function getCaseStatus(): Collection
    {
        return $this->CaseStatus;
    }

    public function addCaseStatus(AdoptionCase $caseStatus): self
    {
        if (!$this->CaseStatus->contains($caseStatus)) {
            $this->CaseStatus->add($caseStatus);
            $caseStatus->setStatus($this);
        }

        return $this;
    }

    public function removeCaseStatus(AdoptionCase $caseStatus): self
    {
        if ($this->CaseStatus->removeElement($caseStatus)) {
            // set the owning side to null (unless already changed)
            if ($caseStatus->getStatus() === $this) {
                $caseStatus->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dog>
     */
    public function getDogStatus(): Collection
    {
        return $this->DogStatus;
    }

    public function addDogStatus(Dog $dogStatus): self
    {
        if (!$this->DogStatus->contains($dogStatus)) {
            $this->DogStatus->add($dogStatus);
            $dogStatus->setStatus($this);
        }

        return $this;
    }

    public function removeDogStatus(Dog $dogStatus): self
    {
        if ($this->DogStatus->removeElement($dogStatus)) {
            // set the owning side to null (unless already changed)
            if ($dogStatus->getStatus() === $this) {
                $dogStatus->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserStatus(): Collection
    {
        return $this->UserStatus;
    }

    public function addUserStatus(User $userStatus): self
    {
        if (!$this->UserStatus->contains($userStatus)) {
            $this->UserStatus->add($userStatus);
            $userStatus->setStatus($this);
        }

        return $this;
    }

    public function removeUserStatus(User $userStatus): self
    {
        if ($this->UserStatus->removeElement($userStatus)) {
            // set the owning side to null (unless already changed)
            if ($userStatus->getStatus() === $this) {
                $userStatus->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documents>
     */
    public function getDocumentStatus(): Collection
    {
        return $this->DocumentStatus;
    }

    public function addDocumentStatus(Documents $documentStatus): self
    {
        if (!$this->DocumentStatus->contains($documentStatus)) {
            $this->DocumentStatus->add($documentStatus);
            $documentStatus->setStatus($this);
        }

        return $this;
    }

    public function removeDocumentStatus(Documents $documentStatus): self
    {
        if ($this->DocumentStatus->removeElement($documentStatus)) {
            // set the owning side to null (unless already changed)
            if ($documentStatus->getStatus() === $this) {
                $documentStatus->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPostStatus(): Collection
    {
        return $this->PostStatus;
    }

    public function addPostStatus(Post $postStatus): self
    {
        if (!$this->PostStatus->contains($postStatus)) {
            $this->PostStatus->add($postStatus);
            $postStatus->setStatus($this);
        }

        return $this;
    }

    public function removePostStatus(Post $postStatus): self
    {
        if ($this->PostStatus->removeElement($postStatus)) {
            // set the owning side to null (unless already changed)
            if ($postStatus->getStatus() === $this) {
                $postStatus->setStatus(null);
            }
        }

        return $this;
    }
}
