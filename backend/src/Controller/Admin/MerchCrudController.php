<?php

namespace App\Controller\Admin;

use App\Entity\Merch;
use App\Form\MerchStockType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MerchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Merch::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield AssociationField::new('category', 'Categorie');
        yield NumberField::new('price', 'Prix');
        yield ChoiceField::new('audience', 'Audience')
            ->setChoices([
                'Matos generaliste (public)' => 'public',
                'Matos du groupe (membres)' => 'members',
            ]);
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('image', 'Image')
            ->renderAsEmbeddedForm(MediaCrudController::class);
        yield CollectionField::new('media', 'Images produit')
            ->useEntryCrudForm(MediaCrudController::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();

        $context  = $this->getContext();
        $instance = $context?->getEntity()?->getInstance();
        $slug = $instance instanceof Merch ? $instance->getCategory()?->getSlug() : null;

        yield CollectionField::new('stocks', 'Stocks')
            ->setEntryType(MerchStockType::class)
            ->setFormTypeOption('entry_options', [
                'category_slug' => $slug,
            ])
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();
    }
}
