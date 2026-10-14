<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminNavigationBuilder
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function navigation(): array
    {
        return [
            [
                'label' => 'Pilotage',
                'items' => [
                    $this->route('Tableau de bord', 'bi bi-speedometer2', 'app_admin_dashboard_index'),
                    $this->route('Retour au site', 'bi bi-arrow-left', 'home'),
                ],
            ],
            [
                'label' => 'Contenu',
                'items' => [
                    $this->crud('Articles', 'bi bi-newspaper', 'articles'),
                    $this->crud('Pages', 'bi bi-file-earmark-text', 'pages'),
                    $this->crud('Chants', 'bi bi-music-note-beamed', 'chants'),
                    $this->crud('Le groupe', 'bi bi-people', 'groupe'),
                    $this->crud('Linktree', 'bi bi-link-45deg', 'linktree'),
                ],
            ],
            [
                'label' => 'Medias',
                'items' => [
                    $this->crud('Evenements', 'bi bi-calendar-event', 'evenements'),
                    $this->crud('Galeries', 'bi bi-images', 'galeries'),
                    $this->crud('Medias', 'bi bi-collection-play', 'medias'),
                    $this->crud('Categories contenu', 'bi bi-tags', 'categories-contenu'),
                ],
            ],
            [
                'label' => 'Boutique',
                'items' => [
                    $this->crud('Merch', 'bi bi-bag', 'merch'),
                    $this->crud('Dons', 'bi bi-heart', 'dons'),
                    $this->crud('Gadgets', 'bi bi-stars', 'gadgets'),
                    $this->crud('Stocks merch', 'bi bi-box-seam', 'stocks-merch'),
                    $this->crud('Categories merch', 'bi bi-list-ul', 'categories-merch'),
                    $this->route('Vente physique', 'bi bi-cash-coin', 'app_admin_merch_physical_sale_new'),
                    $this->route('Import SumUp', 'bi bi-filetype-csv', 'app_admin_merch_sumup_import'),
                    $this->crud('Commandes merch', 'bi bi-receipt', 'commandes-merch'),
                ],
            ],
            [
                'label' => 'Billetterie',
                'items' => [
                    $this->crud('Billets', 'bi bi-ticket-perforated', 'billets'),
                    $this->crud('Categories buvette', 'bi bi-tags', 'buvette-categories'),
                    $this->crud('Produits buvette', 'bi bi-cup-straw', 'buvette-produits'),
                    $this->crud('Precommandes buvette', 'bi bi-cart-check', 'buvette-commandes'),
                    $this->crud('Envois buvette 24h', 'bi bi-envelope-check', 'buvette-rapports'),
                    $this->crud('Categories billets', 'bi bi-list-ul', 'categories-billetterie'),
                    $this->crud('Commandes billets', 'bi bi-receipt', 'commandes-billets'),
                ],
            ],
            [
                'label' => 'Cartage',
                'items' => [
                    $this->crud('Cartages', 'bi bi-person-vcard', 'cartages'),
                    $this->crud('QR codes', 'bi bi-qr-code', 'qr-cartage'),
                ],
            ],
            [
                'label' => 'Gestion',
                'items' => [
                    $this->crud('Utilisateurs', 'bi bi-people', 'utilisateurs'),
                    $this->route($this->translator->trans('admin.navigation.mailing'), 'bi bi-envelope', 'app_admin_mailing'),
                    $this->crud('Codes acces', 'bi bi-key', 'codes-acces'),
                    $this->crud('Paiements SumUp', 'bi bi-credit-card', 'paiements'),
                    $this->crud('Configuration site', 'bi bi-gear', 'configuration'),
                ],
            ],
        ];
    }

    public function quickActions(): array
    {
        return [
            $this->crud('Articles', 'bi bi-newspaper', 'articles', 'Publier et modifier les actualites.'),
            $this->crud('Medias', 'bi bi-images', 'medias', 'Photos, videos et fichiers partages.'),
            $this->crud('Merch', 'bi bi-bag', 'merch', 'Produits, gadgets et stocks.'),
            $this->crud('Dons', 'bi bi-heart', 'dons', 'Suivi des soutiens et paiements.' ),
            $this->route('Vente physique', 'bi bi-cash-coin', 'app_admin_merch_physical_sale_new', 'Encaisser une vente hors ligne et mettre le stock a jour.'),
            $this->route('Import SumUp', 'bi bi-filetype-csv', 'app_admin_merch_sumup_import', 'Rapprocher un export CSV SumUp avec les stocks locaux.'),
            $this->crud('Billetterie', 'bi bi-ticket-perforated', 'billets', 'Matchs, tarifs et disponibilites.'),
            $this->crud('Cartage', 'bi bi-person-vcard', 'cartages', 'Demandes, paiements et validations.'),
            $this->route($this->translator->trans('admin.navigation.mailing'), 'bi bi-envelope', 'app_admin_mailing', 'Communication aux utilisateurs.'),
        ];
    }

    private function route(string $label, string $icon, string $route, ?string $description = null): array
    {
        return [
            'label' => $label,
            'icon' => $icon,
            'url' => $this->urlGenerator->generate($route),
            'description' => $description,
        ];
    }

    private function crud(string $label, string $icon, string $resource, ?string $description = null): array
    {
        return [
            'label' => $label,
            'icon' => $icon,
            'url' => $this->urlGenerator->generate('custom_admin_crud_index', ['resource' => $resource]),
            'description' => $description,
        ];
    }
}
