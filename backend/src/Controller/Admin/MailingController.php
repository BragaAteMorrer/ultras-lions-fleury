<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailingController extends AbstractController
{
    #[Route('/admin/mailing', name: 'app_admin_mailing', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository, MailService $mailService): Response
    {
        $users = $userRepository->findBy([], ['email' => 'ASC']);
        $selectedIds = array_map('intval', (array) $request->request->all('users'));
        $subject = trim((string) $request->request->get('subject', ''));
        $body = trim((string) $request->request->get('body', ''));

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('admin_mailing', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Formulaire invalide, reessaie.');

                return $this->redirectToRoute('app_admin_mailing');
            }

            $errors = [];
            if ($selectedIds === []) {
                $errors[] = 'Selectionne au moins un utilisateur.';
            }
            if ($subject === '') {
                $errors[] = 'Renseigne un sujet.';
            }
            if ($body === '') {
                $errors[] = 'Redige le corps du mail.';
            }

            if ($errors === []) {
                $sent = $this->sendToSelectedUsers($selectedIds, $subject, $body, $userRepository, $mailService);
                $this->addFlash($sent > 0 ? 'success' : 'warning', sprintf('%d mail(s) envoye(s).', $sent));

                return $this->redirectToRoute('app_admin_mailing');
            }

            foreach ($errors as $error) {
                $this->addFlash('danger', $error);
            }
        }

        return $this->render('admin/mailing.html.twig', [
            'users' => $users,
            'selectedIds' => $selectedIds,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    /**
     * @param int[] $selectedIds
     */
    private function sendToSelectedUsers(
        array $selectedIds,
        string $subject,
        string $body,
        UserRepository $userRepository,
        MailService $mailService
    ): int {
        $users = $userRepository->findBy(['id' => array_values(array_unique($selectedIds))]);
        $sent = 0;

        foreach ($users as $user) {
            if (!$user instanceof User || !$user->getEmail()) {
                continue;
            }

            $mailService->send(
                to: $user->getEmail(),
                subject: $subject,
                template: 'email/admin_custom.html.twig',
                context: [
                    'subject' => $subject,
                    'body' => $body,
                    'user' => $user,
                ]
            );
            ++$sent;
        }

        return $sent;
    }
}
