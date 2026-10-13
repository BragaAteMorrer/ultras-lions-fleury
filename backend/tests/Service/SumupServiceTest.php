<?php

namespace App\Tests\Service;

use App\Service\SumupService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class SumupServiceTest extends TestCase
{
    public function testMerchantValidationFailsClosed(): void
    {
        $client = new MockHttpClient();
        $configured = new SumupService($client, 'test-key', null, 'MERCHANT1');

        self::assertTrue($configured->matchesMerchantCode('MERCHANT1'));
        self::assertFalse($configured->matchesMerchantCode('MERCHANT2'));
        self::assertFalse($configured->matchesMerchantCode(''));

        $missing = new SumupService($client, 'test-key', null);
        self::assertFalse($missing->matchesMerchantCode('MERCHANT1'));
    }

    public function testCheckoutIsRetrievedFromSumupRatherThanWebhookPayload(): void
    {
        $client = new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            self::assertSame('GET', $method);
            self::assertSame('https://api.sumup.com/v0.1/checkouts/checkout-1', $url);
            return new MockResponse(json_encode([
                'id' => 'checkout-1',
                'checkout_reference' => 'order-1',
                'amount' => 12.5,
                'currency' => 'EUR',
                'merchant_code' => 'MERCHANT1',
                'status' => 'PAID',
            ], JSON_THROW_ON_ERROR));
        });

        $service = new SumupService($client, 'test-key', null, 'MERCHANT1');
        $response = $service->retrieveCheckout('checkout-1');
        self::assertSame('PAID', $response['status']);
        self::assertSame('order-1', $response['checkout_reference']);
    }

    public function testApiFailureCannotBeMistakenForPaidCheckout(): void
    {
        $client = new MockHttpClient(new MockResponse(
            '{"error_code":"NOT_FOUND","message":"missing"}',
            ['http_code' => 404]
        ));

        $service = new SumupService($client, 'test-key', null, 'MERCHANT1');
        $response = $service->retrieveCheckout('missing');

        self::assertSame(404, $response['_status']);
        self::assertArrayHasKey('_error', $response);
        self::assertArrayNotHasKey('status', $response);
    }
}
