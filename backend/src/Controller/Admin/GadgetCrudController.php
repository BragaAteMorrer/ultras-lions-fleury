<?php

namespace App\Controller\Admin;

use App\Entity\Gadget;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GadgetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gadget::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Gadget')
            ->setEntityLabelInPlural('Gadgets du groupe')
            ->setDefaultSort(['position' => 'ASC', 'createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Nom');
        yield TextField::new('productionLabel', 'Série / année')
            ->setRequired(false);
        yield IntegerField::new('position', 'Ordre');
        yield BooleanField::new('visible', 'Visible');
        yield TextareaField::new('description', 'Description')
            ->hideOnIndex()
            ->setRequired(false);
        yield AssociationField::new('image', 'Photo')
            ->renderAsEmbeddedForm(MediaCrudController::class)
            ->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Ajouté le')
            ->hideOnForm();
    }
}
