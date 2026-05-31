<?php

namespace App\Controller\Admin;

use App\Entity\PaymentCheckout;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PaymentCheckoutCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentCheckout::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Paiement SumUp')
            ->setEntityLabelInPlural('Paiements SumUp')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('type', 'Type');
        yield TextField::new('status', 'Statut');
        yield TextField::new('sumupCheckoutId', 'Checkout ID')->hideOnForm();
        yield TextField::new('checkoutReference', 'Reference')->hideOnForm();
        yield NumberField::new('amount', 'Montant');
        yield TextField::new('currency', 'Devise')->hideOnForm();
        yield AssociationField::new('user', 'Utilisateur')->hideOnForm();
        yield TextField::new('email', 'Email')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Cree le');
        yield DateTimeField::new('paidAt', 'Paye le')->hideOnForm();
        yield DateTimeField::new('processedAt', 'Traite le')->hideOnForm();
    }
}
