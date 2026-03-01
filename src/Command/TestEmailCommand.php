<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:test-email', description: 'Teste l\'envoi d\'un email pour vérifier la config SMTP')]
class TestEmailCommand extends Command
{
    public function __construct(private MailerInterface $mailer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Adresse email du destinataire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $to = $input->getArgument('email');

        $io->info("Envoi d'un email test vers : $to");
        $io->info("MAILER_DSN : " . ($_ENV['MAILER_DSN'] ?? 'non défini'));

        $from = $_ENV['MAILER_FROM_ADDRESS'] ?? 'no-reply@educavision.com';

        try {
            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject('Test EducaVision - Configuration email OK')
                ->text('Si vous recevez ceci, la configuration SMTP fonctionne !');

            $this->mailer->send($email);
            $io->success("Email envoyé ! Vérifiez la boîte de réception et les spams de : $to");
        } catch (\Exception $e) {
            $io->error('Erreur : ' . $e->getMessage());
            $io->note('Vérifiez .env.local : MAILER_DSN, utilisez un compte Gmail.com si @esprit.tn ne fonctionne pas.');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
