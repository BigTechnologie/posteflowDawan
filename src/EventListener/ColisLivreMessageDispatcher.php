<?php 

namespace App\EventListener;

use App\Enum\StatutColis;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use App\Event\ColisStatusChangedEvent;
use App\Message\ColisLivreMessage;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener()]
final class ColisLivreMessageDispatcher
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ){}

    public function __invoke(
        ColisStatusChangedEvent $event
    ): void{
        
        if($event->getNouveauStatut() !== StatutColis::LIVRE) {
            return;
        }

        $colis = $event->getColis();

        $client = $colis->getClient();

        $message = new ColisLivreMessage(
            colisId: $colis->getId(),
            numeroSuivi: $colis->getNumeroSuivi(),
            clientEmail: $client->getEmail()
        );

        $this->messageBus->dispatch($message);

    }


}