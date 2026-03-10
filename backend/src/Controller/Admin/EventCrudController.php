<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Événement')
            ->setEntityLabelInPlural('Événements');
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
        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices([
                'Photo de match' => 'photo_match',
                'Événement' => 'evenement',
            ])
            ->renderExpanded(false);
        yield TextField::new('opponent', 'Adversaire')
            ->setRequired(false)
            ->hideOnIndex();
        yield ChoiceField::new('matchLocation', 'Lieu du match')
            ->setChoices([
                'Domicile' => 'domicile',
                'Extérieur' => 'exterieur',
            ])
            ->setRequired(false)
            ->hideOnIndex();
        yield ChoiceField::new('season', 'Saison')
            ->setChoices($seasonChoices)
            ->setRequired(false)
            ->hideOnIndex();
        yield IntegerField::new('journee', 'Journée')
            ->setRequired(false)
            ->hideOnIndex();
        yield DateTimeField::new('date', 'Date');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('image', 'Image')
            ->renderAsEmbeddedForm(MediaCrudController::class);
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            yield CollectionField::new('media', 'Images événement')
                ->useEntryCrudForm(MediaCrudController::class)
                ->setFormTypeOption('by_reference', false)
                ->allowAdd()
                ->allowDelete()
                ->onlyOnForms();
        }

    }
}
