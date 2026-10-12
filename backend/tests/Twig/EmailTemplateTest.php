<?php

namespace App\Tests\Twig;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class EmailTemplateTest extends KernelTestCase
{
    public function testCoreEmailTemplatesRenderWithoutTwigErrors(): void
    {
        self::bootKernel();
        $twig = static::$kernel->getContainer()->get('twig');

        $user = new User();
        $user->setPrenom('Alex');
        $user->setNom('Martin');
        $user->setEmail('alex@example.test');

        $resetHtml = $twig->render('email/reset_password.html.twig', [
            'user' => $user,
            'resetUrl' => 'http://localhost/reset-password/' . str_repeat('a', 64),
        ]);
        self::assertStringContainsString('Reinitialise ton mot de passe', $resetHtml);
        self::assertStringContainsString('http://localhost/reset-password/', $resetHtml);

        $welcomeHtml = $twig->render('email/welcome.html.twig', [
            'user' => $user,
        ]);
        self::assertStringContainsString('Bienvenue', $welcomeHtml);
        self::assertStringContainsString('Acceder au site', $welcomeHtml);

        $accountHtml = $twig->render('email/account_link_request.html.twig', [
            'guardian' => 'Jordan Dupont',
            'acceptUrl' => 'http://localhost/profil/tutelle/accepter/' . str_repeat('b', 64),
        ]);
        self::assertStringContainsString('Demande de gestion de compte', $accountHtml);
        self::assertStringContainsString("Valider l'association", $accountHtml);

        $checkout = (object) [
            'checkoutReference' => 'CHK-123456',
            'createdAt' => new \DateTimeImmutable('2026-08-13 12:00:00'),
            'amount' => 42.5,
            'currency' => 'EUR',
        ];

        $checkoutHtml = $twig->render('email/checkout_recap.html.twig', [
            'checkout' => $checkout,
            'ticketLines' => [
                ['title' => 'Match A', 'opponent' => 'Team B', 'quantity' => 2, 'totalPrice' => 20],
            ],
            'merchLines' => [
                ['title' => 'Maillot', 'size' => 'M', 'quantity' => 1, 'totalPrice' => 22.5],
            ],
            'cashNotice' => true,
        ]);
        self::assertStringContainsString('Recapitulatif de commande', $checkoutHtml);
        self::assertStringContainsString('CHK-123456', $checkoutHtml);

        $adminHtml = $twig->render('email/admin_custom.html.twig', [
            'subject' => 'Info club',
            'body' => '<p>Bonjour</p>',
            'user' => $user,
        ]);
        self::assertStringContainsString('Info club', $adminHtml);
        self::assertStringContainsString('<p>Bonjour</p>', $adminHtml);
    }

    public function testOrderRecapTemplatesRenderWithoutTwigErrors(): void
    {
        self::bootKernel();
        $twig = static::$kernel->getContainer()->get('twig');

        $user = new User();
        $user->setPrenom('Alex');
        $user->setNom('Martin');

        $merchHtml = $twig->render('email/merch_order_recap.html.twig', [
            'user' => $user,
            'order' => [
                'number' => 'MERCH-001',
                'orderedAt' => new \DateTimeImmutable('2026-08-13 12:00:00'),
                'lines' => [
                    ['title' => 'Sweat', 'size' => 'L', 'quantity' => 1, 'totalPrice' => 35],
                ],
                'total' => 35,
                'note' => 'A recuperer au prochain match',
            ],
        ]);
        self::assertStringContainsString('Recapitulatif de commande', $merchHtml);
        self::assertStringContainsString('MERCH-001', $merchHtml);

        $ticketHtml = $twig->render('email/ticket_order_recap.html.twig', [
            'user' => $user,
            'order' => [
                'number' => 'TICKET-001',
                'orderedAt' => new \DateTimeImmutable('2026-08-13 12:00:00'),
                'lines' => [
                    ['title' => 'Match A', 'matchDate' => new \DateTimeImmutable('2026-08-20 19:00:00'), 'opponent' => 'Team B', 'quantity' => 2, 'totalPrice' => 50],
                ],
                'total' => 50,
            ],
        ]);
        self::assertStringContainsString('Recapitulatif de reservation', $ticketHtml);
        self::assertStringContainsString('TICKET-001', $ticketHtml);
    }
}
