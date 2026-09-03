<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Colis;
use App\Entity\MouvementColis;
use App\Enum\StatutColis;
use App\Event\ColisStatusChangedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TrackingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ){}

    public function changerStatut(
        Colis $colis,
        StatutColis $nouveauStatut,
        string $lieux,
        ?string $commentaire = null
    ): void
    {

        // On recupère l'ancien statut avant de modifier le colis
        $ancienStatut = $colis->getStatut();

        // Mise à jour du statut
        $colis->setStatut($nouveauStatut);

        // Création du mouvement d'historique
        $mouvement = (new MouvementColis())
            ->setColis($colis)
            ->setStatut($nouveauStatut)
            ->setLieu($lieux)
            ->setCommentaire($commentaire);

        $this->entityManager->persist($mouvement);
        $this->entityManager->flush();

        // On dispatche pour annoncer que le statut du colis a changé
        $this->eventDispatcher->dispatch(
            new ColisStatusChangedEvent(
                $colis,
                $ancienStatut,
                $nouveauStatut
            )
        );

    }

}