<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Média')
            ->setEntityLabelInPlural('Médias');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield ImageField::new('path', 'Aperçu')
            ->setBasePath('/uploads/media')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Fichier')
            ->setFormType(VichImageType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms();

        yield TextField::new('alt', 'Alt')->hideOnIndex();
        yield DateTimeField::new('updatedAt', 'MAJ')->hideOnForm();
        yield AssociationField::new('gallery', 'Galerie')->hideOnForm();
        yield AssociationField::new('groupPage', 'Groupe')->hideOnForm();

    }
}
