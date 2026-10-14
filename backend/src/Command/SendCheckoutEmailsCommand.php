<?php

namespace App\Command;

use App\Entity\CheckoutEmailDelivery;
use App\Service\MailService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:checkout-emails:send', description: 'Envoie les récapitulatifs de commandes en file persistante')]
final class SendCheckoutEmailsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailService $mailer
    ) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobs = $this->em->getRepository(CheckoutEmailDelivery::class)->findBy(
            ['status' => 'pending'], ['id' => 'ASC'], 20
        );
        $sent = 0;
        foreach ($jobs as $job) {
            $claimed = $this->em->wrapInTransaction(function () use ($job): bool {
                $this->em->lock($job, LockMode::PESSIMISTIC_WRITE);
                $this->em->refresh($job);
                if ($job->getStatus() !== 'pending') { return false; }
                $job->markSending();
                $this->em->flush();
                return true;
            });
            if (!$claimed) { continue; }

            $checkout = $job->getCheckout();
            $to = $checkout->getEmail() ?: $checkout->getUser()?->getUserIdentifier();
            if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $job->markUncertain();
                $this->em->flush();
                $output->writeln('Commande #'.$checkout->getId().' sans destinataire valide.');
                continue;
            }

            $ticketLines = [];
            $merchLines = [];
            $refreshmentLines = [];
            foreach ($checkout->getCart() as $line) {
                $type = (string) ($line['type'] ?? $checkout->getType());
                $view = [
                    'title' => (string) ($line['title'] ?? 'Produit'),
                    'opponent' => $line['opponent'] ?? null,
                    'size' => $line['size'] ?? null,
                    'quantity' => (int) ($line['quantity'] ?? 1),
                    'totalPrice' => (float) ($line['total'] ?? 0),
                ];
                if ($type === 'refreshment') { $refreshmentLines[] = $view; }
                elseif ($type === 'merch') { $merchLines[] = $view; }
                else { $ticketLines[] = $view; }
            }

            try {
                $this->mailer->send(
                    $to,
                    'Récapitulatif de ta commande',
                    'email/checkout_recap.html.twig',
                    [
                        'checkout' => $checkout,
                        'cashNotice' => false,
                        'ticketLines' => $ticketLines,
                        'merchLines' => $merchLines,
                        'refreshmentLines' => $refreshmentLines,
                    ]
                );
                $job->markSent();
                $checkout->setEmailSentAt(new \DateTime());
                $this->em->flush();
                ++$sent;
            } catch (\Throwable) {
                // Delivery may have reached SMTP before a failure. No blind retry.
                $job->markUncertain();
                $this->em->flush();
                $output->writeln('Envoi incertain #'.$checkout->getId().': vérifier manuellement.');
            }
        }
        $output->writeln(sprintf('%d email(s) envoyés.', $sent));
        return Command::SUCCESS;
    }
}
