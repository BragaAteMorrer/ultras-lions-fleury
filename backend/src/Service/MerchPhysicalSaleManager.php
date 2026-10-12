<?php

namespace App\Service;

use App\Entity\Merch;
use App\Entity\MerchOrder;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MerchPhysicalSaleManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MerchStockManager $stockManager,
    ) {
    }

    public function recordSale(
        Merch $merch,
        string $size,
        int $quantity,
        string $paymentMethod,
        bool $useMemberPrice = false,
        ?string $sumupReference = null,
        ?string $note = null,
        ?User $confirmedBy = null,
        ?\DateTimeInterface $createdAt = null,
        ?float $unitPrice = null,
    ): MerchOrder {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->stockManager->decreaseStock($merch, $size, $quantity);

            $order = new MerchOrder();
            $now = $createdAt ?? new \DateTimeImmutable();
            $effectiveUnitPrice = $unitPrice ?? $merch->getPriceForUser($useMemberPrice);

            $order->setMerch($merch)
                ->setSize($size)
                ->setQuantity($quantity)
                ->setUnitPrice($effectiveUnitPrice)
                ->setTotalPrice(round($effectiveUnitPrice * $quantity, 2))
                ->setPaymentMethod($paymentMethod)
                ->setSumupReference(trim((string) $sumupReference) !== '' ? trim((string) $sumupReference) : null)
                ->setNote(trim((string) $note) !== '' ? trim((string) $note) : null)
                ->setCreatedAt($now)
                ->setConfirmedAt($now)
                ->setConfirmedBy($confirmedBy)
                ->setExecuted(true);

            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $connection->commit();

            return $order;
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }
}
