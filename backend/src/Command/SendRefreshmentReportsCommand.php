<?php

namespace App\Command;

use App\Entity\RefreshmentOrder;
use App\Entity\RefreshmentReport;
use App\Entity\TicketOrder;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:buvette:send-reports', description: 'Envoyer les precommandes buvette 24h avant le match')]
final class SendRefreshmentReportsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailService $mailService,
    ) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // LWS runs this on a single host: use an OS-level lock to prevent
        // overlapping cron invocations from emailing the same report twice.
        $lock = fopen(sys_get_temp_dir().'/ultras-lions-buvette-report.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
            $output->writeln('Un autre envoi buvette est deja en cours.');
            if (is_resource($lock)) { fclose($lock); }
            return Command::SUCCESS;
        }

        $now = new \DateTimeImmutable();
        $sent = 0;
        foreach ($this->entityManager->getRepository(RefreshmentReport::class)->findAll() as $report) {
            $match = $report->getTicket();
            if (!$match || ($report->getSentAt() !== null || $report->getDeliveryStatus() !== 'pending') || !filter_var($report->getRecipient(), FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $matchDate = $match->getMatchDate();
            if (!$matchDate) { continue; }
            $start = \DateTimeImmutable::createFromInterface($matchDate)->modify('-24 hours');
            // Run hourly via LWS cron. Catch up after downtime until the match begins.
            if ($now < $start || $now >= $matchDate) { continue; }

            $tickets = $this->entityManager->getRepository(TicketOrder::class)->findBy(['ticket' => $match, 'paid' => true]);
            $ticketQuantity = array_sum(array_map(static fn (TicketOrder $row): int => $row->getQuantity(), $tickets));
            $orders = $this->entityManager->getRepository(RefreshmentOrder::class)->findBy(['ticket' => $match, 'status' => 'confirmed']);
            $summary = [];
            $customerOrders = [];
            foreach ($orders as $order) {
                // A confirmed pre-order must belong to a settled checkout.
                $checkout = $order->getPaymentCheckout();
                if (!$checkout || $checkout->getStatus() !== 'paid') { continue; }
                $reference = $checkout->getCheckoutReference() ?: 'CHECKOUT-'.$checkout->getId();
                $customerOrders[$reference] ??= [
                    'reference' => $reference,
                    'name' => trim((string) $checkout->getCustomerFirstName().' '.(string) $checkout->getCustomerLastName()),
                    'lines' => [],
                ];
                $customerOrders[$reference]['lines'][] = [
                    'product' => $order->getProduct()?->getName() ?? 'Produit',
                    'quantity' => $order->getQuantity(),
                ];
                $product = $order->getProduct();
                if (!$product) { continue; }
                $id = $product->getId();
                $summary[$id] ??= ['name' => $product->getName(), 'quantity' => 0];
                $summary[$id]['quantity'] += $order->getQuantity();
            }
            // Persist an immutable snapshot and the intent BEFORE contacting SMTP.
            // An interrupted 'sending' remains pending manual reconciliation: never
            // retry blindly because SMTP acknowledgement can be lost after delivery.
            $snapshot = [
                'ticketQuantity' => $ticketQuantity,
                'refreshments' => array_values($summary),
                'customerOrders' => array_values($customerOrders),
                'recipient' => $report->getRecipient(),
                'matchId' => $match->getId(),
            ];
            $key = hash('sha256', 'buvette-report:'.$report->getId());
            $report->markAttempt($key, $snapshot);
            $this->entityManager->flush();
            try {
                $this->mailService->send(
                    $snapshot['recipient'],
                    'Recapitulatif billets et buvette - '.$match->getMatchLabel(),
                    'email/refreshment_report.html.twig',
                    ['match' => $match, 'ticketQuantity' => $snapshot['ticketQuantity'], 'refreshments' => $snapshot['refreshments'], 'customerOrders' => $snapshot['customerOrders']]
                );
                $report->setSentAt(new \DateTimeImmutable());
                $report->setDeliveryStatus('sent');
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                // Unknown outcome: SMTP may already have accepted the message.
                $report->setDeliveryStatus('uncertain');
                $this->entityManager->flush();
                $output->writeln(sprintf('<error>Envoi #%d incertain : verification manuelle requise.</error>', $report->getId()));
                continue;
            }
            ++$sent;
        }
        flock($lock, LOCK_UN);
        fclose($lock);
        $output->writeln(sprintf('%d rapport(s) buvette envoye(s).', $sent));
        return Command::SUCCESS;
    }
}
