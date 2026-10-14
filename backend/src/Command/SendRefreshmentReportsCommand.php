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
            if (!$match || $report->getSentAt() !== null || !filter_var($report->getRecipient(), FILTER_VALIDATE_EMAIL)) {
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
            foreach ($orders as $order) {
                $product = $order->getProduct();
                if (!$product) { continue; }
                $id = $product->getId();
                $summary[$id] ??= ['name' => $product->getName(), 'quantity' => 0];
                $summary[$id]['quantity'] += $order->getQuantity();
            }
            $this->mailService->send(
                $report->getRecipient(),
                'Recapitulatif billets et buvette - '.$match->getMatchLabel(),
                'email/refreshment_report.html.twig',
                ['match' => $match, 'ticketQuantity' => $ticketQuantity, 'refreshments' => array_values($summary)]
            );
            $report->setSentAt(new \DateTimeImmutable());
            $this->entityManager->flush();
            ++$sent;
        }
        flock($lock, LOCK_UN);
        fclose($lock);
        $output->writeln(sprintf('%d rapport(s) buvette envoye(s).', $sent));
        return Command::SUCCESS;
    }
}
