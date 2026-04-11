<?php

namespace App\Controller\Admin;

use App\Entity\GroupPage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupPageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupPage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page Groupe')
            ->setEntityLabelInPlural('Pages Groupe');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('name', 'Nom');
        yield TextEditorField::new('description', 'Description')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield TextEditorField::new('histoireText', 'Texte - Notre histoire')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield TextEditorField::new('mentaliteText', 'Texte - Notre mentalite')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield TextEditorField::new('fonctionnementText', 'Texte - Notre fonctionnement')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield TextEditorField::new('rejoindreText', 'Texte - Pourquoi nous rejoindre ?')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield TextEditorField::new('seCarterText', 'Texte - Se Carter')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();

        yield AssociationField::new('logo', 'Logo')->renderAsEmbeddedForm(MediaCrudController::class);
        yield AssociationField::new('banner', 'Bannière')->renderAsEmbeddedForm(MediaCrudController::class);

        yield CollectionField::new('media', 'Médias')
            ->useEntryCrudForm(MediaCrudController::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();
    }
}
