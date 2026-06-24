<?php

namespace App\Controller\Admin;

class CartageReminderCrudController extends CartageRequestCrudController
{
    protected function entityLabelSingular(): string
    {
        return 'Relance cartage';
    }

    protected function entityLabelPlural(): string
    {
        return 'Relances paiement cartage';
    }
}
