<?php

namespace App\Controller\Admin;

use App\Entity\CartageRegistration;

class CartageRegistrationCrudController extends AbstractCartageRegistrationCrudController
{
    protected function entityLabelSingular(): string
    {
        return 'Cartage';
    }

    protected function entityLabelPlural(): string
    {
        return 'Cartages';
    }

    protected function visibleStatuses(): array
    {
        return [
            CartageRegistration::STATUS_PAID_ONLINE,
            CartageRegistration::STATUS_VALIDATED_CASH,
        ];
    }
}
