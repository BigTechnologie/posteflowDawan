<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(
        message: 'Le nom du client est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom du client doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du client ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $nom = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(
        message: 'L’adresse e-mail du client est obligatoire.'
    )]
    #[Assert\Email(
        message: 'L’adresse e-mail "{{ value }}" n’est pas valide.'
    )]
    #[Assert\Length(
        max: 120,
        maxMessage: 'L’adresse e-mail ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $email = '';

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(
        message: 'Le numéro de téléphone du client est obligatoire.'
    )]
    #[Assert\Regex(
        pattern: '/^(?:\+33|0)[1-9](?:[\s.\-]?\d{2}){4}$/',
        message: 'Le numéro de téléphone doit être un numéro français valide.'
    )]
    private string $telephone = '';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(
        message: 'L’adresse du client est obligatoire.'
    )]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: 'L’adresse doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'L’adresse ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $adresse = '';

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(
        message: 'La ville du client est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 80,
        minMessage: 'La ville doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s\-']+$/u",
        message: 'La ville ne peut contenir que des lettres, espaces, apostrophes et tirets.'
    )]
    private string $ville = '';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(
        message: 'Le code postal du client est obligatoire.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal doit contenir exactement 5 chiffres.'
    )]
    private string $codePostal = '';

    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'Le client doit être rattaché à une agence.'
    )]
    private ?Agence $agenceReference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getAgenceReference(): ?Agence
    {
        return $this->agenceReference;
    }

    public function setAgenceReference(?Agence $agenceReference): self
    {
        $this->agenceReference = $agenceReference;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
