<?php

namespace App\Entity;

use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgenceRepository::class)]
class Agence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(
    message: 'Le nom de l’agence est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 120,
        minMessage: 'Le nom de l’agence doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom de l’agence ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $nom = '';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(
        message: 'Le code postal de l’agence est obligatoire.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal doit contenir exactement 5 chiffres.'
    )]
    private string $codePostal = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(
        message: 'La ville de l’agence est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 120,
        minMessage: 'La ville doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s\-']+$/u",
        message: 'La ville ne peut contenir que des lettres, espaces, apostrophes et tirets.'
    )]
    private string $ville = '';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(
        message: 'L’adresse de l’agence est obligatoire.'
    )]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: 'L’adresse doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'L’adresse ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $adresse = '';

    #[ORM\Column]
    private bool $active = true;

    /**
     * Liste des clients rattachés à l’agence.
     *
     * La propriété propriétaire de la relation se trouve dans Client,
     * sous le nom agenceReference.
     *
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(
        mappedBy: 'agenceReference',
        targetEntity: Client::class
    )]
    private Collection $clients;

    /**
     * Liste des colis déposés dans l’agence.
     *
     * La propriété propriétaire de la relation se trouve dans Colis,
     * sous le nom agenceDepot.
     *
     * @var Collection<int, Colis>
     */
    #[ORM\OneToMany(
        mappedBy: 'agenceDepot',
        targetEntity: Colis::class
    )]
    private Collection $colis;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->colis = new ArrayCollection();
    }

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

    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

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

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active; 
    }

    public function setActive(bool $active): self
    {
        $this->active = $active; 

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) { 
            $this->clients->add($client); 

            $client->setAgenceReference($this); 
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            
            if ($client->getAgenceReference() === $this) {
                $client->setAgenceReference(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colis>
     */
    public function getColis(): Collection
    {
        return $this->colis;
    }

    public function addColis(Colis $colis): self
    {
        if (!$this->colis->contains($colis)) { 
            $this->colis->add($colis); 

            $colis->setAgenceDepot($this); 
        }

        return $this;
    }

    public function removeColis(Colis $colis): self
    {
        if ($this->colis->removeElement($colis)) {
            
            if ($colis->getAgenceDepot() === $this) {
                $colis->setAgenceDepot(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom.' - '.$this->ville;
    }
}