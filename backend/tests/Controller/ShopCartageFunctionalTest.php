<?php

namespace App\Tests\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\User;
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
}
