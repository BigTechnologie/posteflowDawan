<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Colis;
use App\Entity\MouvementColis;
use App\Enum\StatutColis;

class TrackingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    public function changerStatut(
        Colis $colis,
        StatutColis $statut,
        string $lieux,
        ?string $commentaire = null
    ): void
    {

        $colis->setStatut($statut);

        $mouvement = (new MouvementColis())
            ->setColis($colis)
            ->setStatut($statut)
            ->setLieu($lieux)
            ->setCommentaire($commentaire);

        $this->entityManager->persist($mouvement);
        $this->entityManager->flush();

    }

}