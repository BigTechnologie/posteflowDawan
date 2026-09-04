<?php 

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:test-mail',
    description: 'Teste l\'envoi d\'un e-mail avec Symfony Mailer.'
)]
class TestMailCommand extends Command
{
    public function __construct(
        private readonly MailerInterface $mailer
    ){
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $email = (new Email())
            ->from('postflow@exemple.test')
            ->to('client@exemple.test')
            ->subject('Test PostFlow - Symfony Mailer')
            ->text('Bonjour, ceci est un test d\'envoi depuis PostFlow avec Symfony Mailer et Mailtrap')
            ->html(
                '<p>Bonjour,</p> <p> ceci est un <strong>test d\'envoi</strong> depuis PostFlow avec Symfony Mailer et Mailtrap</p>'
            );

            $this->mailer->send($email);

            $output->write(
                '<info>E-mail envoyé avec succès vers Mailtrap.</info>'
            );

            return Command::SUCCESS; // Retourne le code de succès
    }

    
}