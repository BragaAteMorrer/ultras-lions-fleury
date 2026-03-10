<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketPurchaseType;
use App\Repository\TicketCategoryRepository;
use App\Repository\TicketRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $criteria = $selectedCategory ? ['category' => $selectedCategory] : [];
        $items = $ticketRepository->findBy($criteria, ['matchDate' => 'ASC']);

        return $this->render('ticket/list.html.twig', [
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    #[Route('/{id<\\d+>}', name: 'ticket_show')]
    public function show(Ticket $item): Response
    {
        return $this->render('ticket/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id<\\d+>}/reserver', name: 'ticket_buy')]
    public function buy(
        Request $request,
        Ticket $item,
        MailService $mailService,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser() instanceof User ? $this->getUser() : null;

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
                $to = $user?->getUserIdentifier() ?: (string) ($data['email'] ?? '');

                $unitPrice = (float) $item->getPrice();
                $totalPrice = round($unitPrice * $quantity, 2);
                $orderNumber = strtoupper(bin2hex(random_bytes(4)));

                $item->setStock($available - $quantity);
                $em->persist($item);
                $em->flush();

                try {
                    $mailService->send(
                        to: $to,
                        subject: sprintf('Récapitulatif de ta réservation %s', $orderNumber),
                        template: 'email/ticket_order_recap.html.twig',
                        context: [
                            'user' => $user,
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

                    $this->addFlash('success', 'Ta réservation a bien été prise en compte. Un email récapitulatif vient de t’être envoyé.');
                } catch (\Throwable) {
                    $this->addFlash('warning', 'Réservation enregistrée, mais l’envoi de l’email a échoué. Réessaie plus tard.');
                }

                return $this->redirectToRoute('ticket_show', ['id' => $item->getId()]);
            }
        }

        return $this->render('ticket/buy.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }
}

