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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


#[UniqueEntity(fields: ['username'], message: 'Istnieje już konto z tym loginem ')]
#[UniqueEntity(fields: ['email'], message: 'Istnieje już konto z tym emailem')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';
    public const CHANGE_PASSWORD = 'POST_CHANGE_PASSWORD';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message:'Brak loginu')]
    #[Assert\Length(max:180,maxMessage:'Wykorzystano maksymalną liczbe znaków')]
    private ?string $username = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Wybierz Uprawnienia')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:'Brak Hasła')]
    #[Assert\Length(max:100,maxMessage:'Wykorzystano maksymalną liczbe znaków')]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Assert\Email(message:'Niepoprawny adres Email')]
    #[Assert\NotBlank(message:'Brak Emaila')]
    #[Assert\Length(max:100,maxMessage:'Wykorzystano maksymalną liczbe znaków')]
    private ?string $email = null;

    #[Assert\Length(max:1000,maxMessage:'Wykorzystano maksymalną liczbe znaków')]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[Assert\Length(max:255,maxMessage:'Wykorzystano maksymalną liczbe znaków')]
    #[Assert\Url(message:'nieprawidłowy adres Url')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookProfile = null;


  

    #[ORM\Column(nullable: true,length:20)]
    #[Assert\Regex(pattern:"/^[0-9]*$/", message:"Pole może zawierać jedynie cyfry")]
    #[Assert\Length(min:9, minMessage:'za mało znaków', max:9, maxMessage:'za dużo znaków', exactMessage:'pole musi zawierać dokładnie 9 cyfr')]
    private ?string $phoneNumber = null;

    #[Assert\Length(max:255,maxMessage : 'Wykorzystano maksymalną liczbe znaków')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'postOwner', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Dog::class, inversedBy: 'guardian')]
    private Collection $guardianOf;

    #[ORM\ManyToMany(targetEntity: AdoptionCase::class, mappedBy: 'employee')]
    private Collection $employeeAdoptionCases;

    // #[ORM\JoinColumn(nullable: true)]
    #[ORM\OneToMany(mappedBy: 'client',targetEntity: AdoptionCase::class, cascade: ['persist', 'remove'],orphanRemoval: true)]
    private ?Collection $clientAdoptionCases = null;

    #[ORM\Column]
    private ?bool $available = true;

    #[ORM\Column(length: 255)]
    #[Assert\Type(type:'string', message:'Używaj jedynie liter')]
    #[Assert\NotBlank(message:'Wpisz Imie')]
    #[Assert\Regex(pattern:"/^[A-Z a-z]*$/", message:"Pole może zawierać jedynie litery")]
    private ?string $name = null;

    #[Assert\NotBlank(message:'Wpisz Nazwisko')]
    #[Assert\Type(type:'string', message:'Używaj jedynie liter')]
    #[Assert\Regex(pattern:"/^[A-Z a-z]*$/", message:"Pole może zawierać jedynie litery")]
    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\ManyToOne(inversedBy: 'UserStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column]
    private ?bool $archived = false;




    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->clientAdoptionCases = new ArrayCollection();
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

    public function setUsername(?string $username): self
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

    public function setRoles(?array $roles): self
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

    public function setPassword(?string $password): self
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

    public function setEmail(?string $email): self
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
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
        /**
     * @return Collection<int, Post>
     */
    public function getClientAdoptionCases(): Collection
    {
        return $this->clientAdoptionCases;
    }

    public function addClientAdoptionCases(AdoptionCase $adoptionCase): self
    {
        if (!$this->clientAdoptionCases->contains($adoptionCase)) {
            $this->clientAdoptionCases->add($adoptionCase);
            $adoptionCase->setClient($this);
        }

        return $this;
    }

    public function removeClientAdoptionCases(AdoptionCase $adoptionCase): self
    {
        if ($this->clientAdoptionCases->removeElement($adoptionCase)) {
            // set the owning side to null (unless already changed)
            if ($adoptionCase->getClient() === $this) {
                $adoptionCase->setClient(null);
            }
        }

        return $this;
    }



    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(?bool $available): self
    {
        $this->available = $available;

        return $this;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

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
