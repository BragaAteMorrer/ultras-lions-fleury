<?php

namespace App\Controller;

use App\Entity\Merch;
use App\Entity\User;
use App\Entity\PaymentCheckout;
use App\Form\MerchPurchaseType;
use App\Repository\MerchRepository;
use App\Repository\MerchCategoryRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Service\MerchSizingService;
use App\Service\CartService;
use App\Service\SumupService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/table-de-vente')]
class MerchController extends AbstractController
{
    #[Route('/', name: 'merch_index')]
    public function index(Request $request, MerchRepository $merchRepository, MerchCategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $selectedCategory = null;

        $slug = $request->query->getString('category');
        if ($slug !== '') {
            $selectedCategory = $categoryRepository->findOneBy(['slug' => $slug]);
        }

        $isMember = $this->getUser() !== null;
        $items = $merchRepository->findVisibleForUser($selectedCategory, $isMember);

        return $this->render('merch/list.html.twig', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    #[Route('/{id<\\d+>}', name: 'merch_show')]
    public function show(Merch $item, MerchSizingService $sizingService): Response
    {
        $user = $this->getUser() instanceof User ? $this->getUser() : null;
        $sizeChoices = $sizingService->getSizeChoicesForMerch($item);
        $preferredSize = $sizingService->getPreferredSizeForUser($item, $user);

        $isMember = $this->getUser() !== null;
        if (!$isMember && $item->getAudience() === 'members') {
            throw $this->createNotFoundException();
        }

        return $this->render('merch/show.html.twig', [
            'item' => $item,
            'sizeChoices' => $sizeChoices,
            'preferredSize' => $preferredSize,
        ]);
    }

    #[Route('/{id<\\d+>}/acheter', name: 'merch_buy')]
    public function buy(
        Request $request,
        Merch $item,
        MailService $mailService,
        MerchSizingService $sizingService,
        EntityManagerInterface $em
    ): Response {
        $isMember = $this->getUser() !== null;
        if (!$isMember && $item->getAudience() === 'members') {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser() instanceof User ? $this->getUser() : null;
        $sizeChoices = $sizingService->getSizeChoicesForMerch($item);
        $preferredSize = $sizingService->getPreferredSizeForUser($item, $user);
        $stockConfigured = $item->getStocks()->count() > 0;

        if ($preferredSize !== null && !in_array($preferredSize, array_values($sizeChoices), true)) {
            $preferredSize = null;
        }

        $form = $this->createForm(MerchPurchaseType::class, null, [
            'require_email' => $user === null,
            'sizes' => $sizeChoices,
            'preferred_size' => $preferredSize,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (array) $form->getData();
            $quantity = (int) ($data['quantity'] ?? 1);
            $note = (string) ($data['note'] ?? '');
            $size = (string) ($data['size'] ?? 'TU');

            $allowedSizes = array_values($sizeChoices);
            if ($sizeChoices && !in_array($size, $allowedSizes, true)) {
                $form->addError(new \Symfony\Component\Form\FormError('Taille invalide.'));
            } else {
                $stock = $item->getStockForSize($size);
                $available = $stock?->getQuantity() ?? 0;

                if ($stock === null) {
                    $form->addError(new \Symfony\Component\Form\FormError('Stock non configure pour cette taille.'));
                } elseif ($available <= 0) {
                    $form->addError(new \Symfony\Component\Form\FormError('Rupture de stock pour cette taille.'));
                } elseif ($quantity > $available) {
                    $form->addError(new \Symfony\Component\Form\FormError(sprintf('Stock insuffisant (disponible: %d).', $available)));
                } else {
                    $to = $user?->getUserIdentifier() ?: (string) ($data['email'] ?? '');

                    $unitPrice = (float) $item->getPrice();
                    $totalPrice = round($unitPrice * $quantity, 2);

                    $orderNumber = strtoupper(bin2hex(random_bytes(4)));

                    $stock->setQuantity($available - $quantity);
                    $em->persist($stock);
                    $em->flush();

                    try {
                        $mailService->send(
                            to: $to,
                            subject: sprintf('Recapitulatif de ta commande %s', $orderNumber),
                            template: 'email/merch_order_recap.html.twig',
                            context: [
                                'user' => $user,
                                'order' => [
                                    'number' => $orderNumber,
                                    'orderedAt' => new \DateTimeImmutable(),
                                    'note' => $note,
                                    'lines' => [[
                                        'title' => (string) $item->getTitle(),
                                        'size' => $size,
                                        'quantity' => $quantity,
                                        'unitPrice' => $unitPrice,
                                        'totalPrice' => $totalPrice,
                                    ]],
                                    'total' => $totalPrice,
                                ],
                            ]
                        );

                        $this->addFlash('success', 'Ta commande a bien ete prise en compte. Un email recaptitulatif vient de t\'etre envoye.');
                    } catch (\Throwable) {
                        $this->addFlash('warning', 'Commande enregistree, mais l\'envoi de l\'email a echoue. Reessaie plus tard.');
                    }

                    return $this->redirectToRoute('merch_show', ['id' => $item->getId()]);
                }
            }
        }

        return $this->render('merch/buy.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
            'sizeChoices' => $sizeChoices,
            'stockConfigured' => $stockConfigured,
        ]);
    }

    #[Route('/panier', name: 'merch_cart')]
    public function cart(SessionInterface $session, MerchRepository $merchRepository, CartService $cartService): Response
    {
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/ajouter/{id<\\d+>}', name: 'merch_cart_add', methods: ['POST'])]
    public function addToCart(
        Merch $item,
        Request $request,
        SessionInterface $session,
        CartService $cartService
    ): Response {
        $size = (string) $request->request->get('size', 'TU');
        $qty = (int) $request->request->get('quantity', 1);
        $cartService->addMerch($session, $item->getId(), $size, $qty);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/supprimer/{key}', name: 'merch_cart_remove', methods: ['POST'])]
    public function removeFromCart(string $key, SessionInterface $session, CartService $cartService): Response
    {
        $cartService->removeMerch($session, $key);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/vider', name: 'merch_cart_clear', methods: ['POST'])]
    public function clearCart(SessionInterface $session, CartService $cartService): Response
    {
        $cartService->clearMerch($session);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/checkout', name: 'merch_cart_checkout', methods: ['POST'])]
    public function checkout(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        MerchRepository $merchRepository,
        EntityManagerInterface $em,
        SumupService $sumupService
    ): Response {
        $cart = $cartService->getMerchCart($session);
        if (!$cart) {
            $this->addFlash('warning', 'Ton panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) $request->request->get('email', '');
        $cartService->setMerchEmail($session, $email);

        $ids = array_values(array_unique(array_map(static fn ($i) => $i['id'], $cart)));
        $products = $ids ? $merchRepository->findBy(['id' => $ids]) : [];
        $byId = [];
        foreach ($products as $p) {
            $byId[$p->getId()] = $p;
        }

        $lines = [];
        $total = 0.0;

        foreach ($cart as $row) {
            $product = $byId[$row['id']] ?? null;
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
                'merch_id' => $product->getId(),
                'title' => (string) $product->getTitle(),
                'size' => $size,
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
        $checkout->setType('merch')
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

        $description = sprintf('Table de vente - %d article(s)', count($lines));
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

    #[Route('/panier/checkout-widget', name: 'merch_cart_checkout_widget', methods: ['POST'])]
    public function checkoutWidget(
        Request $request,
        SessionInterface $session,
        CartService $cartService,
        MerchRepository $merchRepository,
        EntityManagerInterface $em,
        SumupService $sumupService
    ): JsonResponse {
        $cart = $cartService->getMerchCart($session);
        if (!$cart) {
            return new JsonResponse(['error' => 'empty_cart', 'message' => 'Ton panier est vide.'], 400);
        }

        $payload = $request->toArray();
        $email = $this->getUser() instanceof User ? $this->getUser()->getUserIdentifier() : (string) ($payload['email'] ?? '');
        if (!($this->getUser() instanceof User) && $email === '') {
            return new JsonResponse(['error' => 'email_required', 'message' => 'Email requis.'], 400);
        }
        $cartService->setMerchEmail($session, $email);

        $ids = array_values(array_unique(array_map(static fn ($i) => $i['id'], $cart)));
        $products = $ids ? $merchRepository->findBy(['id' => $ids]) : [];
        $byId = [];
        foreach ($products as $p) {
            $byId[$p->getId()] = $p;
        }

        $lines = [];
        $total = 0.0;

        foreach ($cart as $row) {
            $product = $byId[$row['id']] ?? null;
            if (!$product) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $size = (string) $row['size'];
            $stock = $product->getStockForSize($size);
            $available = $stock?->getQuantity() ?? 0;
            if ($available < $qty) {
                return new JsonResponse(['error' => 'insufficient_stock', 'message' => 'Stock insuffisant.'], 400);
            }

            $unit = (float) $product->getPrice();
            $lineTotal = round($unit * $qty, 2);
            $total += $lineTotal;
            $lines[] = [
                'merch_id' => $product->getId(),
                'title' => (string) $product->getTitle(),
                'size' => $size,
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
        $checkout->setType('merch')
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

        $description = sprintf('Table de vente - %d article(s)', count($lines));
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

    #[Route('/panier/apm', name: 'merch_cart_apm', methods: ['POST'])]
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
        if (!$checkout || $checkout->getType() !== 'merch') {
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
}
