<?php

namespace App\Controller;

use App\Entity\PaymentCheckout;
use App\Entity\User;
use App\Repository\MerchRepository;
use App\Repository\TicketRepository;
use App\Service\CartService;
use App\Service\MailService;
use App\Service\SumupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'cart_index')]
    public function index(
        SessionInterface $session,
        MerchRepository $merchRepository,
        TicketRepository $ticketRepository,
        CartService $cartService
    ): Response {
        $merchCart = $cartService->getMerchCart($session);
        $ticketCart = $cartService->getTicketCart($session);

        $merchItems = [];
        $ticketItems = [];
        $total = 0.0;
        $merchTotal = 0.0;
        $ticketTotal = 0.0;

        $merchIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $merchCart)));
        $merchProducts = $merchIds ? $merchRepository->findBy(['id' => $merchIds]) : [];
        $merchById = [];
        foreach ($merchProducts as $p) {
            $merchById[$p->getId()] = $p;
        }

        foreach ($merchCart as $key => $row) {
            $product = $merchById[$row['id']] ?? null;
            if (!$product) {
                continue;
            }
            $lineTotal = $product->getPrice() * (int) $row['quantity'];
            $merchTotal += $lineTotal;
            $merchItems[] = [
                'key' => $key,
                'product' => $product,
                'size' => $row['size'],
                'quantity' => (int) $row['quantity'],
                'lineTotal' => $lineTotal,
            ];
        }

        $ticketIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $ticketCart)));
        $tickets = $ticketIds ? $ticketRepository->findBy(['id' => $ticketIds]) : [];
        $ticketById = [];
        foreach ($tickets as $t) {
            $ticketById[$t->getId()] = $t;
        }

        foreach ($ticketCart as $key => $row) {
            $ticket = $ticketById[$row['id']] ?? null;
            if (!$ticket) {
                continue;
            }
            $lineTotal = $ticket->getPrice() * (int) $row['quantity'];
            $ticketTotal += $lineTotal;
            $ticketItems[] = [
                'key' => $key,
                'ticket' => $ticket,
                'quantity' => (int) $row['quantity'],
                'lineTotal' => $lineTotal,
            ];
        }

        $total = $merchTotal + $ticketTotal;

        return $this->render('cart/index.html.twig', [
            'merchItems' => $merchItems,
            'ticketItems' => $ticketItems,
            'total' => $total,
            'merchTotal' => $merchTotal,
            'ticketTotal' => $ticketTotal,
            'emailMerch' => $cartService->getMerchEmail($session),
            'emailTicket' => $cartService->getTicketEmail($session),
        ]);
    }

    #[Route('/panier/supprimer/{type}/{key}', name: 'cart_remove', methods: ['POST'])]
    public function remove(string $type, string $key, SessionInterface $session, CartService $cartService): Response
    {
        if ($type === 'merch') {
            $cartService->removeMerch($session, $key);
        } elseif ($type === 'ticket') {
            $cartService->removeTicket($session, $key);
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/vider', name: 'cart_clear', methods: ['POST'])]
    public function clear(SessionInterface $session, CartService $cartService): Response
    {
        $cartService->clearMerch($session);
        $cartService->clearTicket($session);
        $session->save();
        $this->addFlash('success', 'Panier vide.');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/checkout', name: 'cart_checkout', methods: ['POST'])]
    public function checkout(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        MerchRepository $merchRepository,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em,
        SumupService $sumupService
    ): Response {
        $merchCart = $cartService->getMerchCart($session);
        $ticketCart = $cartService->getTicketCart($session);

        if (!$merchCart && !$ticketCart) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) $request->request->get('email', '');
        $firstName = $this->getUser() instanceof User ? (string) ($this->getUser()->getPrenom() ?? '') : (string) $request->request->get('first_name', '');
        $lastName = $this->getUser() instanceof User ? (string) ($this->getUser()->getNom() ?? '') : (string) $request->request->get('last_name', '');

        if (!($this->getUser() instanceof User)) {
            if ($email === '' || $firstName === '' || $lastName === '') {
                $this->addFlash('danger', 'Nom, prenom et email requis.');
                return $this->redirectToRoute('cart_index');
            }
        }

        $cartService->setMerchEmail($session, $email);
        $cartService->setTicketEmail($session, $email);

        $lines = [];
        $total = 0.0;

        $merchIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $merchCart)));
        $merchProducts = $merchIds ? $merchRepository->findBy(['id' => $merchIds]) : [];
        $merchById = [];
        foreach ($merchProducts as $p) {
            $merchById[$p->getId()] = $p;
        }

        foreach ($merchCart as $row) {
            $product = $merchById[$row['id']] ?? null;
            if (!$product) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $size = (string) $row['size'];
            $stock = $product->getStockForSize($size);
            $available = $stock?->getQuantity() ?? 0;
            if ($available < $qty) {
                $this->addFlash('danger', sprintf('Stock insuffisant pour %s (%s).', $product->getTitle(), $size));
                return $this->redirectToRoute('cart_index');
            }

            $unit = (float) $product->getPrice();
            $lineTotal = round($unit * $qty, 2);
            $total += $lineTotal;
            $lines[] = [
                'type' => 'merch',
                'merch_id' => $product->getId(),
                'title' => (string) $product->getTitle(),
                'size' => $size,
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $lineTotal,
            ];
        }

        $ticketIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $ticketCart)));
        $tickets = $ticketIds ? $ticketRepository->findBy(['id' => $ticketIds]) : [];
        $ticketById = [];
        foreach ($tickets as $t) {
            $ticketById[$t->getId()] = $t;
        }

        foreach ($ticketCart as $row) {
            $ticket = $ticketById[$row['id']] ?? null;
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
                'type' => 'ticket',
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

        if ($total <= 0.0) {
            $this->addFlash('warning', 'Cette reservation est gratuite, utilise le bouton Reserver.');
            return $this->redirectToRoute('cart_index');
        }

        $reference = strtoupper(bin2hex(random_bytes(6)));

        $checkout = new PaymentCheckout();
        $checkout->setType('mixed')
            ->setStatus('pending')
            ->setCheckoutReference($reference)
            ->setAmount($total)
            ->setCurrency('EUR')
            ->setUser($this->getUser() instanceof User ? $this->getUser() : null)
            ->setEmail($email !== '' ? $email : null)
            ->setCustomerFirstName($firstName !== '' ? $firstName : null)
            ->setCustomerLastName($lastName !== '' ? $lastName : null)
            ->setCart($lines)
            ->setCreatedAt(new \DateTime());
        $em->persist($checkout);
        $em->flush();

        $description = sprintf('Panier - %d article(s)', count($lines));
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

    #[Route('/panier/checkout-cash', name: 'cart_checkout_cash', methods: ['POST'])]
    public function checkoutCash(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        MerchRepository $merchRepository,
        TicketRepository $ticketRepository,
        EntityManagerInterface $em,
        MailService $mailService
    ): Response {
        $merchCart = $cartService->getMerchCart($session);
        $ticketCart = $cartService->getTicketCart($session);

        if (!$merchCart && !$ticketCart) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) $request->request->get('email', '');
        $firstName = $this->getUser() instanceof User ? (string) ($this->getUser()->getPrenom() ?? '') : (string) $request->request->get('first_name', '');
        $lastName = $this->getUser() instanceof User ? (string) ($this->getUser()->getNom() ?? '') : (string) $request->request->get('last_name', '');

        if (!($this->getUser() instanceof User)) {
            if ($email === '' || $firstName === '' || $lastName === '') {
                $this->addFlash('danger', 'Nom, prenom et email requis.');
                return $this->redirectToRoute('cart_index');
            }
        }

        $cartService->setMerchEmail($session, $email);
        $cartService->setTicketEmail($session, $email);

        $lines = [];
        $total = 0.0;

        $merchIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $merchCart)));
        $merchProducts = $merchIds ? $merchRepository->findBy(['id' => $merchIds]) : [];
        $merchById = [];
        foreach ($merchProducts as $p) {
            $merchById[$p->getId()] = $p;
        }

        foreach ($merchCart as $row) {
            $product = $merchById[$row['id']] ?? null;
            if (!$product) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $size = (string) $row['size'];
            $stock = $product->getStockForSize($size);
            $available = $stock?->getQuantity() ?? 0;
            if ($available < $qty) {
                $this->addFlash('danger', sprintf('Stock insuffisant pour %s (%s).', $product->getTitle(), $size));
                return $this->redirectToRoute('cart_index');
            }

            $unit = (float) $product->getPrice();
            $lineTotal = round($unit * $qty, 2);
            $total += $lineTotal;
            $lines[] = [
                'type' => 'merch',
                'merch_id' => $product->getId(),
                'title' => (string) $product->getTitle(),
                'size' => $size,
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $lineTotal,
            ];

            if ($stock) {
                $stock->setQuantity(max(0, $available - $qty));
                $em->persist($stock);
            }

            $order = new \App\Entity\MerchOrder();
            $order->setUser($this->getUser() instanceof User ? $this->getUser() : null);
            $order->setMerch($product);
            $order->setEmail($email !== '' ? $email : null);
            $order->setCustomerFirstName($firstName !== '' ? $firstName : null);
            $order->setCustomerLastName($lastName !== '' ? $lastName : null);
            $order->setSize($size);
            $order->setQuantity($qty);
            $order->setUnitPrice($unit);
            $order->setTotalPrice($lineTotal);
            $order->setPaymentMethod($lineTotal <= 0.0 ? 'free' : 'cash');
            $order->setExecuted(false);
            $order->setCreatedAt(new \DateTime());
            $em->persist($order);
        }

        $ticketIds = array_values(array_unique(array_map(static fn ($i) => $i['id'], $ticketCart)));
        $tickets = $ticketIds ? $ticketRepository->findBy(['id' => $ticketIds]) : [];
        $ticketById = [];
        foreach ($tickets as $t) {
            $ticketById[$t->getId()] = $t;
        }

        foreach ($ticketCart as $row) {
            $ticket = $ticketById[$row['id']] ?? null;
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
                'type' => 'ticket',
                'ticket_id' => $ticket->getId(),
                'title' => (string) $ticket->getTitle(),
                'opponent' => (string) $ticket->getOpponent(),
                'quantity' => $qty,
                'unit_price' => $unit,
                'total' => $lineTotal,
            ];

            $ticket->setStock(max(0, $available - $qty));
            $em->persist($ticket);

            $order = new \App\Entity\TicketOrder();
            $order->setUser($this->getUser() instanceof User ? $this->getUser() : null);
            $order->setTicket($ticket);
            $order->setEmail($email !== '' ? $email : null);
            $order->setCustomerFirstName($firstName !== '' ? $firstName : null);
            $order->setCustomerLastName($lastName !== '' ? $lastName : null);
            $order->setQuantity($qty);
            $order->setUnitPrice($unit);
            $order->setTotalPrice($lineTotal);
            $order->setPaymentMethod($lineTotal <= 0.0 ? 'free' : 'cash');
            $order->setPaid($lineTotal <= 0.0);
            $order->setCreatedAt(new \DateTime());
            $em->persist($order);
        }

        if (!$lines) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $reference = strtoupper(bin2hex(random_bytes(6)));
        $checkout = new \App\Entity\PaymentCheckout();
        $isFree = $total <= 0.0;

        $checkout->setType($isFree ? 'free' : 'cash')
            ->setStatus($isFree ? 'paid' : 'pending')
            ->setCheckoutReference($reference)
            ->setAmount($total)
            ->setCurrency('EUR')
            ->setUser($this->getUser() instanceof User ? $this->getUser() : null)
            ->setEmail($email !== '' ? $email : null)
            ->setCustomerFirstName($firstName !== '' ? $firstName : null)
            ->setCustomerLastName($lastName !== '' ? $lastName : null)
            ->setCart($lines)
            ->setCreatedAt(new \DateTime());
        if ($isFree) {
            $checkout->setPaidAt(new \DateTime());
            $checkout->setProcessedAt(new \DateTime());
        }
        $em->persist($checkout);

        $em->flush();

        $cartService->clearMerch($session);
        $cartService->clearTicket($session);
        $session->save();

        if ($email !== '') {
            try {
                $mailService->send(
                    to: $email,
                    subject: $isFree ? 'Recapitulatif de ta reservation' : 'Recapitulatif de ta commande (paiement liquide)',
                    template: 'email/checkout_recap.html.twig',
                    context: [
                        'checkout' => $checkout,
                        'ticketLines' => array_filter($lines, fn ($l) => ($l['type'] ?? '') === 'ticket'),
                        'merchLines' => array_filter($lines, fn ($l) => ($l['type'] ?? '') === 'merch'),
                        'cashNotice' => !$isFree,
                    ],
                );
                $checkout->setEmailSentAt(new \DateTime());
                $em->flush();
            } catch (\Throwable) {
                // ignore mail failures
            }
        }

        return $this->redirectToRoute('checkout_cash_thankyou', [
            'ref' => $reference,
        ]);
    }
}
