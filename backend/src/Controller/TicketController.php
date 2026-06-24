<?php

namespace App\Controller;

use App\Entity\BilletwebLead;
use App\Entity\Ticket;
use App\Entity\TicketOrder;
use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Form\TicketPurchaseType;
use App\Repository\TicketCategoryRepository;
use App\Repository\TicketRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Service\MailService;
use App\Service\CartService;
use App\Service\ManagedAccountService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/billetterie')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'ticket_index')]
    public function index(Request $request, TicketRepository $ticketRepository, TicketCategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $selectedCategory = null;

        $slug = $request->query->getString('category');
        if ($slug !== '') {
            $selectedCategory = $categoryRepository->findOneBy(['slug' => $slug]);
        }

        $isMember = $this->getUser() !== null;
        $items = $ticketRepository->findPublicTickets($selectedCategory, $isMember);

        return $this->render('ticket/list.html.twig', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    #[Route('/{id<\\d+>}', name: 'ticket_show')]
    public function show(Ticket $item, TicketRepository $ticketRepository, SessionInterface $session): Response
    {
        $isMember = $this->getUser() !== null;
        if (!$ticketRepository->isVisibleForUser($item, $isMember)) {
            throw $this->createNotFoundException();
        }

        return $this->render('ticket/show.html.twig', [
            'item' => $item,
            'billetwebReady' => $item->usesBilletweb() && $session->get($this->billetwebSessionKey($item), false),
        ]);
    }

    #[Route('/{id<\\d+>}/billetweb-preinscription', name: 'ticket_billetweb_lead', methods: ['POST'])]
    public function billetwebLead(
        Request $request,
        SessionInterface $session,
        Ticket $item,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em
    ): Response {
        $isMember = $this->getUser() !== null;
        if (!$item->usesBilletweb() || !$ticketRepository->isVisibleForUser($item, $isMember)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('billetweb_lead_' . $item->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Formulaire invalide, reessaie.');
            return $this->redirectToRoute('ticket_show', ['id' => $item->getId(), '_fragment' => 'reservation']);
        }

        $firstName = trim((string) $request->request->get('firstName', ''));
        $lastName = trim((string) $request->request->get('lastName', ''));
        $email = trim((string) $request->request->get('email', ''));

        if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Renseigne ton prenom, ton nom et un email valide.');
            return $this->redirectToRoute('ticket_show', ['id' => $item->getId(), '_fragment' => 'reservation']);
        }

        $user = $this->getUser() instanceof User ? $this->getUser() : null;

        $lead = new BilletwebLead();
        $lead
            ->setTicket($item)
            ->setUser($user)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email);

        $em->persist($lead);
        $em->flush();

        $session->set($this->billetwebSessionKey($item), true);

        return $this->redirectToRoute('ticket_show', ['id' => $item->getId(), '_fragment' => 'reservation']);
    }

    #[Route('/{id<\\d+>}/reserver', name: 'ticket_buy')]
    public function buy(
        Request $request,
        Ticket $item,
        TicketRepository $ticketRepository,
        MailService $mailService,
        EntityManagerInterface $em,
        ManagedAccountService $managedAccountService
    ): Response {
        $isMember = $this->getUser() !== null;
        if (!$ticketRepository->isVisibleForUser($item, $isMember)) {
            throw $this->createNotFoundException();
        }

        if ($item->usesBilletweb()) {
            return $this->redirectToRoute('ticket_show', ['id' => $item->getId()]);
        }

        $user = $this->getUser() instanceof User ? $this->getUser() : null;
        $orderableUsers = $user instanceof User ? $managedAccountService->getOrderableUsers($user) : [];

        $form = $this->createForm(TicketPurchaseType::class, null, [
            'require_email' => $user === null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (array) $form->getData();
            $quantity = (int) ($data['quantity'] ?? 1);
            $note = (string) ($data['note'] ?? '');

            $available = (int) $item->getStock();
            if ($available <= 0) {
                $form->addError(new \Symfony\Component\Form\FormError('Plus de places disponibles.'));
            } elseif ($quantity > $available) {
                $form->addError(new \Symfony\Component\Form\FormError(sprintf('Stock insuffisant (disponible: %d).', $available)));
            } else {
                $orderUser = $user instanceof User ? $managedAccountService->resolveOrderUser($user, $request->request->get('beneficiary_user_id')) : null;
                $to = $orderUser?->getUserIdentifier() ?: (string) ($data['email'] ?? '');

                $unitPrice = (float) $item->getPrice();
                $totalPrice = round($unitPrice * $quantity, 2);
                $orderNumber = strtoupper(bin2hex(random_bytes(4)));

                $item->setStock($available - $quantity);
                $em->persist($item);

                $order = new TicketOrder();
                $order->setUser($orderUser);
                $order->setTicket($item);
                $order->setEmail($to !== '' ? $to : null);
                $order->setQuantity($quantity);
                $order->setUnitPrice($unitPrice);
                $order->setTotalPrice($totalPrice);
                $order->setNote($note !== '' ? $note : null);
                $order->setCreatedAt(new \DateTime());
                $em->persist($order);

                $em->flush();

                try {
                    $mailService->send(
                        to: $to,
                        subject: sprintf('Recapitulatif de ta reservation %s', $orderNumber),
                        template: 'email/ticket_order_recap.html.twig',
                        context: [
                            'user' => $orderUser,
                            'order' => [
                                'number' => $orderNumber,
                                'orderedAt' => new \DateTimeImmutable(),
                                'note' => $note,
                                'lines' => [[
                                    'title' => (string) $item->getTitle(),
                                    'matchDate' => $item->getMatchDate(),
                                    'opponent' => (string) $item->getOpponent(),
                                    'quantity' => $quantity,
                                    'unitPrice' => $unitPrice,
                                    'totalPrice' => $totalPrice,
                                ]],
                                'total' => $totalPrice,
                            ],
                        ]
                    );

                    $this->addFlash('success', 'Ta reservation a bien ete prise en compte. Un email recapitulatif vient de t\'etre envoye.');
                } catch (\Throwable) {
                    $this->addFlash('warning', 'Reservation enregistree, mais l\'envoi de l\'email a echoue. Reessaie plus tard.');
                }

                return $this->redirectToRoute('ticket_show', ['id' => $item->getId()]);
            }
        }

        return $this->render('ticket/buy.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
            'orderableUsers' => $orderableUsers,
        ]);
    }

    #[Route('/panier', name: 'ticket_cart')]
    public function cart(SessionInterface $session, TicketRepository $ticketRepository, CartService $cartService): Response
    {
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/ajouter/{id<\\d+>}', name: 'ticket_cart_add', methods: ['POST'])]
    public function addToCart(
        Ticket $item,
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        TicketRepository $ticketRepository
    ): Response {
        $isMember = $this->getUser() !== null;
        if (!$ticketRepository->isVisibleForUser($item, $isMember)) {
            throw $this->createNotFoundException();
        }

        $qty = (int) $request->request->get('quantity', 1);
        $cartService->addTicket($session, $item->getId(), $qty);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/supprimer/{key}', name: 'ticket_cart_remove', methods: ['POST'])]
    public function removeFromCart(string $key, SessionInterface $session, CartService $cartService): Response
    {
        $cartService->removeTicket($session, $key);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/vider', name: 'ticket_cart_clear', methods: ['POST'])]
    public function clearCart(SessionInterface $session, CartService $cartService): Response
    {
        $cartService->clearTicket($session);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/checkout', name: 'ticket_cart_checkout', methods: ['POST'])]
    public function checkout(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em,
        SumupService $sumupService
    ): Response {
        $cart = $cartService->getTicketCart($session);
        if (!$cart) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) $request->request->get('email', '');
        $cartService->setTicketEmail($session, $email);

        $ids = array_values(array_unique(array_map(static fn ($i) => $i['id'], $cart)));
        $tickets = $ids ? $ticketRepository->findBy(['id' => $ids]) : [];
        $byId = [];
        foreach ($tickets as $t) {
            $byId[$t->getId()] = $t;
        }

        $lines = [];
        $total = 0.0;

        foreach ($cart as $row) {
            $ticket = $byId[$row['id']] ?? null;
            if (!$ticket) {
                continue;
            }
            if (!$ticketRepository->isVisibleForUser($ticket, $this->getUser() !== null)) {
                $this->addFlash('danger', sprintf('La billetterie pour %s n\'est plus disponible.', $ticket->getOpponent()));
                return $this->redirectToRoute('cart_index');
            }
            $qty = (int) $row['quantity'];
            $available = (int) $ticket->getStock();
            if ($available < $qty) {
                $this->addFlash('danger', sprintf('Stock insuffisant pour %s.', $ticket->getOpponent()));
                return $this->redirectToRoute('cart_index');
            }

            $unit = (float) $ticket->getPrice();
            $lineTotal = round($unit * $qty, 2);
            $total += $lineTotal;
            $lines[] = [
                'ticket_id' => $ticket->getId(),
                'title' => (string) $ticket->getTitle(),
                'opponent' => (string) $ticket->getOpponent(),
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $lineTotal,
            ];
        }

        if (!$lines) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $reference = strtoupper(bin2hex(random_bytes(6)));

        $checkout = new PaymentCheckout();
        $checkout->setType('ticket')
            ->setStatus('pending')
            ->setCheckoutReference($reference)
            ->setAmount($total)
            ->setCurrency('EUR')
            ->setUser($this->getUser() instanceof User ? $this->getUser() : null)
            ->setEmail($email !== '' ? $email : null)
            ->setCart($lines)
            ->setCreatedAt(new \DateTime());
        $em->persist($checkout);
        $em->flush();

        $description = sprintf('Billetterie - %d billet(s)', count($lines));
        $response = $sumupService->createHostedCheckout($total, 'EUR', $reference, $description);

        if (!empty($response['_error'])) {
            $this->addFlash('danger', sprintf(
                'SumUp error (%s): %s',
                $response['_status'] ?? 'n/a',
                $response['_message'] ?? 'Erreur'
            ));
            $checkout->setStatus('failed');
            $em->flush();
            return $this->redirectToRoute('cart_index');
        }

        $checkoutId = $response['id'] ?? null;
        $hostedUrl = $response['hosted_checkout_url'] ?? null;

        if (!$checkoutId || !$hostedUrl) {
            $checkout->setStatus('failed');
            $em->flush();
            $this->addFlash('danger', 'Impossible de creer le paiement. Reessaie.');
            return $this->redirectToRoute('cart_index');
        }

        $checkout->setSumupCheckoutId($checkoutId);
        $em->flush();

        return $this->redirect($hostedUrl);
    }

    #[Route('/panier/checkout-widget', name: 'ticket_cart_checkout_widget', methods: ['POST'])]
    public function checkoutWidget(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em,
        SumupService $sumupService
    ): JsonResponse {
        $cart = $cartService->getTicketCart($session);
        if (!$cart) {
            return new JsonResponse(['error' => 'empty_cart', 'message' => 'Ton panier est vide.'], 400);
        }

        $payload = $request->toArray();
        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) ($payload['email'] ?? '');
        if (!($this->getUser() instanceof User) && $email === '') {
            return new JsonResponse(['error' => 'email_required', 'message' => 'Email requis.'], 400);
        }
        $cartService->setTicketEmail($session, $email);

        $ids = array_values(array_unique(array_map(static fn ($i) => $i['id'], $cart)));
        $tickets = $ids ? $ticketRepository->findBy(['id' => $ids]) : [];
        $byId = [];
        foreach ($tickets as $t) {
            $byId[$t->getId()] = $t;
        }

        $lines = [];
        $total = 0.0;

        foreach ($cart as $row) {
            $ticket = $byId[$row['id']] ?? null;
            if (!$ticket) {
                continue;
            }
            if (!$ticketRepository->isVisibleForUser($ticket, $this->getUser() !== null)) {
                return new JsonResponse(['error' => 'ticket_unavailable', 'message' => 'Cette billetterie n\'est plus disponible.'], 400);
            }
            $qty = (int) $row['quantity'];
            $available = (int) $ticket->getStock();
            if ($available < $qty) {
                return new JsonResponse(['error' => 'insufficient_stock', 'message' => 'Stock insuffisant.'], 400);
            }

            $unit = (float) $ticket->getPrice();
            $lineTotal = round($unit * $qty, 2);
            $total += $lineTotal;
            $lines[] = [
                'ticket_id' => $ticket->getId(),
                'title' => (string) $ticket->getTitle(),
                'opponent' => (string) $ticket->getOpponent(),
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $lineTotal,
            ];
        }

        if (!$lines) {
            return new JsonResponse(['error' => 'empty_cart', 'message' => 'Ton panier est vide.'], 400);
        }

        $reference = strtoupper(bin2hex(random_bytes(6)));

        $checkout = new PaymentCheckout();
        $checkout->setType('ticket')
            ->setStatus('pending')
            ->setCheckoutReference($reference)
            ->setAmount($total)
            ->setCurrency('EUR')
            ->setUser($this->getUser() instanceof User ? $this->getUser() : null)
            ->setEmail($email !== '' ? $email : null)
            ->setCart($lines)
            ->setCreatedAt(new \DateTime());
        $em->persist($checkout);
        $em->flush();

        $description = sprintf('Billetterie - %d billet(s)', count($lines));
        $response = $sumupService->createHostedCheckout($total, 'EUR', $reference, $description);

        if (!empty($response['_error'])) {
            $checkout->setStatus('failed');
            $em->flush();
            return new JsonResponse([
                'error' => 'sumup_error',
                'message' => $response['_message'] ?? 'SumUp error',
            ], 400);
        }

        $checkoutId = $response['id'] ?? null;
        if (!$checkoutId) {
            $checkout->setStatus('failed');
            $em->flush();
            return new JsonResponse(['error' => 'missing_checkout_id', 'message' => 'Checkout ID manquant.'], 400);
        }

        $checkout->setSumupCheckoutId($checkoutId);
        $em->flush();

        $methods = $sumupService->listPaymentMethods($checkoutId);
        $methodItems = $methods['items'] ?? [];

        return new JsonResponse([
            'checkoutId' => $checkoutId,
            'methods' => $methodItems,
        ]);
    }

    #[Route('/panier/apm', name: 'ticket_cart_apm', methods: ['POST'])]
    public function apmProcess(
        Request $request,
        PaymentCheckoutRepository $paymentCheckoutRepository,
        SumupService $sumupService
    ): JsonResponse {
        $payload = $request->toArray();
        $checkoutId = (string) ($payload['checkoutId'] ?? '');
        $paymentType = (string) ($payload['paymentType'] ?? '');
        $personalDetails = (array) ($payload['personalDetails'] ?? []);

        if ($checkoutId === '' || $paymentType === '') {
            return new JsonResponse(['error' => 'missing_data', 'message' => 'Donnees manquantes.'], 400);
        }

        $checkout = $paymentCheckoutRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);
        if (!$checkout || $checkout->getType() !== 'ticket') {
            return new JsonResponse(['error' => 'unknown_checkout', 'message' => 'Checkout inconnu.'], 404);
        }

        $response = $sumupService->processCheckout($checkoutId, $paymentType, $personalDetails);

        if (!empty($response['_error'])) {
            return new JsonResponse([
                'error' => 'sumup_error',
                'message' => $response['_message'] ?? 'SumUp error',
                'details' => $response['_details'] ?? null,
            ], 400);
        }

        return new JsonResponse($response);
    }

    private function billetwebSessionKey(Ticket $ticket): string
    {
        return 'billetweb_lead_ticket_' . $ticket->getId();
    }
}
