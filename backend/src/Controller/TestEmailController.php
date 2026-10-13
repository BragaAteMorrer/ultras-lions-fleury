<?php

namespace App\Controller;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestEmailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function sendTestEmail(
        Request $request,
        MailService $mailService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return new Response("❌ Aucun utilisateur connecté", 403);
        }

        $email = $request->query->get('to') ?? $user->getEmail();

        // Context réel
        $context = [
            'user' => $user
        ];

        $mailService->send(
            to: $email,
            subject: '🔥 Bienvenue chez les Ultras Lions',
            template: 'email/welcome.html.twig',
            context: $context
        );

        return new Response("✅ Email envoyé à : $email");
    }
}
