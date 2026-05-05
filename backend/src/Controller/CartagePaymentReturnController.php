<?php

namespace App\Controller;

use App\Entity\CartageRegistration;
use App\Repository\CartageRegistrationRepository;
use App\Service\CartageProfileCreator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartagePaymentReturnController extends AbstractController
{
    #[Route('/merci', name: 'cartage_payment_return', methods: ['GET'])]
    public function __invoke(
        Request $request,
        CartageRegistrationRepository $registrationRepository,
        CartageProfileCreator $profileCreator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        Security $security,
    ): Response
    {
        $accountCreated = false;
        $registrationFound = false;
        $reference = trim((string) $request->query->get('ref', ''));

        if (str_starts_with($reference, 'CARTAGE-')) {
            $registration = $registrationRepository->findOneBy(['checkoutReference' => $reference]);

            if ($registration instanceof CartageRegistration) {
                $registrationFound = true;

                try {
                    if ($registration->getStatus() !== CartageRegistration::STATUS_PAID_ONLINE) {
                        $registration->markPaidOnline();
                        $entityManager->flush();
                    }

                    $user = $profileCreator->createProfileIfNeeded($registration);
                    if ($user !== null) {
                        $entityManager->flush();
                        $accountCreated = true;

                        try {
                            $security->login($user);

                            if (method_exists($user, 'getId') && $user->getId() !== null) {
                                return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
                            }
                        } catch (\Throwable $exception) {
                            $logger->warning('Unable to auto-login cartage user after payment return.', [
                                'cartage_registration_id' => $registration->getId(),
                                'checkout_reference' => $reference,
                                'exception' => $exception,
                            ]);
                        }
                    }
                } catch (\Throwable $exception) {
                    $logger->error('Unable to finalize cartage profile after payment return.', [
                        'cartage_registration_id' => $registration->getId(),
                        'checkout_reference' => $reference,
                        'exception' => $exception,
                    ]);

                    return new Response($this->fallbackThankYouHtml());
                }
            }
        }

        return $this->render('cartage/thankyou.html.twig', [
            'accountCreated' => $accountCreated,
            'registrationFound' => $registrationFound,
            'reference' => $reference,
        ]);
    }

    private function fallbackThankYouHtml(): string
    {
        return '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cartage</title></head><body><main style="min-height:100vh;display:grid;place-items:center;font-family:Arial,sans-serif;text-align:center;padding:24px"><div><h1>Cartage</h1><p>Merci, ton paiement est en cours de confirmation.</p></div></main></body></html>';
    }
}
