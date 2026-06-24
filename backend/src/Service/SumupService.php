<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class SumupService
{
    private const BASE_URL = 'https://api.sumup.com';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $apiKey,
        private readonly ?string $returnUrl,
        private readonly ?string $merchantCode = null,
        private readonly ?string $webhookSecret = null,
        private readonly ?string $webhookUrl = null,
        private readonly ?LoggerInterface $logger = null,
    ) {}

    public function createHostedCheckout(float $amount, string $currency, string $reference, string $description): array
    {
        if (!$this->apiKey) {
            return [
                '_error' => 'missing_api_key',
                '_message' => 'SUMUP_API_KEY is required to create a checkout.',
                '_status' => 0,
            ];
        }

        if (!$this->merchantCode) {
            return [
                '_error' => 'missing_merchant_code',
                '_message' => 'SUMUP_MERCHANT_CODE is required to create a checkout.',
                '_status' => 0,
            ];
        }

        $keyMode = $this->detectKeyMode($this->apiKey);
        $this->logger?->info('Preparing SumUp hosted checkout.', [
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'merchant_code' => $this->merchantCode,
            'key_mode' => $keyMode,
            'has_return_url' => trim((string) $this->returnUrl) !== '',
            'has_webhook_url' => trim((string) $this->webhookUrl) !== '',
        ]);

        if ((int) round($amount) === 11) {
            $this->logger?->warning('SumUp sandbox refusal test amount detected.', [
                'amount' => $amount,
                'reference' => $reference,
            ]);
        }

        $redirectUrl = trim((string) $this->returnUrl);
        if ($redirectUrl !== '') {
            $sep = str_contains($redirectUrl, '?') ? '&' : '?';
            $redirectUrl .= $sep . 'ref=' . rawurlencode($reference);
        }

        $payload = [
            'amount' => round($amount, 2),
            'currency' => $currency,
            'checkout_reference' => $reference,
            'description' => $description,
            'hosted_checkout' => ['enabled' => true],
            'redirect_url' => $redirectUrl,
            'merchant_code' => $this->merchantCode,
        ];

        $webhookUrl = trim((string) $this->webhookUrl);
        if ($webhookUrl !== '') {
            $payload['return_url'] = $webhookUrl;
        }

        $response = $this->httpClient->request('POST', self::BASE_URL . '/v0.1/checkouts', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $status = $response->getStatusCode();
        $content = $response->getContent(false);
        $data = json_decode($content, true);

        if ($status >= 400) {
            $this->logger?->error('SumUp hosted checkout creation failed.', [
                'status' => $status,
                'reference' => $reference,
                'merchant_code' => $this->merchantCode,
                'key_mode' => $keyMode,
                'response' => $data,
                'raw_response' => $content,
            ]);

            return [
                '_error' => $data['error_code'] ?? $data['type'] ?? 'sumup_error',
                '_message' => $data['message'] ?? $data['title'] ?? 'SumUp error',
                '_details' => $data['detail'] ?? $data['details'] ?? $content,
                '_status' => $status,
            ];
        }

        return is_array($data) ? $data : [];
    }

    public function retrieveCheckout(string $checkoutId): array
    {
        if (!$this->apiKey) {
            return [
                '_error' => 'missing_api_key',
                '_message' => 'SUMUP_API_KEY is required to retrieve a checkout.',
                '_status' => 0,
            ];
        }

        $response = $this->httpClient->request('GET', self::BASE_URL . '/v0.1/checkouts/' . $checkoutId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);

        $status = $response->getStatusCode();
        $content = $response->getContent(false);
        $data = json_decode($content, true);

        if ($status >= 400) {
            return [
                '_error' => $data['error_code'] ?? $data['type'] ?? 'sumup_error',
                '_message' => $data['message'] ?? $data['title'] ?? 'SumUp error',
                '_details' => $data['detail'] ?? $data['details'] ?? $content,
                '_status' => $status,
            ];
        }

        return is_array($data) ? $data : [];
    }

    public function listPaymentMethods(string $checkoutId): array
    {
        if (!$this->apiKey) {
            return [
                '_error' => 'missing_api_key',
                '_message' => 'SUMUP_API_KEY is required to retrieve payment methods.',
                '_status' => 0,
            ];
        }

        $response = $this->httpClient->request('GET', self::BASE_URL . '/v0.1/checkouts/' . $checkoutId . '/payment-methods', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);

        $status = $response->getStatusCode();
        $content = $response->getContent(false);
        $data = json_decode($content, true);

        if ($status >= 400) {
            return [
                '_error' => $data['error_code'] ?? $data['type'] ?? 'sumup_error',
                '_message' => $data['message'] ?? $data['title'] ?? 'SumUp error',
                '_details' => $data['detail'] ?? $data['details'] ?? $content,
                '_status' => $status,
            ];
        }

        return is_array($data) ? $data : [];
    }

    private function detectKeyMode(?string $apiKey): string
    {
        $apiKey = (string) $apiKey;
        if (str_starts_with($apiKey, 'sup_sk_test_') || str_starts_with($apiKey, 'sk_test_')) {
            return 'test';
        }

        if (str_starts_with($apiKey, 'sup_sk_live_') || str_starts_with($apiKey, 'sk_live_')) {
            return 'live';
        }

        return $apiKey !== '' ? 'unknown' : 'missing';
    }

    public function processCheckout(string $checkoutId, string $paymentType, array $personalDetails = []): array
    {
        if (!$this->apiKey) {
            return [
                '_error' => 'missing_api_key',
                '_message' => 'SUMUP_API_KEY is required to process a checkout.',
                '_status' => 0,
            ];
        }

        $payload = [
            'payment_type' => $paymentType,
        ];

        if ($personalDetails) {
            $payload['personal_details'] = $personalDetails;
        }

        $response = $this->httpClient->request('PUT', self::BASE_URL . '/v0.1/checkouts/' . $checkoutId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $status = $response->getStatusCode();
        $content = $response->getContent(false);
        $data = json_decode($content, true);

        if ($status >= 400) {
            return [
                '_error' => $data['error_code'] ?? $data['type'] ?? 'sumup_error',
                '_message' => $data['message'] ?? $data['title'] ?? 'SumUp error',
                '_details' => $data['detail'] ?? $data['details'] ?? $content,
                '_status' => $status,
            ];
        }

        return is_array($data) ? $data : [];
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (!$this->webhookSecret) {
            return true;
        }

        if ($signature === null || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }
}
