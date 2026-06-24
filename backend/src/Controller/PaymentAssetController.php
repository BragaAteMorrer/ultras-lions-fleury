<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentAssetController extends AbstractController
{
    #[Route('/payment/{name}.svg', name: 'payment_logo_asset', requirements: ['name' => '[a-z0-9\-]+'])]
    #[Route('/assets/payment/{name}.svg', name: 'payment_logo_asset_legacy', requirements: ['name' => '[a-z0-9\-]+'])]
    #[Route('/assets/img/payment/{name}.svg', name: 'payment_logo_asset_mapper', requirements: ['name' => '[a-z0-9\-]+'])]
    public function show(string $name): Response
    {
        $fileName = $this->resolveFileName($name);
        $projectDir = $this->getParameter('kernel.project_dir');

        $candidatePaths = [
            $projectDir.'/public/payment/'.$fileName,
            $projectDir.'/public/assets/img/payment/'.$fileName,
            $projectDir.'/assets/img/payment/'.$fileName,
        ];

        foreach ($candidatePaths as $path) {
            if (is_file($path)) {
                return new Response(
                    (string) file_get_contents($path),
                    Response::HTTP_OK,
                    [
                        'Content-Type' => 'image/svg+xml; charset=utf-8',
                        'Cache-Control' => 'public, max-age=86400, immutable',
                        'X-Content-Type-Options' => 'nosniff',
                    ]
                );
            }
        }

        throw new NotFoundHttpException(sprintf('Payment logo "%s" not found.', $name));
    }

    private function resolveFileName(string $name): string
    {
        $aliases = [
            'american-express' => 'amex',
            'americanexpress' => 'amex',
            'amex' => 'amex',
            'apple-pay' => 'apple-pay',
            'applepay' => 'apple-pay',
            'cb' => 'cb',
            'discover' => 'discover',
            'google-pay' => 'google-pay',
            'googlepay' => 'google-pay',
            'jcb' => 'jcb',
            'maestro' => 'maestro',
            'mastercard' => 'mastercard',
            'samsung-pay' => 'samsungpay',
            'samsungpay' => 'samsungpay',
            'union-pay' => 'unionpay',
            'unionpay' => 'unionpay',
            'v-pay' => 'vpay',
            'vpay' => 'vpay',
            'visa' => 'visa',
            'visa-electron' => 'visa-electron',
            'visaelectron' => 'visa-electron',
        ];

        return $aliases[strtolower($name)] ?? strtolower($name);
    }
}
