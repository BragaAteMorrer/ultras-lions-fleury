<?php

namespace App\Tests\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\RefreshmentCategory;
use App\Entity\RefreshmentOrder;
use App\Entity\RefreshmentProduct;
use App\Entity\Ticket;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RefreshmentPaymentWebhookTest extends WebTestCase
{
    public function testVerifiedPaymentCreatesOrdersOnceOnRepeatedWebhook(): void
    {
        $client = static::createClient();
        // Keep the mocked gateway service across repeated HTTP requests.
        $client->disableReboot();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $match = (new Ticket())
            ->setTitle('Match integration')
            ->setOpponent('Test FC')
            ->setMatchDate(new \DateTime('+5 days'))
            ->setPrice(10.0);
        $category = (new RefreshmentCategory())->setName('Boissons')->setActive(true);
        $product = (new RefreshmentProduct())
            ->setName('Eau')->setCategory($category)->setPriceCents(250)->setActive(true);
        $em->persist($match);
        $em->persist($category);
        $em->persist($product);
        $em->flush();

        $ref = 'BUVETTE'.strtoupper(bin2hex(random_bytes(8)));
        $checkoutId = 'sumup-refreshment-'.bin2hex(random_bytes(6));
        $checkout = (new PaymentCheckout())
            ->setType('refreshment')->setStatus('pending')->setCheckoutReference($ref)
            ->setAmount(5.0)->setCurrency('EUR')->setEmail('buyer@example.test')
            ->setSumupCheckoutId($checkoutId)->setCreatedAt(new \DateTime())
            ->setCart([[
                'type' => 'refreshment',
                'ticket_id' => $match->getId(),
                'refreshment_product_id' => $product->getId(),
                'title' => 'Eau',
                'unit_price_cents' => 250,
                'unit_price' => 2.50,
                'quantity' => 2,
                'total' => 5.0,
            ]]);
        $em->persist($checkout);
        $em->flush();

        $gateway = $this->createMock(SumupService::class);
        $gateway->method('retrieveCheckout')->with($checkoutId)->willReturn([
            'id' => $checkoutId,
            'checkout_reference' => $ref,
            'amount' => 5.0,
            'currency' => 'EUR',
            'merchant_code' => 'MERCHANT_TEST',
            'status' => 'PAID',
        ]);
        $gateway->method('matchesMerchantCode')->willReturn(true);
        static::getContainer()->set(SumupService::class, $gateway);

        $payload = json_encode([
            'event_type' => 'CHECKOUT_STATUS_CHANGED',
            'id' => $checkoutId,
        ], JSON_THROW_ON_ERROR);
        for ($i = 0; $i < 2; ++$i) {
            $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);
            self::assertResponseStatusCodeSame(200);
        }

        $em->refresh($checkout);
        self::assertSame('paid', $checkout->getStatus());
        self::assertNotNull($checkout->getProcessedAt());
        $orders = $em->getRepository(RefreshmentOrder::class)->findBy(['paymentCheckout' => $checkout]);
        self::assertCount(1, $orders);
        self::assertSame(2, $orders[0]->getQuantity());
        self::assertSame(250, $orders[0]->getUnitPriceCents());
        self::assertSame('confirmed', $orders[0]->getStatus());
    }
}
