<php

namespace App\Controller;

use App\Entity\CartageQrToken;
use App\Entity\CartageRegistration;
use App\Entity\Page;
use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Repository\CartageRegistrationRepository;
use App\Repository\CartageQrTokenRepository;
use App\Repository\PageRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Repository\UserRepository;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    private const SESSION_PENDING_REGISTRATION = 'cartage_pending_registration';
    private const SESSION_LAST_CHECKOUT_REFERENCE = 'cartage_last_checkout_reference';
    private const SESSION_LAST_CHECKOUT_ID = 'cartage_last_checkout_id';

    #[Route('/qr/{token}', name: 'cartage_qr_scan')]
    public function scan(string $token, CartageQrTokenRepository $tokenRepository, SessionInterface $session): Response
    {
        $qrToken = $tokenRepository->findOneBy(['token' => $token]);
        if (!$qrToken instanceof CartageQrToken || !$qrToken->isUsable()) {
            return $this->render('cartage/locked.html.twig', [
                'message' => 'Ce QR code de cartage est invalide ou expire.',
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        $session->remove(self::SESSION_PENDING_REGISTRATION);
        $session->set(self::SESSION_TOKEN, $qrToken->getToken());
        $session->set(self::SESSION_UNLOCKED_AT, time());

        return $this->redirectToRoute('cartage_form');
    }

    #[Route('', name: 'cartage_form', methods: ['GET', 'POST'])]
    public function form(
        Request $request,
        SessionInterface $session,
        CartageQrTokenRepository $tokenRepository,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        CartageRegistrationRepository $registrationRepository
    ): Response {
        $qrToken = $this->getUnlockedToken($session, $tokenRepository);
        if (!$qrToken instanceof CartageQrToken) {
            return $this->render('cartage/locked.html.twig', [
                'message' => 'Scanne le QR code de cartage pour acceder au formulaire.',
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        $currentSeason = $this->getSeasonFromQrToken($qrToken);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('cartage_register', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Formulaire invalide, reessaie depuis le QR code.');

                return $this->redirectToRoute('cartage_form');
            }

            $data = $request->request->all();
            $email = $this->normalizeEmail((string) ($data['email']  ''));
            $data['email'] = $email;
            $existingUser = $userRepository->findOneByEmail($email);
            $seasonRegistration = $registrationRepository->findLatestForSeasonByEmail($email, $currentSeason);

            $errors = $this->validateRegistrationData($data, $existingUser instanceof User);
            if ($seasonRegistration instanceof CartageRegistration) {
                if ($seasonRegistration->isCompleted()) {
                    $errors[] = sprintf(
                        'Un cartage valide existe deja pour cette adresse email sur la saison %s.',
                        $currentSeason
                    );
                } elseif ($seasonRegistration->isPending()) {
                    $errors[] = sprintf(
                        'Un cartage est deja en attente pour cette adresse email sur la saison %s.',
                        $currentSeason
                    );
                }
            }

            if ($errors !== []) {
                return $this->render('cartage/form.html.twig', [
                    'qrToken' => $qrToken,
                    'data' => $data,
                    'errors' => $errors,
                    'currentSeason' => $currentSeason,
                    'accountExists' => $existingUser instanceof User,
                    'seasonRegistration' => $seasonRegistration,
                ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            $session->set(self::SESSION_PENDING_REGISTRATION, $this->createPendingRegistrationData($data, $passwordHasher, $existingUser instanceof User, $currentSeason));

            return $this->redirectToRoute('cartage_rules');
        }

        $data = $session->get(self::SESSION_PENDING_REGISTRATION, []);
        if (!is_array($data)) {
            $data = [];
        }

        if ($data === [] && $this->getUser() instanceof User) {
            /** @var User $user */
            $user = $this->getUser();
            $data = $this->createPrefillDataFromUser($user);
        }

        $email = $this->normalizeEmail((string) ($data['email']  ''));
        $existingUser = $userRepository->findOneByEmail($email);
        $seasonRegistration = $registrationRepository->findLatestForSeasonByEmail($email, $currentSeason);

        return $this->render('cartage/form.html.twig', [
            'qrToken' => $qrToken,
            'data' => $data,
            'errors' => [],
            'currentSeason' => $currentSeason,
            'accountExists' => $existingUser instanceof User,
            'seasonRegistration' => $seasonRegistration,
        ]);
    }

    #[Route('/reglement', name: 'cartage_rules', methods: ['GET', 'POST'])]
    public function rules(
        Request $request,
        SessionInterface $session,
        CartageQrTokenRepository $tokenRepository,
        PageRepository $pageRepository,
        CartageRegistrationRepository $registrationRepository,
        EntityManagerInterface $entityManager,
        SumupService $sumupService
    ): Response {
        $qrToken = $this->getUnlockedToken($session, $tokenRepository);
        if (!$qrToken instanceof CartageQrToken) {
            return $this->render('cartage/locked.html.twig', [
                'message' => 'Scanne le QR code de cartage pour acceder au formulaire.',
            ], new Response('', Response::HTTP_FORBIDDEN));
        }

        $pendingData = $session->get(self::SESSION_PENDING_REGISTRATION);
        if (!is_array($pendingData) || $pendingData === []) {
            $this->addFlash('danger', 'Remplis d abord le formulaire de cartage.');

            return $this->redirectToRoute('cartage_form');
        }

        $internalRulesPage = $pageRepository->findOneBy(['slug' => 'reglement-interieur']);
        $data = ['payment_method' => 'online'];
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('cartage_rules', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Validation invalide, reessaie.');

                return $this->redirectToRoute('cartage_rules');
            }

            $data = $request->request->all();
            $errors = $this->validateRulesData($data);
            if ($errors === []) {
                $seasonRegistration = $registrationRepository->findLatestForSeasonByEmail(
                    (string) ($pendingData['email']  ''),
                    (string) ($pendingData['season']  $this->getCurrentSeason())
                );

                if ($seasonRegistration instanceof CartageRegistration) {
                    $errors[] = sprintf(
                        'Un cartage existe deja pour cette adresse email sur la saison %s.',
                        (string) ($pendingData['season']  $this->getCurrentSeason())
                    );
                }
            }

            if ($errors === []) {
                return $this->completeRegistrationPayment(
                    $pendingData,
                    $qrToken,
                    $internalRulesPage,
                    $entityManager,
                    $sumupService,
                    $session,
                    (string) ($data['payment_method']  'cash')
                );
            }
        }

        return $this->render('cartage/rules.html.twig', [
            'qrToken' => $qrToken,
            'internalRulesPage' => $internalRulesPage,
            'data' => $data,
            'errors' => $errors,
        ], $errors === []  null : new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    #[Route('/cartage/confirmation', name: 'cartage_thankyou')]
    public function thankyou(): Response
    {
        return $this->render('cartage/thankyou.html.twig');
    }

    #[Route('/statut', name: 'cartage_status', methods: ['GET', 'POST'])]
    public function status(
        Request $request,
        CartageQrTokenRepository $tokenRepository,
        UserRepository $userRepository,
        CartageRegistrationRepository $registrationRepository
    ): Response {
        $currentSeason = $this->getCurrentCartageSeason($tokenRepository);
        $email = $this->normalizeEmail($request->isMethod('POST')
             $request->request->getString('email')
            : $request->query->getString('email')
        );
        $season = trim($request->isMethod('POST')
             $request->request->getString('season', $currentSeason)
            : $request->query->getString('season', $currentSeason)
        ) : $currentSeason;

        $account = null;
        $registration = null;
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $account = $userRepository->findOneByEmail($email);
            $registration = $registrationRepository->findLatestForSeasonByEmail($email, $season);
        }

        return $this->render('cartage/status.html.twig', [
            'email' => $email,
            'season' => $season,
            'currentSeason' => $currentSeason,
            'accountExists' => $account instanceof User,
            'registration' => $registration,
            'isSearched' => $email !== '',
        ]);
    }

    #[Route('/email-status', name: 'cartage_email_status', methods: ['GET'])]
    public function emailStatus(
        Request $request,
        SessionInterface $session,
        CartageQrTokenRepository $tokenRepository,
        UserRepository $userRepository,
        CartageRegistrationRepository $registrationRepository
    ): JsonResponse {
        if (!$this->getUnlockedToken($session, $tokenRepository) instanceof CartageQrToken) {
            return $this->json(['error' => 'locked'], Response::HTTP_FORBIDDEN);
        }

        $currentSeason = $this->getSeasonFromQrToken($this->getUnlockedToken($session, $tokenRepository));
        $email = $this->normalizeEmail($request->query->getString('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'validEmail' => false,
                'accountExists' => false,
                'season' => $currentSeason,
                'registration' => null,
            ]);
        }

        $registration = $registrationRepository->findLatestForSeasonByEmail($email, $currentSeason);

        return $this->json([
            'validEmail' => true,
            'accountExists' => $userRepository->findOneByEmail($email) instanceof User,
            'season' => $currentSeason,
            'registration' => $registration instanceof CartageRegistration  [
                'status' => $registration->getStatus(),
                'label' => $registration->getStatusLabel(),
                'completed' => $registration->isCompleted(),
                'pending' => $registration->isPending(),
            ] : null,
        ]);
    }

    #[Route('/reprendre/{id}', name: 'cartage_resume', methods: ['GET'])]
    public function resume(
        CartageRegistration $registration,
        PaymentCheckoutRepository $checkoutRepository,
        EntityManagerInterface $entityManager,
        SumupService $sumupService,
        LoggerInterface $logger
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('danger', 'Connecte-toi pour reprendre ton cartage.');

            return $this->redirectToRoute('app_login');
        }

        $profileResponse = $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        $registrationEmail = $this->normalizeEmail($registration->getEmail());
        $userEmail = $this->normalizeEmail((string) $user->getEmail());

        if (!$this->isGranted('ROLE_ADMIN') && $registrationEmail !== $userEmail) {
            throw $this->createAccessDeniedException('Ce cartage ne correspond pas a ton compte.');
        }

        if ($registration->isCompleted()) {
            $this->addFlash('success', 'Ton cartage est deja valide.');

            return $profileResponse;
        }

        if ($registration->getStatus() !== CartageRegistration::STATUS_PENDING_ONLINE) {
            $this->addFlash('warning', 'Ce cartage ne peut pas etre repris en paiement en ligne.');

            return $profileResponse;
        }

        $checkout = null;
        $reference = (string) $registration->getCheckoutReference();
        if ($reference !== '') {
            $checkout = $checkoutRepository->findOneBy(['checkoutReference' => $reference]);
        }

        if ($checkout instanceof PaymentCheckout && $checkout->getStatus() === 'paid') {
            $registration->markPaidOnline();
            $entityManager->flush();
            $this->addFlash('success', 'Paiement confirme, ton cartage est valide.');

            return $profileResponse;
        }

        if ($checkout instanceof PaymentCheckout && $checkout->getSumupCheckoutId()) {
            $details = $sumupService->retrieveCheckout($checkout->getSumupCheckoutId());
            if (empty($details['_error'])) {
                $status = strtoupper((string) ($details['status']  ''));
                if (in_array($status, ['PAID', 'SUCCESSFUL', 'SUCCESS', 'COMPLETED'], true)) {
                    $checkout->setStatus('paid');
                    $checkout->setPaidAt(new \DateTime());
                    $registration->markPaidOnline();
                    $entityManager->flush();
                    $this->addFlash('success', 'Paiement confirme, ton cartage est valide.');

                    return $profileResponse;
                }

                $hostedUrl = (string) ($details['hosted_checkout_url']  '');
                if ($hostedUrl !== '' && !in_array($status, ['FAILED', 'CANCELLED', 'CANCELED', 'EXPIRED'], true)) {
                    return $this->redirect($hostedUrl);
                }
            } else {
                $logger->warning('Impossible de recuperer le checkout SumUp de cartage.', [
                    'registration_id' => $registration->getId(),
                    'checkout_id' => $checkout->getSumupCheckoutId(),
                    'error' => $details['_error'],
                ]);
            }
        }

        return $this->restartCartageOnlinePayment($registration, $checkout, $user, $entityManager, $sumupService);
    }

    private function completeRegistrationPayment(
        array $data,
        CartageQrToken $qrToken,
        Page $internalRulesPage,
        EntityManagerInterface $entityManager,
        SumupService $sumupService,
        SessionInterface $session,
        string $paymentMethod
    ): Response {
        $registration = $this->createRegistrationFromData($data, $qrToken);
        $registration
            ->setInternalRulesAcceptedAt(new \DateTimeImmutable())
            ->setInternalRulesTitle($internalRulesPage->getTitle()  'Reglement interieur')
            ->setInternalRulesContent($internalRulesPage->getContent());

        if ($paymentMethod === 'cash' || $registration->getAmount() <= 0) {
            $registration
                ->setPaymentMethod('cash')
                ->setStatus(CartageRegistration::STATUS_PENDING_CASH);

            $qrToken->incrementUsedCount();
            $entityManager->persist($registration);
            $entityManager->persist($qrToken);
            $entityManager->flush();
            $this->clearCartageSession($session);

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

            $message = trim((string) ($response['_message']  ''));
            $errorCode = trim((string) ($response['_error']  ''));
            $detail = trim((string) ($response['_details']  ''));
            $humanMessage = $message !== ''  $message : 'Le paiement en ligne est indisponible.';
            $this->addFlash('danger', $detail !== ''
                 sprintf('%s (%s)', $humanMessage, $errorCode !== ''  $errorCode : $detail)
                : $humanMessage
            );

            return $this->redirectToRoute('cartage_rules');
        }

        $checkoutId = $response['id']  null;
        $hostedUrl = $response['hosted_checkout_url']  null;

        if (!$checkoutId || !$hostedUrl) {
            $checkout->setStatus('failed');
            $entityManager->flush();
            $this->addFlash('danger', 'Impossible de creer le paiement. Reessaie.');

            return $this->redirectToRoute('cartage_rules');
        }

        $checkout->setSumupCheckoutId($checkoutId);
        $registration->setSumupCheckoutId($checkoutId);
        $qrToken->incrementUsedCount();
        $entityManager->persist($qrToken);
        $entityManager->flush();

        $session->set(self::SESSION_LAST_CHECKOUT_REFERENCE, $reference);
        $session->set(self::SESSION_LAST_CHECKOUT_ID, $checkoutId);
        $this->clearCartageSession($session);

        return $this->redirect($hostedUrl);
    }

    private function restartCartageOnlinePayment(
        CartageRegistration $registration,
        PaymentCheckout $previousCheckout,
        User $user,
        EntityManagerInterface $entityManager,
        SumupService $sumupService
    ): Response {
        $reference = strtoupper('CARTAGE-'.bin2hex(random_bytes(5)));

        if ($previousCheckout instanceof PaymentCheckout && $previousCheckout->getStatus() === 'pending') {
            $previousCheckout->setStatus('failed');
        }

        $registration
            ->setPaymentMethod('online')
            ->setStatus(CartageRegistration::STATUS_PENDING_ONLINE)
            ->setCheckoutReference($reference)
            ->setSumupCheckoutId(null);

        $checkout = (new PaymentCheckout())
            ->setType('cartage')
            ->setStatus('pending')
            ->setCheckoutReference($reference)
            ->setAmount($registration->getAmount())
            ->setCurrency('EUR')
            ->setUser($user)
            ->setEmail($registration->getEmail())
            ->setCustomerFirstName($registration->getFirstName())
            ->setCustomerLastName($registration->getLastName())
            ->setCart($this->createCartageCheckoutCart($registration))
            ->setCreatedAt(new \DateTime());

        $entityManager->persist($registration);
        $entityManager->persist($checkout);
        $entityManager->flush();

        $response = $sumupService->createHostedCheckout(
            $registration->getAmount(),
            'EUR',
            $reference,
            sprintf('Cartage - %s %s', $registration->getFirstName(), $registration->getLastName())
        );

        if (!empty($response['_error'])) {
            $checkout->setStatus('failed');
            $entityManager->flush();
            $this->addFlash('danger', 'Le paiement en ligne est indisponible pour le moment. Reessaie plus tard.');

            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        $checkoutId = (string) ($response['id']  '');
        $hostedUrl = (string) ($response['hosted_checkout_url']  '');

        if ($checkoutId === '' || $hostedUrl === '') {
            $checkout->setStatus('failed');
            $entityManager->flush();
            $this->addFlash('danger', 'Impossible de recreer le paiement. Reessaie plus tard.');

            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        $checkout->setSumupCheckoutId($checkoutId);
        $registration->setSumupCheckoutId($checkoutId);
        $entityManager->flush();

        $session->set(self::SESSION_LAST_CHECKOUT_REFERENCE, $reference);
        $session->set(self::SESSION_LAST_CHECKOUT_ID, $checkoutId);

        return $this->redirect($hostedUrl);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function createCartageCheckoutCart(CartageRegistration $registration): array
    {
        return [[
            'cartage_registration_id' => $registration->getId(),
            'title' => 'Cartage',
            'first_name' => $registration->getFirstName(),
            'last_name' => $registration->getLastName(),
            'quantity' => 1,
            'unit_price' => $registration->getAmount(),
            'total' => $registration->getAmount(),
        ]];
    }

    private function getUnlockedToken(SessionInterface $session, CartageQrTokenRepository $tokenRepository): CartageQrToken
    {
        $token = (string) $session->get(self::SESSION_TOKEN, '');
        $unlockedAt = (int) $session->get(self::SESSION_UNLOCKED_AT, 0);

        if ($token === '' || $unlockedAt <= 0 || time() - $unlockedAt > 3600) {
            return null;
        }

        $qrToken = $tokenRepository->findOneBy(['token' => $token]);

        return $qrToken instanceof CartageQrToken && $qrToken->isUsable()  $qrToken : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return string[]
     */
    private function validateRegistrationData(array $data, bool $accountExists): array
    {
        $errors = [];

        foreach (['prenom' => 'prenom', 'nom' => 'nom', 'telephone' => 'telephone'] as $field => $label) {
            if (trim((string) ($data[$field]  '')) === '') {
                $errors[] = sprintf('Le champ %s est obligatoire.', $label);
            }
        }

        if (!filter_var((string) ($data['email']  ''), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Renseigne un email valide.';
        }

        $birthDateValue = trim((string) ($data['birth_date']  ''));
        if ($birthDateValue === '') {
            $errors[] = 'La date de naissance est obligatoire.';
        } else {
            $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $birthDateValue);
            if (!$birthDate instanceof \DateTimeImmutable || $birthDate->format('Y-m-d') !== $birthDateValue) {
                $errors[] = 'Renseigne une date de naissance valide.';
            } elseif ($birthDate > new \DateTimeImmutable('today')) {
                $errors[] = 'La date de naissance ne peut pas etre dans le futur.';
            }
        }

        if (!$accountExists) {
            $password = (string) ($data['plainPassword']  '');
            $confirmPassword = (string) ($data['confirmPassword']  '');

            if ($password === '') {
                $errors[] = 'Le mot de passe est obligatoire pour creer un compte.';
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
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return string[]
     */
    private function validateRulesData(array $data): array
    {
        $errors = [];

        if ((string) ($data['accept_internal_rules']  '') !== '1') {
            $errors[] = 'Tu dois lire et accepter le reglement interieur avant de payer.';
        }

        if (!in_array((string) ($data['payment_method']  ''), ['cash', 'online'], true)) {
            $errors[] = 'Choisis un moyen de paiement.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function createPendingRegistrationData(array $data, UserPasswordHasherInterface $passwordHasher, bool $accountExists, string $season): array
    {
        $passwordHash = '';
        if (!$accountExists) {
            $passwordHash = $passwordHasher->hashPassword(new User(), (string) $data['plainPassword']);
        }

        return [
            'prenom' => (string) ($data['prenom']  ''),
            'nom' => (string) ($data['nom']  ''),
            'email' => $this->normalizeEmail((string) ($data['email']  '')),
            'telephone' => (string) ($data['telephone']  ''),
            'birth_date' => (string) ($data['birth_date']  ''),
            'adresse' => (string) ($data['adresse']  ''),
            'codePostal' => (string) ($data['codePostal']  ''),
            'ville' => (string) ($data['ville']  ''),
            'tailleTshirt' => (string) ($data['tailleTshirt']  ''),
            'taillePolo' => (string) ($data['taillePolo']  ''),
            'taillePull' => (string) ($data['taillePull']  ''),
            'tailleSweat' => (string) ($data['tailleSweat']  ''),
            'tailleVeste' => (string) ($data['tailleVeste']  ''),
            'tailleShort' => (string) ($data['tailleShort']  ''),
            'password_hash' => $passwordHash,
            'account_exists' => $accountExists  '1' : '0',
            'season' => $season,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createRegistrationFromData(array $data, CartageQrToken $qrToken): CartageRegistration
    {
        $birthDate = null;
        $birthDateValue = trim((string) ($data['birth_date']  ''));
        if ($birthDateValue !== '') {
            $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $birthDateValue) : null;
        }

        $registration = (new CartageRegistration())
            ->setQrToken($qrToken)
            ->setFirstName((string) $data['prenom'])
            ->setLastName((string) $data['nom'])
            ->setEmail((string) $data['email'])
            ->setPhone((string) $data['telephone'])
            ->setBirthDate($birthDate)
            ->setAddress((string) ($data['adresse']  ''))
            ->setPostalCode((string) ($data['codePostal']  ''))
            ->setCity((string) ($data['ville']  ''))
            ->setShirtSize((string) ($data['tailleTshirt']  ''))
            ->setPoloSize((string) ($data['taillePolo']  ''))
            ->setPullSize((string) ($data['taillePull']  ''))
            ->setSweatSize((string) ($data['tailleSweat']  ''))
            ->setJacketSize((string) ($data['tailleVeste']  ''))
            ->setShortSize((string) ($data['tailleShort']  ''))
            ->setAmount($qrToken->getAmount())
            ->setSeason((string) ($data['season']  $this->getSeasonFromQrToken($qrToken)));

        $registration->setPassword((string) ($data['password_hash']  ''));

        return $registration;
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function getCurrentSeason(\DateTimeImmutable $date = null): string
    {
        $date = new \DateTimeImmutable();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $startYear = $month >= 7  $year : $year - 1;

        return sprintf('%d-%d', $startYear, $startYear + 1);
    }

    private function getCurrentCartageSeason(CartageQrTokenRepository $tokenRepository): string
    {
        return $this->getSeasonFromQrToken($tokenRepository->findLatestUsable());
    }

    private function getSeasonFromQrToken(CartageQrToken $qrToken): string
    {
        if ($qrToken instanceof CartageQrToken) {
            $season = $this->extractSeason($qrToken->getLabel());
            if ($season !== null) {
                return $season;
            }
        }

        return $this->getCurrentSeason();
    }

    private function extractSeason(string $value): string
    {
        if (preg_match('/(20\d{2})\s*[\/-]\s*(20\d{2})/', $value, $matches) !== 1) {
            return null;
        }

        return $matches[1].'-'.$matches[2];
    }

    /**
     * @return array<string, string>
     */
    private function createPrefillDataFromUser(User $user): array
    {
        $birthDate = $user->getDateNaissance();

        return [
            'prenom' => (string) ($user->getPrenom()  ''),
            'nom' => (string) ($user->getNom()  ''),
            'email' => $this->normalizeEmail((string) $user->getEmail()),
            'telephone' => (string) ($user->getTelephone()  ''),
            'birth_date' => $birthDate instanceof \DateTimeInterface  $birthDate->format('Y-m-d') : '',
            'adresse' => (string) ($user->getAdresse()  ''),
            'codePostal' => (string) ($user->getCodePostal()  ''),
            'ville' => (string) ($user->getVille()  ''),
            'tailleTshirt' => (string) ($user->getTailleTshirt()  ''),
            'taillePolo' => (string) ($user->getTaillePolo()  ''),
            'taillePull' => (string) ($user->getTaillePull()  ''),
            'tailleSweat' => (string) ($user->getTailleSweat()  ''),
            'tailleVeste' => (string) ($user->getTailleVeste()  ''),
            'tailleShort' => (string) ($user->getTailleShort()  ''),
        ];
    }

    private function clearCartageSession(SessionInterface $session): void
    {
        $session->remove(self::SESSION_TOKEN);
        $session->remove(self::SESSION_UNLOCKED_AT);
        $session->remove(self::SESSION_PENDING_REGISTRATION);
    }
}
