<?php

namespace App\Controller\Admin;

use App\Entity\Merch;
use App\Entity\User;
use App\Form\MerchPhysicalSaleType;
use App\Service\AdminNavigationBuilder;
use App\Service\MerchPhysicalSaleManager;
use App\Service\MerchStockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/manage/ventes-physiques/merch')]
class MerchPhysicalSaleController extends AbstractController
{
    public function __construct(
        private readonly AdminNavigationBuilder $adminNavigationBuilder,
        private readonly MerchStockManager $stockManager,
        private readonly MerchPhysicalSaleManager $physicalSaleManager,
    ) {
    }

    #[Route('', name: 'app_admin_merch_physical_sale_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(MerchPhysicalSaleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (array) $form->getData();

            $merch = $data['merch'] ?? null;
            $size = trim((string) ($data['size'] ?? ''));
            $sumupSku = trim((string) ($data['sumupSku'] ?? ''));
            $quantity = max(1, (int) ($data['quantity'] ?? 1));
            $paymentMethod = trim((string) ($data['paymentMethod'] ?? 'sumup_pos'));
            $useMemberPrice = (bool) ($data['useMemberPrice'] ?? false);
            $sumupReference = trim((string) ($data['sumupReference'] ?? ''));
            $note = trim((string) ($data['note'] ?? ''));

            $stock = null;
            if ($sumupSku !== '') {
                $stock = $this->stockManager->findStockBySku($sumupSku);
                if (!$stock) {
                    $form->addError(new FormError(sprintf('Aucun stock ne correspond au SKU SumUp "%s".', $sumupSku)));
                } else {
                    $merch = $stock->getMerch();
                    $size = $stock->getSize();
                }
            }

            if (!$stock) {
                if (!$merch instanceof Merch) {
                    $form->addError(new FormError('Le produit selectionne est invalide.'));
                } elseif ($size === '') {
                    $form->addError(new FormError('La taille est obligatoire si aucun SKU SumUp n’est renseigne.'));
                } else {
                    $stock = $merch->getStockForSize($size);
                    if (!$stock) {
                        $form->addError(new FormError('Stock non configure pour cette taille.'));
                    }
                }
            }

            if ($stock instanceof \App\Entity\MerchStock && $sumupSku !== '' && $stock->getSumupSku() !== null && $stock->getSumupSku() !== $sumupSku) {
                $form->addError(new FormError('Le SKU SumUp saisi ne correspond pas au stock trouve.'));
            }

            if ($stock instanceof \App\Entity\MerchStock) {
                try {
                    $order = $this->physicalSaleManager->recordSale(
                        merch: $stock->getMerch(),
                        size: $stock->getSize(),
                        quantity: $quantity,
                        paymentMethod: $paymentMethod,
                        useMemberPrice: $useMemberPrice,
                        sumupReference: $sumupReference !== '' ? $sumupReference : null,
                        note: $note !== '' ? $note : null,
                        confirmedBy: $this->getUser() instanceof User ? $this->getUser() : null,
                    );

                    $this->addFlash('success', sprintf(
                        'Vente physique enregistree et stock mis a jour pour la commande #%d.',
                        $order->getId() ?? 0
                    ));

                    return $this->redirectToRoute('custom_admin_crud_show', [
                        'resource' => 'commandes-merch',
                        'id' => $order->getId(),
                    ]);
                } catch (\LogicException $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }
        }

        return $this->render('admin/merch_physical_sale/new.html.twig', [
            'form' => $form->createView(),
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }
}
