<?php

namespace App\Controller\Admin;

use App\Entity\CartageRegistration;

class CartageRequestCrudController extends AbstractCartageRegistrationCrudController
{
    protected function entityLabelSingular(): string
    {
        return 'Demande de cartage';
    }

    protected function entityLabelPlural(): string
    {
        return 'Demandes de cartage non payees';
    }

    protected function visibleStatuses(): array
    {
        return [
            CartageRegistration::STATUS_PENDING_CASH,
            CartageRegistration::STATUS_PENDING_ONLINE,
        ];
    }

    protected function allowsPaymentReminder(): bool
    {
        return true;
    }
}
