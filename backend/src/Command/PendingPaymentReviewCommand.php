<?php

namespace App\Command;

use App\Entity\PaymentCheckout;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:payments:pending-review', description: 'Liste les paiements payés nécessitant une régularisation manuelle.')]
final class PendingPaymentReviewCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $checkouts = $this->entityManager->getRepository(PaymentCheckout::class)
            ->findBy(['status' => 'paid_stock_review'], ['createdAt' => 'ASC'], 500);

        foreach ($checkouts as $checkout) {
            $output->writeln(sprintf(
                'ID=%d reference=%s type=%s montant=%.2f %s date=%s',
                $checkout->getId(),
                $checkout->getCheckoutReference() ?? '-',
                $checkout->getType() ?? '-',
                $checkout->getAmount(),
                $checkout->getCurrency(),
                $checkout->getCreatedAt()?->format('Y-m-d H:i:s') ?? '-'
            ));
        }
        $output->writeln(sprintf('%d paiements à réconcilier (maximum 500 affichés).', count($checkouts)));
        return Command::SUCCESS;
    }
}
