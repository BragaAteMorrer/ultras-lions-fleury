<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PaymentCheckout;
use App\Entity\UserAccountLink;
use App\Form\ProfileType;
use App\Form\ProfilePasswordType;
use App\Repository\BilletwebLeadRepository;
use App\Repository\CartageRegistrationRepository;
use App\Repository\CartageQrTokenRepository;
use App\Repository\MerchOrderRepository;
use App\Repository\TicketOrderRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Repository\UserAccountLinkRepository;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\ManagedAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/profil/{id<\d+>}', name: 'profile_show')]
    public function show(
        User $user,
        MerchOrderRepository $merchOrderRepository,
        TicketOrderRepository $ticketOrderRepository,
        PaymentCheckoutRepository $paymentCheckoutRepository,
        CartageRegistrationRepository $cartageRegistrationRepository,
        CartageQrTokenRepository $cartageQrTokenRepository,
        BilletwebLeadRepository $billetwebLeadRepository,
        UserAccountLinkRepository $accountLinkRepository,
        ManagedAccountService $managedAccountService
    ): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || (!$this->isGranted('ROLE_ADMIN') && !$managedAccountService->canAccessUser($currentUser, $user))) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_show'));
        }

        $currentSeason = $this->getCurrentCartageSeason($cartageQrTokenRepository);
        $cartageRegistration = $user->getEmail()
            ? $cartageRegistrationRepository->findLatestForSeasonByEmail($user->getEmail(), $currentSeason)
            : null;

        $merchOrders = $merchOrderRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        $ticketOrders = $ticketOrderRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        $paymentCheckouts = $paymentCheckoutRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        $billetwebLeads = $billetwebLeadRepository->findForUserProfile($user);
        $managedAccountLinks = $accountLinkRepository->findAcceptedForGuardian($user);
        $pendingManagedAccountLinks = $accountLinkRepository->findPendingForGuardian($user);
        $guardianLinks = $accountLinkRepository->findAcceptedForManagedUser($user);
        $pendingGuardianLinks = $accountLinkRepository->findPendingForManagedUser($user);
        $canManageProfile = $currentUser instanceof User && ($this->isGranted('ROLE_ADMIN') || $managedAccountService->canAccessUser($currentUser, $user));

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'merchOrders' => $merchOrders,
            'ticketOrders' => $ticketOrders,
            'paymentCheckouts' => $paymentCheckouts,
            'billetwebLeads' => $billetwebLeads,
            'currentSeason' => $currentSeason,
            'cartageRegistration' => $cartageRegistration,
            'managedAccountLinks' => $managedAccountLinks,
            'pendingManagedAccountLinks' => $pendingManagedAccountLinks,
            'guardianLinks' => $guardianLinks,
            'pendingGuardianLinks' => $pendingGuardianLinks,
            'canManageProfile' => $canManageProfile,
        ]);
    }

    #[Route('/profil/commande/{id}', name: 'profile_order_show')]
    public function orderShow(
        PaymentCheckout $checkout,
        \App\Repository\MerchRepository $merchRepository,
        \App\Repository\TicketRepository $ticketRepository,
        ManagedAccountService $managedAccountService
    ): Response {
        $current = $this->getUser();
        if (!$current instanceof User || (!$this->isGranted('ROLE_ADMIN') && (!$checkout->getUser() instanceof User || !$managedAccountService->canAccessUser($current, $checkout->getUser())))) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_orders'));
        }

        $lines = $checkout->getCart();
        $items = [];
        $ticketItems = [];
        $merchItems = [];

        if ($checkout->getType() === 'merch') {
            $ids = array_values(array_unique(array_filter(array_map(static fn ($i) => $i['merch_id'] ?? null, $lines))));
            $products = $ids ? $merchRepository->findBy(['id' => $ids]) : [];
            $byId = [];
            foreach ($products as $p) {
                $byId[$p->getId()] = $p;
            }

            foreach ($lines as $row) {
                $product = $byId[$row['merch_id'] ?? 0] ?? null;
                $items[] = [
                    'title' => $row['title'] ?? ($product?->getTitle() ?? $this->translator->trans('profile_order.html.product')),
                    'size' => $row['size'] ?? 'TU',
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            }
            $merchItems = $items;
        } elseif ($checkout->getType() === 'ticket') {
            $ids = array_values(array_unique(array_filter(array_map(static fn ($i) => $i['ticket_id'] ?? null, $lines))));
            $tickets = $ids ? $ticketRepository->findBy(['id' => $ids]) : [];
            $byId = [];
            foreach ($tickets as $t) {
                $byId[$t->getId()] = $t;
            }

            foreach ($lines as $row) {
                $ticket = $byId[$row['ticket_id'] ?? 0] ?? null;
                $items[] = [
                    'title' => $row['title'] ?? ($ticket?->getTitle() ?? $this->translator->trans('profile_order.html.match')),
                    'opponent' => $row['opponent'] ?? ($ticket?->getOpponent() ?? ''),
                    'match_date' => $ticket?->getMatchDate(),
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'unit_price' => (float) ($row['unit_price'] ?? 0),
                    'total' => (float) ($row['total'] ?? 0),
                ];
            }
            $ticketItems = $items;
        } else {
            foreach ($lines as $row) {
                $lineType = (string) ($row['type'] ?? '');
                if ($lineType === 'ticket') {
                    $ticketItems[] = [
                        'title' => $row['title'] ?? $this->translator->trans('profile_order.html.match'),
                        'opponent' => $row['opponent'] ?? '',
                        'match_date' => $row['match_date'] ?? null,
                        'quantity' => (int) ($row['quantity'] ?? 1),
                        'unit_price' => (float) ($row['unit_price'] ?? 0),
                        'total' => (float) ($row['total'] ?? 0),
                    ];
                } else {
                    $merchItems[] = [
                        'title' => $row['title'] ?? $this->translator->trans('profile_order.html.product'),
                        'size' => $row['size'] ?? 'TU',
                        'quantity' => (int) ($row['quantity'] ?? 1),
                        'unit_price' => (float) ($row['unit_price'] ?? 0),
                        'total' => (float) ($row['total'] ?? 0),
                    ];
                }
            }
        }

        return $this->render('profile/order.html.twig', [
            'checkout' => $checkout,
            'items' => $items,
            'ticketItems' => $ticketItems,
            'merchItems' => $merchItems,
        ]);
    }

    #[Route('/profil/{id<\d+>}/tutelle/demander', name: 'profile_managed_account_request', methods: ['POST'])]
    public function requestManagedAccount(
        Request $request,
        User $user,
        UserRepository $userRepository,
        UserAccountLinkRepository $accountLinkRepository,
        EntityManagerInterface $em,
        MailService $mailService
    ): Response {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_manage_links'));
        }

        if (!$this->isCsrfTokenValid('managed_account_request_'.$user->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', $this->translator->trans('profile.flash.invalid_form'));
            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        $email = strtolower(trim((string) $request->request->get('managed_email', '')));
        $managedUser = $email !== '' ? $userRepository->findOneByEmail($email) : null;

        if (!$managedUser instanceof User) {
            $this->addFlash('danger', $this->translator->trans('profile.flash.no_account_found'));
            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        if ($managedUser->getId() === $user->getId()) {
            $this->addFlash('danger', $this->translator->trans('profile.flash.cannot_associate_own_account'));
            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        $existing = $accountLinkRepository->findExisting($user, $managedUser);
        if ($existing instanceof UserAccountLink) {
            if ($existing->isAccepted() || !$existing->isExpired()) {
                $this->addFlash(
                    'warning',
                    $existing->isAccepted()
                        ? $this->translator->trans('profile.flash.already_linked')
                        : $this->translator->trans('profile.flash.pending_request_exists')
                );
                return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
            }

            $link = $existing
                ->setToken(bin2hex(random_bytes(32)))
                ->setRequestedAt(new \DateTimeImmutable())
                ->setExpiresAt((new \DateTimeImmutable())->modify('+14 days'));
        } else {
            $link = (new UserAccountLink())
                ->setGuardian($user)
                ->setManagedUser($managedUser)
                ->setToken(bin2hex(random_bytes(32)));

            $em->persist($link);
        }
        $em->flush();

        try {
            $mailService->send(
                to: (string) $managedUser->getEmail(),
                subject: $this->translator->trans('profile.email.request_subject'),
                template: 'email/account_link_request.html.twig',
                context: [
                    'guardian' => (string) $user,
                    'acceptUrl' => $this->generateUrl(
                        'profile_managed_account_accept',
                        ['token' => $link->getToken()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]
            );
            $this->addFlash('success', $this->translator->trans('profile.flash.request_sent'));
        } catch (\Throwable) {
            $this->addFlash('warning', $this->translator->trans('profile.flash.request_created_email_failed'));
        }

        return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
    }

    #[Route('/profil/tutelle/accepter/{token}', name: 'profile_managed_account_accept')]
    public function acceptManagedAccount(
        string $token,
        UserAccountLinkRepository $accountLinkRepository,
        EntityManagerInterface $em
    ): Response {
        $link = $accountLinkRepository->findOneBy(['token' => $token]);
        if (!$link instanceof UserAccountLink || $link->isExpired()) {
            throw $this->createNotFoundException($this->translator->trans('profile.flash.link_not_found_or_expired'));
        }

        $link->accept();
        $em->flush();

        $this->addFlash('success', $this->translator->trans('profile.flash.association_validated'));
        $managedUser = $link->getManagedUser();

        return $this->redirectToRoute('profile_show', [
            'id' => $managedUser?->getId(),
        ]);
    }

    #[Route('/profil/tutelle/{id<\d+>}/delier', name: 'profile_managed_account_unlink', methods: ['POST'])]
    public function unlinkManagedAccount(
        UserAccountLink $link,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $current = $this->getUser();
        if (!$current instanceof User || ($link->getGuardian()?->getId() !== $current->getId() && $link->getManagedUser()?->getId() !== $current->getId())) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_unlink'));
        }

        if (!$this->isCsrfTokenValid('managed_account_unlink_'.$link->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', $this->translator->trans('profile.flash.invalid_form'));
            return $this->redirectToRoute('profile_show', ['id' => $current->getId()]);
        }

        $em->remove($link);
        $em->flush();

        $this->addFlash('success', $this->translator->trans('profile.flash.association_deleted'));

        return $this->redirectToRoute('profile_show', ['id' => $current->getId()]);
    }

    #[Route('/profil/{id<\d+>}/edit', name: 'profile_edit')]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        ManagedAccountService $managedAccountService
    ): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || (!$this->isGranted('ROLE_ADMIN') && !$managedAccountService->canAccessUser($currentUser, $user))) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_edit'));
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photoProfil')->getData();

            $fs = new Filesystem();

            if ($photoFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profils';

                if (!$fs->exists($uploadsDir)) {
                    $fs->mkdir($uploadsDir, 0775);
                }

                $newFilename = uniqid('pf_') . '.' . $photoFile->guessExtension();
                $photoFile->move($uploadsDir, $newFilename);

                $user->setPhotoProfil($newFilename);
            }

            $em->flush();

            $this->addFlash('success', $this->translator->trans('profile.flash.updated'));

            return $this->redirectToRoute('profile_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/profil/{id<\d+>}/mot-de-passe', name: 'profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || $currentUser->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException($this->translator->trans('profile.flash.access_denied_password'));
        }

        $form = $this->createForm(ProfilePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = (string) $form->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(new FormError($this->translator->trans('profile.flash.current_password_incorrect')));
            } else {
                $newPassword = trim((string) $form->get('plainPassword')->getData());
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $user->eraseCredentials();
                $em->flush();

                $this->addFlash('success', $this->translator->trans('profile.flash.password_updated'));

                return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
            }
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    private function getCurrentSeason(?\DateTimeImmutable $date = null): string
    {
        $date ??= new \DateTimeImmutable();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $startYear = $month >= 7 ? $year : $year - 1;

        return sprintf('%d-%d', $startYear, $startYear + 1);
    }

    private function getCurrentCartageSeason(CartageQrTokenRepository $cartageQrTokenRepository): string
    {
        $qrToken = $cartageQrTokenRepository->findLatestUsable();
        $season = $qrToken ? $this->extractSeason((string) $qrToken->getLabel()) : null;

        return $season ?: $this->getCurrentSeason();
    }

    private function extractSeason(string $value): ?string
    {
        if (preg_match('/(20\d{2})\s*[\/-]\s*(20\d{2})/', $value, $matches) !== 1) {
            return null;
        }

        return $matches[1].'-'.$matches[2];
    }
}
