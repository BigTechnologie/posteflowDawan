<?php 

namespace App\EventListener;

use App\Event\ColisStatusChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener()]
final class ColisStatusChangedAuditListener
{

public function __construct(
        #[Autowire(service: 'monolog.logger.audit')]
        private readonly LoggerInterface $auditLogger
    ){}


    public function __invoke(
        ColisStatusChangedEvent $event
    ): void{
        $colis = $event->getColis();

        $this->auditLogger->info(
            'AUDIT - Changement de statut',
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