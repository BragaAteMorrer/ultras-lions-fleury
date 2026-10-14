<?php

namespace App\Tests\Entity;

use App\Entity\PaymentCheckout;
use PHPUnit\Framework\TestCase;

final class PaymentReviewResolutionTest extends TestCase
{
    public function testResolutionRequiresPaymentUnderReview(): void
    {
        $checkout = (new PaymentCheckout())->setStatus('pending');
        $this->expectException(\LogicException::class);
        $checkout->recordManualResolution('refunded', 'sumup-12345');
    }

    public function testResolutionRequiresValidProof(): void
    {
        $checkout = (new PaymentCheckout())->setStatus('paid_stock_review');
        $this->expectException(\InvalidArgumentException::class);
        $checkout->recordManualResolution('refunded', '');
    }

    public function testResolutionIsImmutable(): void
    {
        $checkout = (new PaymentCheckout())->setStatus('paid_stock_review');
        $checkout->recordManualResolution('refunded', 'sumup-12345');
        self::assertSame('refunded', $checkout->getReviewResolution());
        self::assertSame('sumup-12345', $checkout->getReviewReference());
        self::assertNotNull($checkout->getReviewResolvedAt());
        $this->expectException(\LogicException::class);
        $checkout->recordManualResolution('fulfilled', 'delivery-67890');
    }
}
