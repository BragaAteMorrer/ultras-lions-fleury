<?php

namespace App\Tests\Entity;

use App\Entity\RefreshmentOrder;
use PHPUnit\Framework\TestCase;

final class RefreshmentFulfillmentTest extends TestCase
{
    public function testConfirmedOrderCanBePreparedThenCollected(): void
    {
        $order = new RefreshmentOrder();
        $order->markPrepared();
        self::assertNotNull($order->getPreparedAt());
        self::assertNull($order->getCollectedAt());
        $order->markCollected();
        self::assertNotNull($order->getCollectedAt());
    }

    public function testPreparationIsIdempotent(): void
    {
        $order = new RefreshmentOrder();
        $order->markPrepared();
        $first = $order->getPreparedAt();
        $order->markPrepared();
        self::assertSame($first, $order->getPreparedAt());
    }

    public function testCannotCollectUnpreparedOrder(): void
    {
        $this->expectException(\LogicException::class);
        (new RefreshmentOrder())->markCollected();
    }

    public function testCannotCollectTwice(): void
    {
        $order = new RefreshmentOrder();
        $order->markPrepared();
        $order->markCollected();
        $this->expectException(\LogicException::class);
        $order->markCollected();
    }

    public function testCancelledOrderCannotBePrepared(): void
    {
        $order = (new RefreshmentOrder())->setStatus('cancelled');
        $this->expectException(\LogicException::class);
        $order->markPrepared();
    }
}
