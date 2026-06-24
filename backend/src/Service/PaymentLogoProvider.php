<?php

namespace App\Service;

final class PaymentLogoProvider
{
    private ?array $methods = null;

    public function sumupMethods(): array
    {
        if ($this->methods !== null) {
            return $this->methods;
        }

        $definitions = [
            ['type' => 'visa', 'label' => 'Visa', 'file' => 'visa.svg'],
            ['type' => 'visa-electron', 'label' => 'Visa Electron', 'file' => 'visa-electron.svg'],
            ['type' => 'v-pay', 'label' => 'V Pay', 'file' => 'vpay.svg'],
            ['type' => 'amex', 'label' => 'American Express', 'file' => 'amex.svg'],
            ['type' => 'cb', 'label' => 'Carte Bancaire', 'file' => 'cb.svg'],
            ['type' => 'discover', 'label' => 'Discover', 'file' => 'discover.svg'],
            ['type' => 'union-pay', 'label' => 'Union Pay', 'file' => 'unionpay.svg'],
            ['type' => 'mastercard', 'label' => 'Mastercard', 'file' => 'mastercard.svg'],
            ['type' => 'maestro', 'label' => 'Maestro', 'file' => 'maestro.svg'],
            ['type' => 'jcb', 'label' => 'JCB', 'file' => 'jcb.svg'],
            ['type' => 'samsung-pay', 'label' => 'Samsung Pay', 'file' => 'samsungpay.svg'],
            ['type' => 'g-pay', 'label' => 'Google Pay', 'file' => 'google-pay.svg'],
            ['type' => 'apple-pay', 'label' => 'Apple Pay', 'file' => 'apple-pay.svg'],
        ];

        $this->methods = array_map(
            fn (array $definition): array => [
                'type' => $definition['type'],
                'label' => $definition['label'],
                'asset' => $this->toDataUri($definition['file']),
            ],
            $definitions
        );

        return $this->methods;
    }

    private function toDataUri(string $fileName): string
    {
        $projectDir = dirname(__DIR__, 2);
        $candidatePaths = [
            $projectDir.'/public/payment/'.$fileName,
            $projectDir.'/public/assets/img/payment/'.$fileName,
            $projectDir.'/assets/img/payment/'.$fileName,
        ];

        foreach ($candidatePaths as $path) {
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }

            $contents = file_get_contents($path);
            if ($contents === false) {
                continue;
            }

            return 'data:image/svg+xml;base64,'.base64_encode($contents);
        }

        return '';
    }
}
