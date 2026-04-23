<?php

namespace App\Service;

use App\Entity\Merch;

class MerchStockManager
{
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
}
