<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[UniqueEntity(fields: ['username'], message: 'Istnieje już konto z tym loginem ')]
#[UniqueEntity(fields: ['email'], message: 'Istnieje już konto z tym emailem')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookProfile = null;

    #[ORM\Column(nullable: true)]
    private ?int $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'postOwner', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Dog::class, inversedBy: 'guardian')]
    private Collection $guardianOf;

    #[ORM\ManyToMany(targetEntity: AdoptionCase::class, mappedBy: 'employee')]
    private Collection $employeeAdoptionCases;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?AdoptionCase $clientAdoptionCases = null;

    #[ORM\Column]
    private ?bool $available = true;




    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->guardianOf = new ArrayCollection();
        $this->employeeAdoptionCases = new ArrayCollection();

        
    }
    public function __toString()
    {
        return $this->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getFacebookProfile(): ?string
    {
        return $this->facebookProfile;
    }

    public function setFacebookProfile(?string $facebookProfile): self
    {
        $this->facebookProfile = $facebookProfile;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setPostOwner($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPostOwner() === $this) {
                $post->setPostOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dog>
     */
    public function getGuardianOf(): Collection
    {
        return $this->guardianOf;
    }

    public function addGuardianOf(Dog $guardianOf): self
    {
        if (!$this->guardianOf->contains($guardianOf)) {
            $this->guardianOf->add($guardianOf);
        }

        return $this;
    }

    public function removeGuardianOf(Dog $guardianOf): self
    {
        $this->guardianOf->removeElement($guardianOf);

        return $this;
    }

    /**
     * @return Collection<int, AdoptionCase>
     */
    public function getEmployeeAdoptionCases(): Collection
    {
        return $this->employeeAdoptionCases;
    }

    public function addEmployeeAdoptionCase(AdoptionCase $employeeAdoptionCase): self
    {
        if (!$this->employeeAdoptionCases->contains($employeeAdoptionCase)) {
            $this->employeeAdoptionCases->add($employeeAdoptionCase);
            $employeeAdoptionCase->addEmployee($this);
        }

        return $this;
    }

    public function removeEmployeeAdoptionCase(AdoptionCase $employeeAdoptionCase): self
    {
        if ($this->employeeAdoptionCases->removeElement($employeeAdoptionCase)) {
            $employeeAdoptionCase->removeEmployee($this);
        }

        return $this;
    }

    public function getClientAdoptionCases(): ?AdoptionCase
    {
        return $this->clientAdoptionCases;
    }

    public function setClientAdoptionCases(AdoptionCase $clientAdoptionCases): self
    {
        // set the owning side of the relation if necessary
        if ($clientAdoptionCases->getClient() !== $this) {
            $clientAdoptionCases->setClient($this);
        }

        $this->clientAdoptionCases = $clientAdoptionCases;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }







  
}
