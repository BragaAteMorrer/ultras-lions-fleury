<?php

namespace App\Controller\Admin;

use App\Entity\MerchStock;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class MerchStockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MerchStock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Stock')
            ->setEntityLabelInPlural('Stocks')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('merch', 'Produit')
            ->setRequired(true);

        yield ChoiceField::new('size', 'Taille')
            ->setChoices([
                'XS' => 'XS',
                'S' => 'S',
                'M' => 'M',
                'L' => 'L',
                'XL' => 'XL',
                'XXL' => 'XXL',
                '3XL' => '3XL',
            ])
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT);

        yield IntegerField::new('quantity', 'Quantité');
    }
}
