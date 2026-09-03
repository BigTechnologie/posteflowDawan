<?php 

namespace App\Event;

use App\Entity\Colis;
use App\Enum\StatutColis;

final class ColisStatusChangedEvent
{
    public function __construct(
        private readonly Colis $colis,
        private readonly StatutColis $ancienStatut,
        private readonly StatutColis $nouveauStatut,
        private readonly \DateTimeImmutable $dateChangement = new \DateTimeImmutable()
    ){}


    public function getColis(): Colis
    {
        return $this->colis;
    }

    public function getAncienStatut(): StatutColis
    {
        return $this->ancienStatut;
    }

    public function getNouveauStatut(): StatutColis
    {
        return $this->nouveauStatut;
    }

    public function getDateChangement(): \DateTimeImmutable
    {
        return $this->dateChangement;
    }

}