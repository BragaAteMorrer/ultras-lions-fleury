<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Evenement')
            ->setEntityLabelInPlural('Evenements');
    }

    public function configureFields(string $pageName): iterable
    {
        $currentYear = (int) date('Y');
        $seasonChoices = [];
        for ($year = $currentYear - 2; $year <= $currentYear + 2; $year++) {
            $label = sprintf('%d/%d', $year, $year + 1);
            $value = sprintf('%d-%d', $year, $year + 1);
            $seasonChoices[$label] = $value;
        }

        yield IdField::new('id')->hideOnForm();

        yield TextField::new('title', 'Titre')
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new('category', 'Categorie');
        yield TextField::new('opponent', 'Adversaire')
            ->setRequired(false)
            ->hideOnIndex();
        yield ChoiceField::new('matchLocation', 'Lieu du match')
            ->setChoices([
                'Domicile' => 'domicile',
                'Exterieur' => 'exterieur',
            ])
            ->setRequired(false)
            ->hideOnIndex();
        yield ChoiceField::new('season', 'Saison')
            ->setChoices($seasonChoices)
            ->setRequired(false)
            ->hideOnIndex();
        yield IntegerField::new('journee', 'Journee')
            ->setRequired(false)
            ->hideOnIndex();
        yield DateTimeField::new('date', 'Date');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('image', 'Image')
            ->renderAsEmbeddedForm(MediaCrudController::class);
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            yield CollectionField::new('media', 'Images evenement')
                ->useEntryCrudForm(MediaCrudController::class)
                ->setFormTypeOption('by_reference', false)
                ->allowAdd()
                ->allowDelete()
                ->onlyOnForms();
        }
    }
}
