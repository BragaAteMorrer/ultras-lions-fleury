<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Form\DonationFormData;
use App\Form\DonationType;
use App\Repository\DonationRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Service\DonationConfig;
use App\Service\DonationPaymentService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/boutique/don')]
final class DonationController extends AbstractController
{
    public function __construct(
        private readonly DonationConfig $donationConfig,
        private readonly DonationPaymentService $paymentService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'donation_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        DonationRepository $donationRepository,
        PaymentCheckoutRepository $checkoutRepository,
        SumupService $sumupService,
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        LoggerInterface $logger,
    ): Response {
        $resumeDonation = $this->findDonationFromReference((string) $request->query->get('ref', ''), $donationRepository, $checkoutRepository);
        $user = $this->getUser() instanceof User ? $this->getUser() : null;
        $data = $this->buildInitialData($user, $resumeDonation);

        $submissionToken = $session->get('donation_form_token');
        if (!is_string($submissionToken) || $submissionToken === '') {
            $submissionToken = bin2hex(random_bytes(16));
            $session->set('donation_form_token', $submissionToken);
        }
        $data->submissionToken = $submissionToken;

        $form = $this->createForm(DonationType::class, $data);

        $form->get('submissionToken')->setData($submissionToken);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedToken = (string) $data->submissionToken;
            $sessionToken = (string) $session->get('donation_form_token', '');
            if ($submittedToken === '' || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
                $this->addFlash('warning', $this->translator->trans('donation.flash.invalid_submission'));

                return $this->redirectToRoute('donation_index');
            }

            $donation = $resumeDonation instanceof Donation ? $resumeDonation : new Donation();
            if (!$resumeDonation instanceof Donation) {
                $entityManager->persist($donation);
            }

            $amount = $this->resolveAmount($data, $form, $donation);
            if ($amount === null) {
                return $this->renderDonationPage($form, $resumeDonation, $session);
            }

            $donation
                ->setAmount($amount)
                ->setCurrency($this->donationConfig->getCurrency())
                ->setDonorFirstName($data->firstName)
                ->setDonorLastName($data->lastName)
                ->setDonorEmail($data->email)
                ->setAnonymous((bool) $data->anonymous)
                ->setMessage($data->message)
                ->setUser($user);

            $reference = strtoupper(bin2hex(random_bytes(6)));
            $checkout = $this->paymentService->createCheckout($donation, $user, $reference);

            $response = $sumupService->createHostedCheckout(
                $donation->getAmount(),
                $donation->getCurrency(),
                $reference,
                $this->translator->trans('donation.payment_description', [
                    '%amount%' => number_format($donation->getAmount(), 2, ',', ' '),
                ]),
                $this->generateUrl('donation_thankyou', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );

            if (!empty($response['_error'])) {
                $donation->markFailed();
                $checkout->setStatus('failed');
                $entityManager->flush();

                $this->addFlash('danger', $this->translator->trans('donation.flash.sumup_error', [
                    '%message%' => (string) ($response['_message'] ?? 'SumUp error'),
                ]));

                return $this->renderDonationPage($form, $donation, $session);
            }

            $checkoutId = (string) ($response['id'] ?? '');
            $hostedUrl = (string) ($response['hosted_checkout_url'] ?? '');
            if ($checkoutId === '' || $hostedUrl === '') {
                $donation->markFailed();
                $checkout->setStatus('failed');
                $entityManager->flush();

                $this->addFlash('danger', $this->translator->trans('donation.flash.checkout_creation_failed'));

                return $this->renderDonationPage($form, $donation, $session);
            }

            $checkout->setSumupCheckoutId($checkoutId);
            $entityManager->flush();

            $session->set('donation_last_submission_token', $submittedToken);
            $session->set('donation_last_hosted_url', $hostedUrl);
            $session->set('donation_last_reference', $reference);
            $session->set('donation_last_checkout_id', $checkoutId);

            return $this->redirect($hostedUrl);
        }

        return $this->renderDonationPage($form, $resumeDonation, $session);
    }

    #[Route('/merci', name: 'donation_thankyou', methods: ['GET'])]
    public function thankYou(
        Request $request,
        DonationRepository $donationRepository,
        PaymentCheckoutRepository $checkoutRepository,
        SumupService $sumupService,
        SessionInterface $session,
        LoggerInterface $logger,
    ): Response {
        $context = $this->resolveDonationContext($request, $donationRepository, $checkoutRepository);
        $donation = $context['donation'];
        $checkout = $context['checkout'];
        $status = 'UNKNOWN';
        $paymentStatusLabel = $this->translator->trans('donation.payment_state.pending');
        $paymentStatusMessage = $this->translator->trans('donation.payment_state.pending_message');

        if (!$donation instanceof Donation || !$checkout instanceof PaymentCheckout) {
            return $this->render('donation/status.html.twig', [
                'statusTitle' => $this->translator->trans('donation.status.not_found_title'),
                'statusMessage' => $this->translator->trans('donation.status.not_found_message'),
                'retryUrl' => $this->generateUrl('donation_index'),
                'homeUrl' => $this->generateUrl('home'),
            ]);
        }

        if ($checkout->getStatus() !== 'paid' && $checkout->getSumupCheckoutId()) {
            $details = $sumupService->retrieveCheckout($checkout->getSumupCheckoutId());
            if (!empty($details['_error'])) {
                $logger->warning('Donation checkout could not be refreshed from SumUp.', [
                    'donation_id' => $donation->getId(),
                    'checkout_id' => $checkout->getSumupCheckoutId(),
                    'error' => $details['_error'],
                ]);

                $status = 'ERROR';
                $paymentStatusLabel = $this->translator->trans('donation.payment_state.unavailable');
                $paymentStatusMessage = $this->translator->trans('donation.payment_state.unavailable_message');
            } else {
                $result = $this->paymentService->synchronizeFromGateway($donation, $checkout, $details);
                $status = (string) ($result['status'] ?? 'UNKNOWN');
                $paymentStatusLabel = $this->translator->trans((string) ($result['label'] ?? 'donation.payment_state.pending'));
                $paymentStatusMessage = $this->translator->trans((string) ($result['message'] ?? 'donation.payment_state.pending_message'));
            }
        } else {
            $status = strtoupper((string) $checkout->getStatus());
            $paymentStatusLabel = $this->translator->trans($donation->getStatusTranslationKey());
            $paymentStatusMessage = $this->translator->trans('donation.payment_state.confirmed_message');
        }

        if ($donation->isPaid()) {
            $this->paymentService->sendConfirmationMail($donation, $checkout);
            $session->remove('donation_last_submission_token');
            $session->remove('donation_last_hosted_url');
            $session->remove('donation_last_reference');
            $session->remove('donation_last_checkout_id');

            return $this->render('donation/thankyou.html.twig', [
                'donation' => $donation,
                'checkout' => $checkout,
                'paymentStatus' => $status,
                'paymentStatusLabel' => $this->translator->trans('donation.payment_state.confirmed'),
                'paymentStatusMessage' => $this->translator->trans('donation.payment_state.confirmed_message'),
            ]);
        }

        if (in_array($donation->getStatus(), [Donation::STATUS_CANCELLED, Donation::STATUS_FAILED], true) || in_array($status, ['FAILED', 'DECLINED', 'EXPIRED'], true)) {
            $route = $donation->getStatus() === Donation::STATUS_CANCELLED || $status === 'CANCELLED' || $status === 'CANCELED'
                ? 'donation_cancelled'
                : 'donation_failed';

            return $this->redirectToRoute($route, [
                'ref' => $donation->getPaymentReference() ?? (string) $request->query->get('ref', ''),
            ]);
        }

        return $this->render('donation/thankyou.html.twig', [
            'donation' => $donation,
            'checkout' => $checkout,
            'paymentStatus' => $status,
            'paymentStatusLabel' => $paymentStatusLabel,
            'paymentStatusMessage' => $paymentStatusMessage,
        ]);
    }

    #[Route('/annule', name: 'donation_cancelled', methods: ['GET'])]
    public function cancelled(
        Request $request,
        DonationRepository $donationRepository,
        PaymentCheckoutRepository $checkoutRepository,
    ): Response {
        $context = $this->resolveDonationContext($request, $donationRepository, $checkoutRepository);
        $donation = $context['donation'];

        return $this->render('donation/status.html.twig', [
            'donation' => $donation,
            'checkout' => $context['checkout'],
            'statusTitle' => $this->translator->trans('donation.status.cancelled_title'),
            'statusMessage' => $this->translator->trans('donation.status.cancelled_message'),
            'retryUrl' => $this->generateUrl('donation_index', $donation instanceof Donation ? ['ref' => $donation->getPaymentReference()] : []),
            'homeUrl' => $this->generateUrl('home'),
        ]);
    }

    #[Route('/impossible', name: 'donation_failed', methods: ['GET'])]
    public function failed(
        Request $request,
        DonationRepository $donationRepository,
        PaymentCheckoutRepository $checkoutRepository,
    ): Response {
        $context = $this->resolveDonationContext($request, $donationRepository, $checkoutRepository);
        $donation = $context['donation'];

        return $this->render('donation/status.html.twig', [
            'donation' => $donation,
            'checkout' => $context['checkout'],
            'statusTitle' => $this->translator->trans('donation.status.failed_title'),
            'statusMessage' => $this->translator->trans('donation.status.failed_message'),
            'retryUrl' => $this->generateUrl('donation_index', $donation instanceof Donation ? ['ref' => $donation->getPaymentReference()] : []),
            'homeUrl' => $this->generateUrl('home'),
        ]);
    }

    /**
     * @return array{donation: Donation|null, checkout: PaymentCheckout|null}
     */
    private function resolveDonationContext(Request $request, DonationRepository $donationRepository, PaymentCheckoutRepository $checkoutRepository): array
    {
        $reference = trim((string) $request->query->get('ref', ''));
        if ($reference === '') {
            return ['donation' => null, 'checkout' => null];
        }

        $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $reference]);
        if (!$checkout instanceof PaymentCheckout) {
            return ['donation' => null, 'checkout' => null];
        }

        $donation = $donationRepository->findOneBy(['paymentCheckout' => $checkout]);

        return ['donation' => $donation instanceof Donation ? $donation : null, 'checkout' => $checkout];
    }

    private function findDonationFromReference(string $reference, DonationRepository $donationRepository, PaymentCheckoutRepository $checkoutRepository): ?Donation
    {
        if ($reference === '') {
            return null;
        }

        $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $reference]);
        if (!$checkout instanceof PaymentCheckout) {
            return null;
        }

        $donation = $donationRepository->findOneBy(['paymentCheckout' => $checkout]);

        return $donation instanceof Donation ? $donation : null;
    }

    private function buildInitialData(?User $user, ?Donation $donation): DonationFormData
    {
        $data = new DonationFormData();
        $data->submissionToken = null;

        if ($donation instanceof Donation) {
            $data->amountChoice = $this->donationConfig->isPresetAmount($donation->getAmount()) ? (string) $donation->getAmount() : 'custom';
            $data->customAmount = $this->donationConfig->isPresetAmount($donation->getAmount()) ? null : number_format($donation->getAmount(), 2, '.', '');
            $data->firstName = $donation->getDonorFirstName();
            $data->lastName = $donation->getDonorLastName();
            $data->email = $donation->getDonorEmail();
            $data->anonymous = $donation->isAnonymous();
            $data->message = $donation->getMessage();

            return $data;
        }

        if ($user instanceof User) {
            $data->firstName = $user->getPrenom() ?? '';
            $data->lastName = $user->getNom() ?? '';
            $data->email = $user->getEmail() ?? '';
        }

        return $data;
    }

    private function resolveAmount(DonationFormData $data, \Symfony\Component\Form\FormInterface $form, Donation $donation): ?float
    {
        $choice = trim((string) $data->amountChoice);
        $presets = array_map(static fn (float $amount): string => (string) $amount, $this->donationConfig->getPresetAmounts());

        if ($choice !== '' && $choice !== 'custom') {
            if (!in_array($choice, $presets, true)) {
                $form->get('amountChoice')->addError(new \Symfony\Component\Form\FormError($this->translator->trans('donation.form.validation.invalid_amount_choice')));

                return null;
            }

            $amount = round((float) $choice, 2);
        } else {
            $rawCustomAmount = str_replace([' ', "\xc2\xa0"], '', (string) $data->customAmount);
            $rawCustomAmount = str_replace(',', '.', $rawCustomAmount);
            if ($rawCustomAmount === '' || !is_numeric($rawCustomAmount)) {
                $form->get('customAmount')->addError(new \Symfony\Component\Form\FormError($this->translator->trans('donation.form.validation.custom_amount_required')));

                return null;
            }

            $amount = round((float) $rawCustomAmount, 2);
        }

        if (!$this->donationConfig->isAmountAllowed($amount)) {
            $message = $amount < $this->donationConfig->getMinimumAmount()
                ? 'donation.form.validation.custom_amount_too_low'
                : 'donation.form.validation.custom_amount_too_high';
            $targetField = $choice === 'custom' || $choice === '' ? 'customAmount' : 'amountChoice';
            $form->get($targetField)->addError(new \Symfony\Component\Form\FormError($this->translator->trans($message, [
                '%min%' => number_format($this->donationConfig->getMinimumAmount(), 2, ',', ' '),
                '%max%' => $this->donationConfig->getMaximumAmount() !== null ? number_format($this->donationConfig->getMaximumAmount(), 2, ',', ' ') : '',
            ])));

            return null;
        }

        $donation->setAmount($amount);

        return $amount;
    }

    private function renderDonationPage(\Symfony\Component\Form\FormInterface $form, ?Donation $resumeDonation, SessionInterface $session): Response
    {
        $token = $session->get('donation_form_token');
        if (!$form->isSubmitted() && is_string($token) && $token !== '') {
            $form->get('submissionToken')->setData($token);
        }

        return $this->render('donation/index.html.twig', [
            'form' => $form->createView(),
            'donation' => $resumeDonation,
            'presetAmounts' => $this->donationConfig->getPresetAmounts(),
            'minimumAmount' => $this->donationConfig->getMinimumAmount(),
            'maximumAmount' => $this->donationConfig->getMaximumAmount(),
            'currency' => $this->donationConfig->getCurrency(),
            'resumeDonation' => $resumeDonation instanceof Donation,
        ]);
    }
}
