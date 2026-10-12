<?php

namespace App\Service;

use App\Entity\Donation;
use App\Entity\PaymentCheckout;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DonationPaymentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailService $mailService,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function createCheckout(
        Donation $donation,
        ?User $user,
        string $reference
    ): PaymentCheckout {
        $checkout = $donation->getPaymentCheckout() ?? new PaymentCheckout();

        $checkout->setType('donation');
        $checkout->setStatus('pending');
        $checkout->setCheckoutReference($reference);
        $checkout->setAmount($donation->getAmount());
        $checkout->setCurrency($donation->getCurrency());
        $checkout->setUser($user);
        $checkout->setEmail($donation->getDonorEmail());
        $checkout->setCustomerFirstName($donation->getDonorFirstName());
        $checkout->setCustomerLastName($donation->getDonorLastName());
        $checkout->setCart([
            [
                'type' => 'donation',
                'amount' => $donation->getAmount(),
                'currency' => $donation->getCurrency(),
                'anonymous' => $donation->isAnonymous(),
                'message' => $donation->getMessage(),
            ],
        ]);
        $checkout->setCreatedAt(new \DateTime());
        $checkout->setPaidAt(null);
        $checkout->setProcessedAt(null);
        $checkout->setEmailSentAt(null);

        $donation->setPaymentCheckout($checkout);
        $donation->setUser($user);
        $donation->setStatus(Donation::STATUS_PENDING);

        $this->entityManager->persist($checkout);
        $this->entityManager->persist($donation);

        return $checkout;
    }

    /**
     * @param array<string, mixed> $details
     */
    public function synchronizeFromGateway(Donation $donation, PaymentCheckout $checkout, array $details): array
    {
        if ($checkout->getProcessedAt() !== null && $donation->isPaid()) {
            return [
                'confirmed' => true,
                'status' => 'PAID',
                'label' => 'donation.payment_state.confirmed',
                'message' => 'donation.payment_state.confirmed_message',
            ];
        }

        $status = strtoupper((string) ($details['status'] ?? ''));
        $amount = $this->extractAmount($details, $checkout);
        $currency = strtoupper((string) ($details['currency'] ?? $checkout->getCurrency()));

        $isSuccess = in_array($status, ['PAID', 'SUCCESSFUL', 'COMPLETED'], true);
        $isFailure = in_array($status, ['FAILED', 'DECLINED', 'CANCELLED', 'CANCELED', 'EXPIRED'], true);
        $matchesAmount = abs($amount - $checkout->getAmount()) < 0.01;
        $matchesCurrency = $currency === '' || $currency === strtoupper($checkout->getCurrency());

        if ($isSuccess && $matchesAmount && $matchesCurrency) {
            $donation->markPaid();
            $checkout->setStatus('paid');
            $checkout->setPaidAt($checkout->getPaidAt() ?? new \DateTime());
            $checkout->setProcessedAt($checkout->getProcessedAt() ?? new \DateTime());
            $this->entityManager->flush();

            return [
                'confirmed' => true,
                'status' => $status !== '' ? $status : 'PAID',
                'label' => 'donation.payment_state.confirmed',
                'message' => 'donation.payment_state.confirmed_message',
            ];
        }

        if ($isSuccess && (!$matchesAmount || !$matchesCurrency)) {
            $this->logger->warning('Donation payment amount mismatch.', [
                'donation_id' => $donation->getId(),
                'checkout_reference' => $checkout->getCheckoutReference(),
                'checkout_amount' => $checkout->getAmount(),
                'gateway_amount' => $amount,
                'checkout_currency' => $checkout->getCurrency(),
                'gateway_currency' => $currency,
                'status' => $status,
            ]);

            $donation->markFailed();
            $checkout->setStatus('failed');
            $checkout->setProcessedAt($checkout->getProcessedAt() ?? new \DateTime());
            $this->entityManager->flush();

            return [
                'confirmed' => false,
                'status' => 'AMOUNT_MISMATCH',
                'label' => 'donation.payment_state.amount_mismatch',
                'message' => 'donation.payment_state.amount_mismatch_message',
            ];
        }

        if ($isFailure) {
            if ($status === 'CANCELLED' || $status === 'CANCELED') {
                $donation->markCancelled();
                $checkout->setStatus('cancelled');
            } else {
                $donation->markFailed();
                $checkout->setStatus('failed');
            }

            $checkout->setProcessedAt($checkout->getProcessedAt() ?? new \DateTime());
            $this->entityManager->flush();

            return [
                'confirmed' => false,
                'status' => $status !== '' ? $status : 'FAILED',
                'label' => $status === 'CANCELLED' || $status === 'CANCELED' ? 'donation.payment_state.cancelled' : 'donation.payment_state.failed',
                'message' => $status === 'CANCELLED' || $status === 'CANCELED' ? 'donation.payment_state.cancelled_message' : 'donation.payment_state.failed_message',
            ];
        }

        $checkout->setStatus('pending');
        $this->entityManager->flush();

        return [
            'confirmed' => false,
            'status' => $status !== '' ? $status : 'PENDING',
            'label' => 'donation.payment_state.pending',
            'message' => 'donation.payment_state.pending_message',
        ];
    }

    public function sendConfirmationMail(Donation $donation, PaymentCheckout $checkout): bool
    {
        if ($checkout->getEmailSentAt() !== null || !$donation->isPaid()) {
            return false;
        }

        $recipient = trim((string) ($donation->getDonorEmail() ?: $checkout->getEmail() ?: $donation->getUser()?->getUserIdentifier() ?: $checkout->getUser()?->getUserIdentifier() ?: ''));
        if ($recipient === '') {
            return false;
        }

        try {
            $this->mailService->send(
                to: $recipient,
                subject: $this->translator->trans('email.donation_recap.subject'),
                template: 'email/donation_recap.html.twig',
                context: [
                    'donation' => $donation,
                    'checkout' => $checkout,
                ]
            );

            $checkout->setEmailSentAt(new \DateTime());
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error('Donation confirmation email failed.', [
                'donation_id' => $donation->getId(),
                'checkout_reference' => $checkout->getCheckoutReference(),
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $details
     */
    private function extractAmount(array $details, PaymentCheckout $checkout): float
    {
        foreach (['amount', 'total_amount', 'gross_amount'] as $key) {
            if (!array_key_exists($key, $details) || $details[$key] === null || $details[$key] === '') {
                continue;
            }

            return round((float) $details[$key], 2);
        }

        return round($checkout->getAmount(), 2);
    }
}
