<?php

namespace App\DTO;

use App\Enum\StatutColis;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO contenant les critères utilisés pour rechercher des colis.
 *
 * Cet objet n’est pas une entité Doctrine :
 *
 * - il ne possède pas d’identifiant ;
 * - il ne correspond à aucune table ;
 * - il n’est jamais persisté en base de données.
 *
 * Il sert uniquement à transporter les données du formulaire
 * de recherche jusqu’au repository.
 */
class ColisSearchDTO
{
    /**
     * Recherche textuelle générale.
     *
     * Cette valeur pourra être recherchée dans :
     *
     * - le numéro de suivi ;
     * - le nom du destinataire ;
     * - le nom du client.
     */
    #[Assert\Length(
        max: 100,
        maxMessage: 'La recherche ne peut pas dépasser {{ limit }} caractères.'
    )]
    public ?string $terme = null;

    /**
     * Statut du colis sélectionné dans le filtre.
     *
     * Exemple :
     *
     * StatutColis::EN_TRANSIT
     */
    public ?StatutColis $statut = null;

    /**
     * Ville de livraison recherchée.
     */
    #[Assert\Length(
        max: 80,
        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]*$/u", // regex pour autoriser les lettres, espaces, apostrophes et tirets
        message: 'La ville ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.'
    )]
    public ?string $ville = null;

    /**
     * Indique si au moins un critère de recherche a été renseigné.
     */
    public function hasCriteria(): bool
    {
        return $this->terme !== null
            || $this->statut !== null
            || $this->ville !== null;
    }

    /**
     * Nettoie et normalise les critères textuels.
     *
     * Une chaîne vide est transformée en null afin d’éviter
     * d’envoyer des critères inutiles au repository.
     */
    public function normalize(): void
    {
        $this->terme = $this->normalizeString(
            $this->terme
        );

        $this->ville = $this->normalizeString(
            $this->ville
        );
    }

    private function normalizeString(
        ?string $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== ''
            ? $value
            : null;
    }
}