<?php

namespace App\Controller\Admin;

use App\Entity\TicketCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TicketCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TicketCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie Billetterie')
            ->setEntityLabelInPlural('Catégories Billetterie');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('slug', 'Slug');
    }
}

