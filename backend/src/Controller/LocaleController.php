<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LocaleController extends AbstractController
{
    private const SUPPORTED_LOCALES = ['fr', 'en', 'es', 'de', 'pt', 'ar'];

    #[Route('/langue/{locale}', name: 'locale_switch', requirements: ['locale' => 'fr|en|es|de|pt|ar'])]
    public function switch(string $locale, Request $request): RedirectResponse
    {
        if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'fr';
        }

        $request->getSession()->set('_locale', $locale);

        $referer = (string) $request->headers->get('referer', '');
        if ($referer !== '' && str_starts_with($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('home');
    }
}
