<?php

namespace App\Controller;

use App\Entity\CartageQrToken;
use App\Entity\CartageRegistration;
use App\Entity\Page;
use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Repository\CartageQrTokenRepository;
use App\Repository\PageRepository;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cartage')]
class CartageController extends AbstractController
{
    private const SESSION_TOKEN = 'cartage_qr_token';
    private const SESSION_UNLOCKED_AT = 'cartage_qr_unlocked_at';

    #[Route('/qr/{token}', name: 'cartage_qr_scan')]
    public function scan(string $token, CartageQrTokenRepository $tokenRepository, SessionInterface $session): Response
    {
        $qrToken = $tokenRepository->findOneBy(['token' => $token]);
        if (!$qrToken instanceof CartageQrToken || !$qrToken->isUsable()) {
            return $this->render('cartage/locked.html.twig', [
                'message' => 'Ce QR code de cartage est invalide ou expire.',
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        $session->set(self::SESSION_TOKEN, $qrToken->getToken());
        $session->set(self::SESSION_UNLOCKED_AT, time());

        return $this->redirectToRoute('cartage_form');
    }

    #[Route('', name: 'cartage_form', methods: ['GET', 'POST'])]
    public function form(
        Request $request,
        SessionInterface $session,
        CartageQrTokenRepository $tokenRepository,
        PageRepository $pageRepository,
        EntityManagerInterface $entityManager,
        SumupService $sumupService,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $qrToken = $this->getUnlockedToken($session, $tokenRepository);
        if (!$qrToken instanceof CartageQrToken) {
            return $this->render('cartage/locked.html.twig', [
                'message' => 'Scanne le QR code de cartage pour acceder au formulaire.',
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        $internalRulesPage = $pageRepository->findOneBy(['slug' => 'reglement-interieur']);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('cartage_register', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Formulaire invalide, reessaie depuis le QR code.');

                return $this->redirectToRoute('cartage_form');
            }

            $data = $request->request->all();
            $errors = $this->validateRegistrationData($data);
            if ($errors !== []) {
                return $this->render('cartage/form.html.twig', [
                    'qrToken' => $qrToken,
                    'internalRulesPage' => $internalRulesPage,
                    'data' => $data,
                    'errors' => $errors,
                ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            $registration = $this->createRegistrationFromData($data, $qrToken, $passwordHasher);
            $registration
                ->setInternalRulesAcceptedAt(new \DateTimeImmutable())
                ->setInternalRulesTitle($internalRulesPage?->getTitle() ?? 'Reglement interieur')
                ->setInternalRulesContent($internalRulesPage?->getContent());

            $paymentMethod = (string) ($data['payment_method'] ?? 'cash');

            if ($paymentMethod === 'cash' || $registration->getAmount() <= 0) {
                $registration
                    ->setPaymentMethod('cash')
                    ->setStatus(CartageRegistration::STATUS_PENDING_CASH);

                $qrToken->incrementUsedCount();
                $entityManager->persist($registration);
                $entityManager->persist($qrToken);
                $entityManager->flush();
                $session->remove(self::SESSION_TOKEN);
                $session->remove(self::SESSION_UNLOCKED_AT);

                return $this->render('cartage/cash_thankyou.html.twig', [
                    'registration' => $registration,
                ]);
            }

            $reference = strtoupper('CARTAGE-'.bin2hex(random_bytes(5)));
            $registration
                ->setPaymentMethod('online')
                ->setStatus(CartageRegistration::STATUS_PENDING_ONLINE)
                ->setCheckoutReference($reference);

            $checkout = new PaymentCheckout();
            $checkout->setType('cartage')
                ->setStatus('pending')
                ->setCheckoutReference($reference)
                ->setAmount($registration->getAmount())
                ->setCurrency('EUR')
                ->setEmail($registration->getEmail())
                ->setCart([[
                    'cartage_registration_id' => null,
                    'title' => 'Cartage',
                    'first_name' => $registration->getFirstName(),
                    'last_name' => $registration->getLastName(),
                    'quantity' => 1,
                    'unit_price' => $registration->getAmount(),
                    'total' => $registration->getAmount(),
                ]])
                ->setCreatedAt(new \DateTime());

            $entityManager->persist($registration);
            $entityManager->persist($checkout);
            $entityManager->flush();

            $checkout->setCart([[
                'cartage_registration_id' => $registration->getId(),
                'title' => 'Cartage',
                'first_name' => $registration->getFirstName(),
                'last_name' => $registration->getLastName(),
                'quantity' => 1,
                'unit_price' => $registration->getAmount(),
                'total' => $registration->getAmount(),
            ]]);
            $entityManager->flush();

            $response = $sumupService->createHostedCheckout(
                $registration->getAmount(),
                'EUR',
                $reference,
                sprintf('Cartage - %s %s', $registration->getFirstName(), $registration->getLastName())
            );

            if (!empty($response['_error'])) {
                $registration->setStatus(CartageRegistration::STATUS_PENDING_ONLINE);
                $checkout->setStatus('failed');
                $entityManager->flush();

                $this->addFlash('danger', 'Le paiement en ligne est indisponible. Choisis le paiement en liquide ou reessaie.');

                return $this->redirectToRoute('cartage_form');
            }

            $checkoutId = $response['id'] ?? null;
            $hostedUrl = $response['hosted_checkout_url'] ?? null;

            if (!$checkoutId || !$hostedUrl) {
                $checkout->setStatus('failed');
                $entityManager->flush();
                $this->addFlash('danger', 'Impossible de creer le paiement. Reessaie.');

                return $this->redirectToRoute('cartage_form');
            }

            $checkout->setSumupCheckoutId($checkoutId);
            $registration->setSumupCheckoutId($checkoutId);
            $qrToken->incrementUsedCount();
            $entityManager->persist($qrToken);
            $entityManager->flush();

            $session->remove(self::SESSION_TOKEN);
            $session->remove(self::SESSION_UNLOCKED_AT);

            return $this->redirect($hostedUrl);
        }

        return $this->render('cartage/form.html.twig', [
            'qrToken' => $qrToken,
            'internalRulesPage' => $internalRulesPage,
            'data' => [],
            'errors' => [],
        ]);
    }

    #[Route('/merci', name: 'cartage_thankyou')]
    public function thankyou(): Response
    {
        return $this->render('cartage/thankyou.html.twig');
    }

    private function getUnlockedToken(SessionInterface $session, CartageQrTokenRepository $tokenRepository): ?CartageQrToken
    {
        $token = (string) $session->get(self::SESSION_TOKEN, '');
        $unlockedAt = (int) $session->get(self::SESSION_UNLOCKED_AT, 0);

        if ($token === '' || $unlockedAt <= 0 || time() - $unlockedAt > 3600) {
            return null;
        }

        $qrToken = $tokenRepository->findOneBy(['token' => $token]);

        return $qrToken instanceof CartageQrToken && $qrToken->isUsable() ? $qrToken : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return string[]
     */
    private function validateRegistrationData(array $data): array
    {
        $errors = [];

        foreach (['prenom' => 'prenom', 'nom' => 'nom', 'telephone' => 'telephone'] as $field => $label) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                $errors[] = sprintf('Le champ %s est obligatoire.', $label);
            }
        }

        if (!filter_var((string) ($data['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Renseigne un email valide.';
        }

        if (!in_array((string) ($data['payment_method'] ?? ''), ['cash', 'online'], true)) {
            $errors[] = 'Choisis un moyen de paiement.';
        }

        if ((string) ($data['accept_internal_rules'] ?? '') !== '1') {
            $errors[] = 'Tu dois lire et accepter le reglement interieur avant de valider ton cartage.';
        }

        $password = (string) ($data['plainPassword'] ?? '');
        $confirmPassword = (string) ($data['confirmPassword'] ?? '');

        if ($password === '') {
            $errors[] = 'Le mot de passe est obligatoire.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }

        if (!preg_match('/[\W]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractere special.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createRegistrationFromData(array $data, CartageQrToken $qrToken, UserPasswordHasherInterface $passwordHasher): CartageRegistration
    {
        $birthDate = null;
        $birthDateValue = trim((string) ($data['birth_date'] ?? ''));
        if ($birthDateValue !== '') {
            $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $birthDateValue) ?: null;
        }

        $registration = (new CartageRegistration())
            ->setQrToken($qrToken)
            ->setFirstName((string) $data['prenom'])
            ->setLastName((string) $data['nom'])
            ->setEmail((string) $data['email'])
            ->setPhone((string) $data['telephone'])
            ->setBirthDate($birthDate)
            ->setAddress((string) ($data['adresse'] ?? ''))
            ->setPostalCode((string) ($data['codePostal'] ?? ''))
            ->setCity((string) ($data['ville'] ?? ''))
            ->setShirtSize((string) ($data['tailleTshirt'] ?? ''))
            ->setSweatSize((string) ($data['tailleSweat'] ?? ''))
            ->setJacketSize((string) ($data['tailleVeste'] ?? ''))
            ->setShortSize((string) ($data['tailleShort'] ?? ''))
            ->setAmount($qrToken->getAmount());

        $registration->setPassword($passwordHasher->hashPassword(new User(), (string) $data['plainPassword']));

        return $registration;
    }
}
