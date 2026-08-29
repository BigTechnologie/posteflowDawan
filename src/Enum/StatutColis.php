<?php

namespace App\Enum;

enum StatutColis: string
{
    case CREE = 'CREE';
    case PRIS_EN_CHARGE = 'PRIS_EN_CHARGE';
    case EN_TRANSIT = 'EN_TRANSIT';
    case EN_LIVRAISON = 'EN_LIVRAISON';
    case LIVRE = 'LIVRE';
    case INCIDENT = 'INCIDENT';
    case RETOUR = 'RETOUR';

    /**
     * Retourne un libellé lisible destiné à l’interface utilisateur.
     */
    public function label(): string
    {
        return match ($this) {
            self::CREE => 'Créé',
            self::PRIS_EN_CHARGE => 'Pris en charge',
            self::EN_TRANSIT => 'En transit',
            self::EN_LIVRAISON => 'En livraison',
            self::LIVRE => 'Livré',
            self::INCIDENT => 'Incident',
            self::RETOUR => 'Retour',
        };
    }

    /**
     * Retourne les choix dans le format attendu par ChoiceType :
     *
     * [
     *     'Créé' => StatutColis::CREE,
     *     'Pris en charge' => StatutColis::PRIS_EN_CHARGE,
     * ]
     *
     * @return array<string, self>
     */
    public static function choices(): array
    {
        $choices = [];

        foreach (self::cases() as $statut) {
            $choices[$statut->label()] = $statut;
        }

        return $choices;
    }

    /**
     * Retourne uniquement les valeurs techniques.
     *
     * Exemple :
     *
     * [
     *     'CREE',
     *     'PRIS_EN_CHARGE',
     *     'EN_TRANSIT',
     * ]
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $statut): string => $statut->value,
            self::cases()
        );
    }

    /**
     * Retourne une classe Bootstrap associée au statut.
     *
     * Cette méthode pourra être utilisée dans les templates Twig.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::CREE => 'text-bg-secondary',
            self::PRIS_EN_CHARGE => 'text-bg-primary',
            self::EN_TRANSIT => 'text-bg-info',
            self::EN_LIVRAISON => 'text-bg-warning',
            self::LIVRE => 'text-bg-success',
            self::INCIDENT => 'text-bg-danger',
            self::RETOUR => 'text-bg-dark',
        };
    }

    /**
     * Indique si le statut termine le cycle normal du colis.
     */
    public function isFinal(): bool
    {
        return match ($this) {
            self::LIVRE,
            self::RETOUR => true,

            default => false,
        };
    }
}