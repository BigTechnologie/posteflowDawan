<?php

namespace App\Entity;

use App\Enum\StatutColis;
use App\Repository\MouvementColisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MouvementColisRepository::class)]
class MouvementColis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(
        length: 30,
        enumType: StatutColis::class
    )]
    private StatutColis $statut = StatutColis::CREE;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(
        message: 'Le lieu du mouvement est obligatoire.'
    )]
    #[Assert\Length(
        min: 2,
        max: 160,
        minMessage: 'Le lieu doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le lieu ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $lieu = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $commentaire = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La date du mouvement est obligatoire.'
    )]
    #[Assert\LessThanOrEqual(
        value: 'now',
        message: 'La date du mouvement ne peut pas être située dans le futur.'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'mouvements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(
        message: 'Le mouvement doit être associé à un colis.'
    )]
    private ?Colis $colis = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = trim($lieu);

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        if ($commentaire === null) {
            $this->commentaire = null;

            return $this;
        }

        $commentaire = trim($commentaire);

        $this->commentaire = $commentaire === ''
            ? null
            : $commentaire;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getColis(): ?Colis
    {
        return $this->colis;
    }

    public function setColis(?Colis $colis): self
    {
        $this->colis = $colis;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s - %s',
            $this->statut->label(),
            $this->lieu,
            $this->createdAt->format('d/m/Y H:i')
        );
    }
}