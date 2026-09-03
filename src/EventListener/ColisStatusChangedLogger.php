<?php 

namespace App\EventListener;

use App\Event\ColisStatusChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener()]
final class ColisStatusChangedLogger
{
    public function __construct(
        private readonly LoggerInterface $logger
    ){}


    public function __invoke(
        ColisStatusChangedEvent $event
    ): void{
        $colis = $event->getColis();

        $this->logger->info(
            'Le statut du colis {numeroSuivi} est passé de {ancienStatut} à {nouveauStatut}.',
            [
                'numeroSuivi' => $colis->getNumeroSuivi(),
                'ancienStatut' => $event
                    ->getAncienStatut()->label(),
                'nouveauStatut' => $event
                    ->getNouveauStatut()->label(),
                'dateChangement' => $event
                    ->getDateChangement()->format(\DateTimeInterface::ATOM),
                'colisId' => $colis->getId(),

            ]
        );


    }
    

    



}