<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     /**
     * @Assert\NotBlank(message="Email is required.")
     * @Assert\Email(message="Please enter a valid email address.")
     */
    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


     /**
     * @Assert\NotBlank(message="First name is required.")
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="First name must be at least {{ limit }} characters long.",
     *     maxMessage="First name cannot be longer than {{ limit }} characters."
     * )
     */
    #[Assert\NotBlank(message: 'First Name is required.')]
    #[Assert\Length(min: 2, max: 50, minMessage:"First name must be at least 3 characters long.",  maxMessage:"First name cannot be longer than 50 characters." )]
    #[ORM\Column(length: 120)]
    private ?string $firstName = null;

      /**
     * @Assert\NotBlank(message="Last name is required.")
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="Last name must be at least {{ limit }} characters long.",
     *     maxMessage="Last name cannot be longer than {{ limit }} characters."
     * )
     */
    #[Assert\NotBlank(message: 'Last Name  is required.')]
    #[Assert\Length(min: 2, max: 50, minMessage:"Last name must be at least 3 characters long.",  maxMessage:"Last name cannot be longer than 50 characters." )]
    #[ORM\Column(length: 120)]
    private ?string $lastName = null;


     /**
     * @Assert\NotBlank(message="Phone number is required.")
     * @Assert\Regex(
     *     pattern="/^\+?[0-9\s\-]{7,15}$/",
     *     message="Please enter a valid phone number."
     * )
     */
    #[Assert\NotBlank(message: 'Phone number is required.')]
    #[Assert\Regex( pattern:"/^\+?[0-9\s\-]{7,15}$/" , message:"Please enter a valid phone number.")]
    #[ORM\Column(length: 120)]
    private ?string $phone = null;

     /**
     * @Assert\NotBlank(message="Address is required.")
     * @Assert\Length(
     *     min=10,
     *     max=200,
     *     minMessage="Address must be at least {{ limit }} characters long.",
     *     maxMessage="Address cannot be longer than {{ limit }} characters."
     * )
     */
    #[Assert\NotBlank(message: 'Address  is required.')]
    #[Assert\Length(min: 10, max: 200, minMessage:"Address must be at least 3 characters long.",  maxMessage:"Address cannot be longer than 200 characters." )]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private bool $isVerified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
