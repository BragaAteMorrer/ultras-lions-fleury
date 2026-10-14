<?php

namespace App\Tests\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group shop_cartage
 * Runs against an isolated test database; never point DATABASE_URL at production.
 */
final class ShopCartageFunctionalTest extends WebTestCase
{
    private function checkout(?User $owner = null): PaymentCheckout
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $checkout = (new PaymentCheckout())
            ->setType('ticket')
            ->setStatus('paid')
            ->setCheckoutReference(strtoupper(bin2hex(random_bytes(8))))
            ->setAmount(12.50)
            ->setCurrency('EUR')
            ->setCart([])
            ->setCreatedAt(new \DateTime())
            ->setUser($owner);
        $em->persist($checkout);
        $em->flush();

        return $checkout;
    }

    public function testEmptyBasketRendersForGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/panier');
        self::assertResponseIsSuccessful();
    }

    public function testCartageFormRequiresQrScan(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cartage');
        self::assertResponseStatusCodeSame(403);
    }

    public function testReceiptReferenceAloneCannotAuthorizeGuest(): void
    {
        $client = static::createClient();
        $checkout = $this->checkout();
        $client->request('GET', '/merci/pdf?ref='.$checkout->getCheckoutReference());
        self::assertResponseStatusCodeSame(404);
    }

    public function testCashConfirmationReferenceAloneCannotAuthorizeGuest(): void
    {
        $client = static::createClient();
        $checkout = $this->checkout();
        $client->request('GET', '/merci-liquide?ref='.$checkout->getCheckoutReference());
        self::assertResponseStatusCodeSame(404);
    }

    public function testPaymentStatusIsNotExposedToUnrelatedGuest(): void
    {
        $client = static::createClient();
        $checkout = $this->checkout();
        $client->request('GET', '/commande/merci?ref='.$checkout->getCheckoutReference());
        self::assertResponseIsSuccessful();
        self::assertStringNotContainsString($checkout->getCheckoutReference(), $client->getResponse()->getContent() ?: '');
    }

    public function testOtherAuthenticatedUserCannotReadReceipt(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $owner = (new User())->setEmail('receipt-owner-'.bin2hex(random_bytes(6)).'@example.test')->setPassword('unused');
        $other = (new User())->setEmail('receipt-other-'.bin2hex(random_bytes(6)).'@example.test')->setPassword('unused');
        $em->persist($owner);
        $em->persist($other);
        $em->flush();
        $checkout = $this->checkout($owner);
        $client->loginUser($other);
        $client->request('GET', '/merci/pdf?ref='.$checkout->getCheckoutReference());
        self::assertResponseStatusCodeSame(404);
    }
    public function testWebhookRejectsMalformedAndUnknownCheckout(): void
    {
        $client = static::createClient();
        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{}');
        self::assertResponseStatusCodeSame(204);

        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{"event_type":"CHECKOUT_STATUS_CHANGED","id":42}');
        self::assertResponseStatusCodeSame(400);

        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{"event_type":"CHECKOUT_STATUS_CHANGED","id":"missing-checkout"}');
        self::assertResponseStatusCodeSame(404);
    }

    public function testWebhookFailsClosedWhenGatewayVerificationFails(): void
    {
        $client = static::createClient();
        $checkout = $this->checkout();
        $checkout->setSumupCheckoutId('checkout-test-error');
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $sumup = $this->createMock(SumupService::class);
        $sumup->method('retrieveCheckout')->willReturn(['_error' => 'timeout', '_status' => 503]);
        static::getContainer()->set(SumupService::class, $sumup);

        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{"event_type":"CHECKOUT_STATUS_CHANGED","id":"checkout-test-error"}');
        self::assertResponseStatusCodeSame(503);
        static::getContainer()->get(EntityManagerInterface::class)->refresh($checkout);
        self::assertSame('paid', $checkout->getStatus());
        self::assertNull($checkout->getProcessedAt());
    }

    public function testWebhookRejectsMismatchedMerchant(): void
    {
        $client = static::createClient();
        $checkout = $this->checkout();
        $checkout->setSumupCheckoutId('checkout-test-merchant');
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $sumup = $this->createMock(SumupService::class);
        $sumup->method('retrieveCheckout')->willReturn([
            'id' => 'checkout-test-merchant',
            'checkout_reference' => $checkout->getCheckoutReference(),
            'amount' => $checkout->getAmount(),
            'currency' => 'EUR',
            'merchant_code' => 'FOREIGN_MERCHANT',
            'status' => 'PAID',
        ]);
        $sumup->method('matchesMerchantCode')->willReturn(false);
        static::getContainer()->set(SumupService::class, $sumup);

        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{"event_type":"CHECKOUT_STATUS_CHANGED","id":"checkout-test-merchant"}');
        self::assertResponseStatusCodeSame(422);
        static::getContainer()->get(EntityManagerInterface::class)->refresh($checkout);
        self::assertNull($checkout->getProcessedAt());
    }


    public function testReceiptPdfWithMissingReferenceIsNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/merci/pdf');
        self::assertResponseStatusCodeSame(404);
    }

    public function testReceiptPdfWithUnknownReferenceIsNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/merci/pdf?ref=nonexistent-reference');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCashConfirmationWithUnknownReferenceIsNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/merci-liquide?ref=nonexistent-reference');
        self::assertResponseStatusCodeSame(404);
    }

    public function testWebhookIgnoresUnexpectedEventWithoutCreatingOrders(): void
    {
        $client = static::createClient();
        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{"event_type":"UNRELATED_EVENT","id":"unknown-checkout"}');
        self::assertResponseStatusCodeSame(204);
    }

    public function testWebhookRejectsOversizedCheckoutIdentifier(): void
    {
        $client = static::createClient();
        $payload = json_encode(['event_type' => 'CHECKOUT_STATUS_CHANGED', 'id' => str_repeat('x', 129)], JSON_THROW_ON_ERROR);
        $client->request('POST', '/sumup/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);
        self::assertResponseStatusCodeSame(400);
    }

}
