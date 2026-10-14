<?php

namespace App\Command;

use App\Entity\PaymentCheckout;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:payments:resolve-review', description: 'Enregistrer la preuve d une régularisation SumUp effectuée manuellement')]
final class ResolvePaymentReviewCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('checkout-id', InputArgument::REQUIRED, 'Identifiant interne du checkout');
        $this->addArgument('resolution', InputArgument::REQUIRED, 'refunded ou fulfilled');
        $this->addArgument('proof-reference', InputArgument::REQUIRED, 'Référence de remboursement SumUp ou de livraison');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = filter_var($input->getArgument('checkout-id'), FILTER_VALIDATE_INT);
        $resolution = (string) $input->getArgument('resolution');
        $proof = trim((string) $input->getArgument('proof-reference'));
        if (!$id || !in_array($resolution, ['refunded', 'fulfilled'], true) || strlen($proof) < 5 || strlen($proof) > 180) {
            $output->writeln('<error>Arguments invalides. Aucune opération effectuée.</error>');
            return Command::INVALID;
        }
        try {
            $this->entityManager->wrapInTransaction(function () use ($id, $resolution, $proof): void {
                $checkout = $this->entityManager->find(PaymentCheckout::class, $id, LockMode::PESSIMISTIC_WRITE);
                if (!$checkout || $checkout->getStatus() !== 'paid_stock_review') {
                    throw new \DomainException('Paiement absent ou non concerné par une revue.');
                }
                $checkout->recordManualResolution($resolution, $proof);
                $this->entityManager->flush();
            });
        } catch (\Throwable $e) {
            $output->writeln('<error>Résolution non enregistrée : '.$e->getMessage().'</error>');
            return Command::FAILURE;
        }
        $output->writeln('<info>Preuve enregistrée. Aucun remboursement SumUp n a été exécuté.</info>');
        return Command::SUCCESS;
    }
}
