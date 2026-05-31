<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PaymentCheckout;
use App\Form\ProfileType;
use App\Repository\MerchOrderRepository;
use App\Repository\TicketOrderRepository;
use App\Repository\PaymentCheckoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profil/{id}', name: 'profile_show')]
    public function show(
        User $user,
        MerchOrderRepository $merchOrderRepository,
        TicketOrderRepository $ticketOrderRepository,
        PaymentCheckoutRepository $paymentCheckoutRepository
    ): Response
    {
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

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'merchOrders' => $merchOrders,
            'ticketOrders' => $ticketOrders,
            'paymentCheckouts' => $paymentCheckouts,
        ]);
    }

    #[Route('/profil/commande/{id}', name: 'profile_order_show')]
    public function orderShow(
        PaymentCheckout $checkout,
        \App\Repository\MerchRepository $merchRepository,
        \App\Repository\TicketRepository $ticketRepository
    ): Response {
        $current = $this->getUser();
        if (!$current || (!$this->isGranted('ROLE_ADMIN') && $checkout->getUser()?->getId() !== $current->getId())) {
            throw $this->createAccessDeniedException("Tu ne peux voir que tes commandes.");
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
                    'title' => $row['title'] ?? ($product?->getTitle() ?? 'Produit'),
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
                    'title' => $row['title'] ?? ($ticket?->getTitle() ?? 'Match'),
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
                        'title' => $row['title'] ?? 'Match',
                        'opponent' => $row['opponent'] ?? '',
                        'match_date' => $row['match_date'] ?? null,
                        'quantity' => (int) ($row['quantity'] ?? 1),
                        'unit_price' => (float) ($row['unit_price'] ?? 0),
                        'total' => (float) ($row['total'] ?? 0),
                    ];
                } else {
                    $merchItems[] = [
                        'title' => $row['title'] ?? 'Produit',
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

    #[Route('/profil/{id}/edit', name: 'profile_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException("Tu ne peux modifier que ton propre compte.");
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

            $this->addFlash('success', 'Profil mis à jour !');

            return $this->redirectToRoute('profile_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
