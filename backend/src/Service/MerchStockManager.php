<?php

namespace App\Service;

use App\Entity\Merch;
use App\Entity\MerchStock;
use App\Repository\MerchStockRepository;

class MerchStockManager
{
    public function __construct(
        private readonly MerchStockRepository $stockRepository,
    ) {
    }

    public function decreaseStock(
        Merch $merch,
        string $size,
        int $quantity = 1
    ): void {
        $stock = $merch->getStockForSize($size);

        if (!$stock) {
            throw new \LogicException('Stock introuvable pour cette taille.');
        }

        if ($stock->getQuantity() < $quantity) {
            throw new \LogicException('Stock insuffisant.');
        }

        $stock->setQuantity($stock->getQuantity() - $quantity);
    }

    public function findStockBySku(string $sumupSku): ?MerchStock
    {
        return $this->stockRepository->findOneBySumupSku($sumupSku);
    }

    public function decreaseStockBySku(string $sumupSku, int $quantity = 1): MerchStock
    {
        $stock = $this->findStockBySku($sumupSku);

        if (!$stock instanceof MerchStock) {
            throw new \LogicException('Stock introuvable pour ce SKU SumUp.');
        }

        if ($stock->getQuantity() < $quantity) {
            throw new \LogicException('Stock insuffisant.');
        }

        $stock->setQuantity($stock->getQuantity() - $quantity);

        return $stock;
    }
}
