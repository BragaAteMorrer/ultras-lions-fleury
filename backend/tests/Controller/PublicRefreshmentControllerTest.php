<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PublicRefreshmentControllerTest extends WebTestCase
{
    public function testUnknownMatchCannotExposeCatalogue(): void
    {
        $client = static::createClient();
        $client->request('GET', '/buvette/match/2147483647');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCatalogueIsReadOnly(): void
    {
        $client = static::createClient();
        $client->request('POST', '/buvette/match/2147483647');
        self::assertResponseStatusCodeSame(405);
    }
}
