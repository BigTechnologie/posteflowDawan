<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: ['email'],
    message: 'Un utilisateur utilise déjà cette adresse e-mail.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_AGENT = 'ROLE_AGENT';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_AGENT,
        self::ROLE_ADMIN,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(
        message: 'L’adresse e-mail est obligatoire.'
    )]
    #[Assert\Email(
        message: 'L’adresse e-mail "{{ value }}" n’est pas valide.'
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L’adresse e-mail ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $email = '';

    /**
     * @var list<string>
     */
    #[ORM\Column(type: 'json')]
    #[Assert\NotNull(
        message: 'Les rôles de l’utilisateur doivent être définis.'
    )]
    #[Assert\All([
        new Assert\Choice(
            choices: self::ROLES,
            message: 'Le rôle "{{ value }}" n’est pas autorisé.'
        ),
    ])]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(
        message: 'Le mot de passe chiffré est obligatoire.'
    )]
    private string $password = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(
        message: 'Le nom de l’utilisateur est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 120,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'Le nom ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.'
    )]
    private string $nom = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = trim($nom);

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            $this->nom,
            $this->email
        );
    }
}