<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RefreshmentCheckoutSecurityTest extends WebTestCase
{
    public function testUnknownMatchCheckoutCannotStartPayment(): void
    {
        $client = static::createClient();
        $client->request('POST', '/buvette/match/2147483647/commander');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCheckoutRequiresCsrfToken(): void
    {
        $client = static::createClient();
        $client->request('POST', '/buvette/match/2147483647/commander', [
            'email' => 'shop@example.test',
            'quantities' => ['1' => '1'],
        ]);
        self::assertResponseStatusCodeSame(404);
    }

    public function testCheckoutCannotBeStartedWithGet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/buvette/match/2147483647/commander');
        self::assertResponseStatusCodeSame(405);
    }
}
