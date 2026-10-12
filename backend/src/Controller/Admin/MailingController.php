<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AdminNavigationBuilder;
use App\Service\MailService;
use App\Service\PageContentSanitizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class MailingController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/admin/mailing', name: 'app_admin_mailing', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        MailService $mailService,
        AdminNavigationBuilder $adminNavigationBuilder,
        PageContentSanitizer $pageContentSanitizer,
    ): Response {
        $users = $userRepository->findBy([], ['email' => 'ASC']);
        $selectedIds = array_map('intval', (array) $request->request->all('users'));
        $subject = trim((string) $request->request->get('subject', ''));
        $headerTitle = trim((string) $request->request->get('headerTitle', ''));
        $headerSubtitle = trim((string) $request->request->get('headerSubtitle', ''));
        $body = $pageContentSanitizer->sanitize((string) $request->request->get('body', ''));

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('admin_mailing', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', $this->translator->trans('admin.mailing.flash.invalid_form'));

                return $this->redirectToRoute('app_admin_mailing');
            }

            $errors = [];
            if ($selectedIds === []) {
                $errors[] = 'admin.mailing.flash.select_one_user';
            }
            if ($subject === '') {
                $errors[] = 'admin.mailing.flash.enter_subject';
            }
            if ($headerTitle === '') {
                $errors[] = 'admin.mailing.flash.enter_header_title';
            }
            if ($headerSubtitle === '') {
                $errors[] = 'admin.mailing.flash.enter_header_subtitle';
            }
            if ($body === '') {
                $errors[] = 'admin.mailing.flash.enter_body';
            }

            if ($errors === []) {
                $sent = $this->sendToSelectedUsers($selectedIds, $subject, $headerTitle, $headerSubtitle, $body, $userRepository, $mailService);
                $this->addFlash($sent > 0 ? 'success' : 'warning', $this->translator->trans('admin.mailing.flash.sent_count', ['%count%' => $sent]));

                return $this->redirectToRoute('app_admin_mailing');
            }

            foreach ($errors as $error) {
                $this->addFlash('danger', $this->translator->trans($error));
            }
        }

        return $this->render('admin/mailing.html.twig', [
            'users' => $users,
            'selectedIds' => $selectedIds,
            'subject' => $subject,
            'headerTitle' => $headerTitle,
            'headerSubtitle' => $headerSubtitle,
            'body' => $body,
            'adminNavigation' => $adminNavigationBuilder->navigation(),
        ]);
    }

    #[Route('/admin/mailing/preview', name: 'app_admin_mailing_preview', methods: ['POST'])]
    public function preview(Request $request, PageContentSanitizer $pageContentSanitizer): Response
    {
        if (!$this->isCsrfTokenValid('admin_mailing_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException($this->translator->trans('admin.mailing.flash.preview_csrf_invalid'));
        }

        $subject = trim((string) $request->request->get('subject', '')) ?: $this->translator->trans('admin.mailing.html.preview_default_subject');
        $headerTitle = trim((string) $request->request->get('headerTitle', '')) ?: $subject;
        $headerSubtitle = trim((string) $request->request->get('headerSubtitle', '')) ?: $this->translator->trans('admin.mailing.html.preview_default_header_subtitle');
        $body = $pageContentSanitizer->sanitize((string) $request->request->get('body', ''));

        return $this->render('email/admin_custom.html.twig', [
            'subject' => $subject,
            'headerTitle' => $headerTitle,
            'headerSubtitle' => $headerSubtitle,
            'body' => $body,
            'recipientName' => $this->translator->trans('admin.mailing.html.preview_recipient_name'),
            'mailingPreview' => true,
        ]);
    }

    /**
     * @param int[] $selectedIds
     */
    private function sendToSelectedUsers(
        array $selectedIds,
        string $subject,
        string $headerTitle,
        string $headerSubtitle,
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
                    'headerTitle' => $headerTitle,
                    'headerSubtitle' => $headerSubtitle,
                    'body' => $body,
                    'user' => $user,
                    'recipientName' => trim((string) $user->getPrenom().' '.(string) $user->getNom()) ?: $user->getEmail(),
                ]
            );
            ++$sent;
        }

        return $sent;
    }
}
