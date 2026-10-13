<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail,
        private LoggerInterface $logger,
    ) {}

    public function send(string $to, string $subject, string $template, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, 'Ultras Lions Fleury'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (\Throwable $exception) {
            $this->logger->error('Email delivery failed.', [
                'to' => $this->maskEmail($to),
                'subject' => $subject,
                'template' => $template,
                'exception' => $exception::class,
            ]);

            throw $exception;
        }
    }

    private function maskEmail(string $email): string
    {
        $email = trim($email);
        if ($email === '' || !str_contains($email, '@')) {
            return '[invalid]';
        }

        [$localPart, $domain] = explode('@', $email, 2);
        $maskedLocal = substr($localPart, 0, 2) . str_repeat('*', max(0, strlen($localPart) - 2));

        return $maskedLocal . '@' . $domain;
    }
}
