<?php

namespace App\Entity;

use App\Enum\StatutColis;
use App\Repository\ColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ColisRepository::class)]
#[UniqueEntity(
    fields: ['numeroSuivi'],
    message: 'Un colis utilise déjà ce numéro de suivi.'
)]
class Colis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(
        message: 'Le numéro de suivi est obligatoire.'
    )]
    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: 'Le numéro de suivi doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le numéro de suivi ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z0-9-]+$/',
        message: 'Le numéro de suivi ne peut contenir que des lettres majuscules, des chiffres et des tirets.'
    )]
    private string $numeroSuivi = '';

    #[ORM\Column(
        length: 30,
        enumType: StatutColis::class
    )]
    private StatutColis $statut = StatutColis::CREE;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(
        message: 'Le nom du destinataire est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 120,
        minMessage: 'Le nom du destinataire doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du destinataire ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $destinataire = '';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(
        message: 'L’adresse de livraison est obligatoire.'
    )]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: 'L’adresse de livraison doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'L’adresse de livraison ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $adresseLivraison = '';

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(
        message: 'La ville de livraison est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 80,
        minMessage: 'La ville de livraison doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La ville de livraison ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'La ville de livraison ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.'
    )]
    private string $villeLivraison = '';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(
        message: 'Le code postal de livraison est obligatoire.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal de livraison doit contenir exactement 5 chiffres.'
    )]
    private string $codePostalLivraison = '';

    #[ORM\Column(nullable: false)]
    #[Assert\NotNull(
        message: 'Le poids du colis est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le poids du colis doit être strictement supérieur à zéro.'
    )]
    #[Assert\LessThanOrEqual(
        value: 30,
        message: 'Le poids du colis ne peut pas dépasser {{ compared_value }} kg.'
    )]
    private ?float $poidsKg = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La date de création du colis est obligatoire.'
    )]
    #[Assert\LessThanOrEqual(
        value: 'now',
        message: 'La date de création du colis ne peut pas être située dans le futur.'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'Le colis doit être associé à un client.'
    )]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'colis')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'Le colis doit être associé à une agence de dépôt.'
    )]
    private ?Agence $agenceDepot = null;

    /**
     * @var Collection<int, MouvementColis>
     */
    #[ORM\OneToMany(
        mappedBy: 'colis',
        targetEntity: MouvementColis::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy([
        'createdAt' => 'DESC',
    ])]
    private Collection $mouvements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->mouvements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroSuivi(): string
    {
        return $this->numeroSuivi;
    }

    public function setNumeroSuivi(string $numeroSuivi): self
    {
        $this->numeroSuivi = strtoupper(
            trim($numeroSuivi)
        );

        return $this;
    }

    public function getStatut(): StatutColis
    {
        return $this->statut;
    }

    public function setStatut(StatutColis $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDestinataire(): string
    {
        return $this->destinataire;
    }

    public function setDestinataire(string $destinataire): self
    {
        $this->destinataire = trim($destinataire);

        return $this;
    }

    public function getAdresseLivraison(): string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = trim($adresseLivraison);

        return $this;
    }

    public function getVilleLivraison(): string
    {
        return $this->villeLivraison;
    }

    public function setVilleLivraison(string $villeLivraison): self
    {
        $this->villeLivraison = trim($villeLivraison);

        return $this;
    }

    public function getCodePostalLivraison(): string
    {
        return $this->codePostalLivraison;
    }

    public function setCodePostalLivraison(
        string $codePostalLivraison
    ): self {
        $this->codePostalLivraison = trim(
            $codePostalLivraison
        );

        return $this;
    }

    public function getPoidsKg(): ?float
    {
        return $this->poidsKg;
    }

    public function setPoidsKg(?float $poidsKg): self
    {
        $this->poidsKg = $poidsKg;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(
        \DateTimeImmutable $createdAt
    ): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getAgenceDepot(): ?Agence
    {
        return $this->agenceDepot;
    }

    public function setAgenceDepot(
        ?Agence $agenceDepot
    ): self {
        $this->agenceDepot = $agenceDepot;

        return $this;
    }

    /**
     * @return Collection<int, MouvementColis>
     */
    public function getMouvements(): Collection
    {
        return $this->mouvements;
    }

    public function addMouvement(
        MouvementColis $mouvement
    ): self {
        if (!$this->mouvements->contains($mouvement)) {
            $this->mouvements->add($mouvement);

            $mouvement->setColis($this);
        }

        return $this;
    }

    public function removeMouvement(
        MouvementColis $mouvement
    ): self {
        if ($this->mouvements->removeElement($mouvement)) {
            
            if ($mouvement->getColis() === $this) {
                $mouvement->setColis(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->numeroSuivi;
    }
}