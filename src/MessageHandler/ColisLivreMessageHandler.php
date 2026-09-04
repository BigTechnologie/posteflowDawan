<?php

namespace App\MessageHandler;

use App\Message\ColisLivreMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;


#[AsMessageHandler]
final class ColisLivreMessageHandler
{
    public function __construct(
        private readonly MailerInterface $mailer, 
        private readonly LoggerInterface $logger 
    ) {
    }

    public function __invoke(
        ColisLivreMessage $message 
    ): void {
        $clientEmail = $message->getClientEmail(); 

        /*
         * Si aucun e-mail client n’est disponible,
         * on ne tente pas l’envoi.
         */
        if ($clientEmail === null || $clientEmail === '') {
            $this->logger->warning(
                'Impossible d’envoyer la notification de livraison : aucun e-mail client pour le colis {numeroSuivi}.',
                [
                    'colisId' => $message->getColisId(), 
                    'numeroSuivi' => $message->getNumeroSuivi(), 
                ]
            );

            return;
        }

        $email = (new Email())
            ->from('posteflow@example.test') 
            ->to($clientEmail) 
            ->subject(
                sprintf(
                    'Votre colis %s a été livré', 
                    $message->getNumeroSuivi() 
                )
            )
            ->text(
                sprintf(
                    "Bonjour,\n\nVotre colis %s a bien été livré.\n\nMerci d’utiliser PosteFlow.",
                    $message->getNumeroSuivi() 
                )
            )
            ->html(
                sprintf(
                    '<p>Bonjour,</p>
                    <p>Votre colis <strong>%s</strong> a bien été livré.</p>
                    <p>Merci d’utiliser PosteFlow.</p>',
                    $message->getNumeroSuivi()
                )
            );

        $this->mailer->send($email);

        $this->logger->info(
            'E-mail de livraison envoyé au client pour le colis {numeroSuivi}.',
            [
                'colisId' => $message->getColisId(),
                'numeroSuivi' => $message->getNumeroSuivi(),
                'clientEmail' => $clientEmail,
            ]
        );
    }
}