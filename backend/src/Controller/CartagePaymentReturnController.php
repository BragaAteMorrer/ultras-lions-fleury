<?php

namespace App\Controller;

use App\Entity\CartageRegistration;
use App\Entity\PaymentCheckout;
use App\Repository\CartageRegistrationRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Repository\UserRepository;
use App\Service\CartageProfileCreator;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartagePaymentReturnController extends AbstractController
{
    #[Route('/merci', name: 'cartage_payment_return', methods: ['GET'])]
    public function __invoke(
        Request $request,
        CartageRegistrationRepository $registrationRepository,
        PaymentCheckoutRepository $checkoutRepository,
        UserRepository $userRepository,
        SumupService $sumupService,
        CartageProfileCreator $profileCreator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        Security $security,
        SessionInterface $session,
    ): Response {
        $paymentConfirmed = false;
        $registrationFound = false;
        $profileUrl = null;
        $paymentStatus = 'UNKNOWN';
        $paymentStatusLabel = 'Paiement en attente';
        $paymentStatusMessage = 'Nous vérifions ton paiement avec SumUp.';
        $reference = trim((string) (
            $request->query->get('ref')
            ?? $request->query->get('checkout_reference')
            ?? ''
        ));
        $checkoutId = trim((string) (
            $request->query->get('checkout_id')
            ?? $request->query->get('id')
            ?? ''
        ));

        $checkout = $this->findCheckout($checkoutRepository, $reference, $checkoutId);
        if ($checkout instanceof PaymentCheckout) {
            $reference = (string) ($checkout->getCheckoutReference() ?: $reference);
            $checkoutId = (string) ($checkout->getSumupCheckoutId() ?: $checkoutId);
        }

        if ($reference === '') {
            $reference = trim((string) $session->get('cartage_last_checkout_reference', ''));
        }

        if ($checkoutId === '') {
            $checkoutId = trim((string) $session->get('cartage_last_checkout_id', ''));
        }

        $logger->info('Cartage payment return received.', [
            'checkout_reference' => $reference,
            'checkout_id' => $checkoutId,
            'request_query' => $request->query->all(),
        ]);

        $registration = $this->findRegistration($registrationRepository, $reference, $checkoutId, $checkout);

        if ($registration instanceof CartageRegistration) {
            $registrationFound = true;
            $reference = (string) ($registration->getCheckoutReference() ?: $reference);
            $checkoutId = (string) ($registration->getSumupCheckoutId() ?: $checkoutId);

            try {
                $checkoutState = $this->resolveCheckoutState($checkout, $checkoutId, $sumupService, $logger);
                $paymentConfirmed = $registration->getStatus() === CartageRegistration::STATUS_PAID_ONLINE
                    || $checkoutState['confirmed'];
                $paymentStatus = $checkoutState['status'];
                $paymentStatusLabel = $checkoutState['label'];
                $paymentStatusMessage = $checkoutState['message'];
            } catch (\Throwable $exception) {
                $logger->error('Unable to confirm cartage payment on return.', [
                    'cartage_registration_id' => $registration->getId(),
                    'checkout_reference' => $reference,
                    'checkout_id' => $checkoutId,
                    'exception' => $exception,
                ]);

                $paymentConfirmed = $registration->getStatus() === CartageRegistration::STATUS_PAID_ONLINE;
                $paymentStatus = 'ERROR';
                $paymentStatusLabel = 'Paiement indisponible';
                $paymentStatusMessage = 'Nous n avons pas pu verifier le paiement pour le moment.';
            }

            if ($paymentConfirmed) {
                $this->markRegistrationPaid($registration, $checkout, $entityManager, $logger, $reference, $checkoutId);
                $profileUrl = $this->finalizeProfile(
                    $registration,
                    $checkout,
                    $userRepository,
                    $profileCreator,
                    $entityManager,
                    $logger,
                    $security,
                    $reference,
                    $checkoutId
                );

                if ($profileUrl !== null) {
                    return $this->redirect($profileUrl);
                }
            }
        }

        return $this->render('cartage/thankyou.html.twig', [
            'paymentConfirmed' => $paymentConfirmed,
            'registrationFound' => $registrationFound,
            'reference' => $reference,
            'profileUrl' => $profileUrl,
            'checkoutId' => $checkoutId,
            'paymentStatus' => $paymentStatus,
            'paymentStatusLabel' => $paymentStatusLabel,
            'paymentStatusMessage' => $paymentStatusMessage,
        ]);
    }

    private function markRegistrationPaid(
        CartageRegistration $registration,
        ?PaymentCheckout $checkout,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        string $reference,
        string $checkoutId,
    ): void {
        try {
            if ($checkout instanceof PaymentCheckout && $checkout->getStatus() !== 'paid') {
                $checkout->setStatus('paid');
                $checkout->setPaidAt(new \DateTime());
            }

            if ($registration->getStatus() !== CartageRegistration::STATUS_PAID_ONLINE) {
                $registration->markPaidOnline();
            }

            $entityManager->flush();
        } catch (\Throwable $exception) {
            $logger->error('Unable to persist paid cartage status on return.', [
                'cartage_registration_id' => $registration->getId(),
                'checkout_reference' => $reference,
                'checkout_id' => $checkoutId,
                'exception' => $exception,
            ]);
        }
    }

    private function finalizeProfile(
        CartageRegistration $registration,
        ?PaymentCheckout $checkout,
        UserRepository $userRepository,
        CartageProfileCreator $profileCreator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        Security $security,
        string $reference,
        string $checkoutId,
    ): ?string {
        $profileUrl = null;

        try {
            $user = $profileCreator->createProfileIfNeeded($registration);
            if ($user !== null) {
                if ($checkout instanceof PaymentCheckout && $checkout->getUser() === null) {
                    $checkout->setUser($user);
                }

                $entityManager->flush();

                if ($user->getId() !== null) {
                    $profileUrl = $this->generateUrl('profile_show', ['id' => $user->getId()]);
                }

                try {
                    $security->login($user);

                    return $profileUrl;
                } catch (\Throwable $exception) {
                    $logger->warning('Unable to auto-login cartage user after payment return.', [
                        'cartage_registration_id' => $registration->getId(),
                        'checkout_reference' => $reference,
                        'exception' => $exception,
                    ]);
                }
            }
        } catch (\Throwable $exception) {
            $logger->error('Unable to finalize cartage profile after payment return.', [
                'cartage_registration_id' => $registration->getId(),
                'checkout_reference' => $reference,
                'checkout_id' => $checkoutId,
                'exception' => $exception,
            ]);
        }

        if ($profileUrl !== null) {
            return $profileUrl;
        }

        try {
            $user = $userRepository->findOneByEmail($registration->getEmail());
            if ($user !== null && $user->getId() !== null) {
                return $this->generateUrl('profile_show', ['id' => $user->getId()]);
            }
        } catch (\Throwable $exception) {
            $logger->warning('Unable to find cartage user after paid return.', [
                'cartage_registration_id' => $registration->getId(),
                'checkout_reference' => $reference,
                'exception' => $exception,
            ]);
        }

        return null;
    }

    private function findCheckout(PaymentCheckoutRepository $checkoutRepository, string $reference, string $checkoutId): ?PaymentCheckout
    {
        if ($reference !== '') {
            $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $reference]);
            if ($checkout instanceof PaymentCheckout) {
                return $checkout;
            }
        }

        if ($checkoutId !== '') {
            $checkout = $checkoutRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);
            if ($checkout instanceof PaymentCheckout) {
                return $checkout;
            }
        }

        return null;
    }

    private function findRegistration(
        CartageRegistrationRepository $registrationRepository,
        string $reference,
        string $checkoutId,
        ?PaymentCheckout $checkout,
    ): ?CartageRegistration {
        if ($reference !== '') {
            $registration = $registrationRepository->findOneBy(['checkoutReference' => $reference]);
            if ($registration instanceof CartageRegistration) {
                return $registration;
            }
        }

        if ($checkoutId !== '') {
            $registration = $registrationRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);
            if ($registration instanceof CartageRegistration) {
                return $registration;
            }
        }

        if ($checkout instanceof PaymentCheckout) {
            foreach ($checkout->getCart() as $line) {
                $registrationId = (int) ($line['cartage_registration_id'] ?? 0);
                if ($registrationId <= 0) {
                    continue;
                }

                $registration = $registrationRepository->find($registrationId);
                if ($registration instanceof CartageRegistration) {
                    return $registration;
                }
            }
        }

        return null;
    }

    /**
     * @return array{confirmed: bool, status: string, label: string, message: string}
     */
    private function resolveCheckoutState(
        ?PaymentCheckout $checkout,
        string $checkoutId,
        SumupService $sumupService,
        LoggerInterface $logger,
    ): array {
        if ($checkout instanceof PaymentCheckout && $checkout->getStatus() === 'paid') {
            return [
                'confirmed' => true,
                'status' => 'PAID',
                'label' => 'Paiement confirme',
                'message' => 'Le paiement a deja ete confirme par le systeme.',
            ];
        }

        $checkoutId = $checkout instanceof PaymentCheckout
            ? (string) $checkout->getSumupCheckoutId()
            : $checkoutId;

        if ($checkoutId === '') {
            return [
                'confirmed' => false,
                'status' => 'UNKNOWN',
                'label' => 'Paiement non verifie',
                'message' => 'Aucun identifiant SumUp n a ete fourni au retour.',
            ];
        }

        $details = $sumupService->retrieveCheckout($checkoutId);
        $logger->info('Cartage checkout status retrieved from SumUp.', [
            'checkout_id' => $checkoutId,
            'details' => $details,
        ]);

        if (!empty($details['_error'])) {
            $logger->warning('Unable to confirm cartage checkout on return.', [
                'checkout_id' => $checkoutId,
                'sumup_error' => $details['_error'],
                'sumup_message' => $details['_message'] ?? null,
            ]);

            return [
                'confirmed' => false,
                'status' => 'ERROR',
                'label' => 'Paiement indisponible',
                'message' => trim((string) ($details['_message'] ?? 'Une erreur SumUp a empeche la verification du paiement.')),
            ];
        }

        $status = strtoupper((string) ($details['status'] ?? ''));
        $confirmed = in_array($status, ['PAID', 'SUCCESSFUL', 'COMPLETED'], true);

        return [
            'confirmed' => $confirmed,
            'status' => $status !== '' ? $status : 'UNKNOWN',
            'label' => $this->getPaymentStatusLabel($status),
            'message' => $this->getPaymentStatusMessage($status),
        ];
    }

    private function getPaymentStatusLabel(string $status): string
    {
        return match (strtoupper($status)) {
            'PAID', 'SUCCESSFUL', 'COMPLETED' => 'Paiement confirme',
            'PENDING', 'WAITING' => 'Paiement en attente',
            'FAILED', 'DECLINED', 'CANCELLED', 'CANCELED', 'EXPIRED' => 'Paiement refuse',
            'ERROR' => 'Paiement indisponible',
            default => 'Statut SumUp: '.($status !== '' ? strtoupper($status) : 'INCONNU'),
        };
    }

    private function getPaymentStatusMessage(string $status): string
    {
        return match (strtoupper($status)) {
            'PAID', 'SUCCESSFUL', 'COMPLETED' => 'Ton paiement a bien ete confirme par SumUp.',
            'PENDING', 'WAITING' => 'Le paiement est encore en cours de traitement chez SumUp.',
            'FAILED', 'DECLINED', 'CANCELLED', 'CANCELED', 'EXPIRED' => 'SumUp a refuse ou expire le paiement. Reessaie avec une autre carte ou un autre essai sandbox.',
            'ERROR' => 'Nous n avons pas pu verifier le paiement pour le moment.',
            default => 'Le retour SumUp a ete recu, mais le statut exact est inhabituel.',
        };
    }
}
