<?php

namespace App\Controller\Admin;

use App\Entity\InviteCode;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InviteCodeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InviteCode::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code')->onlyOnIndex(),
            BooleanField::new('used'),
            DateTimeField::new('expiresAt'),
        ];
    }
}
