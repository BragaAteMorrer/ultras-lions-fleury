<?php

namespace App\Controller\Admin;

use App\Entity\BilletwebLead;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BilletwebLeadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BilletwebLead::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Pre-inscription Billetweb')
            ->setEntityLabelInPlural('Pre-inscriptions Billetweb')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('ticket', 'Match');
        yield TextField::new('firstName', 'Prenom');
        yield TextField::new('lastName', 'Nom');
        yield TextField::new('email', 'Email');
        yield AssociationField::new('user', 'Compte')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Date');
    }
}
