<?php

namespace App\Controller;

use App\Entity\Merch;
use App\Entity\User;
use App\Form\MerchPurchaseType;
use App\Repository\MerchRepository;
use App\Repository\MerchCategoryRepository;
use App\Service\MerchSizingService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $criteria = $selectedCategory ? ['category' => $selectedCategory] : [];
        $items = $merchRepository->findBy($criteria, ['id' => 'DESC']);

        return $this->render('merch/list.html.twig', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    #[Route('/{id<\\d+>}', name: 'merch_show')]
    public function show(Merch $item): Response
    {
        return $this->render('merch/show.html.twig', [
            'item' => $item,
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
                    $form->addError(new \Symfony\Component\Form\FormError('Stock non configuré pour cette taille.'));
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
                            subject: sprintf('🧾 Récapitulatif de ta commande %s', $orderNumber),
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

                        $this->addFlash('success', 'Ta commande a bien été prise en compte. Un email récapitulatif vient de t’être envoyé.');
                    } catch (\Throwable) {
                        $this->addFlash('warning', 'Commande enregistrée, mais l’envoi de l’email a échoué. Réessaie plus tard.');
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
}
