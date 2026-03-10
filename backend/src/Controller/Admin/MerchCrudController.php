<?php

namespace App\Controller\Admin;

use App\Entity\Merch;
use App\Form\MerchStockType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
        yield AssociationField::new('category', 'Catégorie');
        yield NumberField::new('price', 'Prix');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('image', 'Image')
            ->renderAsEmbeddedForm(MediaCrudController::class);
        yield CollectionField::new('media', 'Images produit')
            ->useEntryCrudForm(MediaCrudController::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();

        $noSizeSlugs = [
            'stickers',
            'couvre-chef',
            'cartage',
            'echarpe',
            'gadget',
            'patch',
        ];

        $context  = $this->getContext();
        $instance = $context?->getEntity()?->getInstance();

        // CREATE
        if ($pageName === Crud::PAGE_NEW) {
            yield TextField::new('stocks_info')
                ->setVirtual(true)
                ->setHelp("💡 Enregistre le produit avant d’ajouter les stocks.")
                ->onlyOnForms();

            return;
        }

        // EDIT
        if ($pageName === Crud::PAGE_EDIT && $instance instanceof Merch) {
            $slug = $instance->getCategory()?->getSlug();

            if ($slug && !in_array($slug, $noSizeSlugs, true)) {
                yield CollectionField::new('stocks', 'Stocks')
                    ->setEntryType(MerchStockType::class)
                    ->setFormTypeOption('entry_options', [
                        'category_slug' => $slug,
                    ])
                    ->setFormTypeOption('by_reference', false) // ⭐ OBLIGATOIRE
                    ->allowAdd()
                    ->allowDelete()
                    ->onlyOnForms();
            }
        }
    }
}
