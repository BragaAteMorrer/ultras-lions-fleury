<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Billet')
            ->setEntityLabelInPlural('Billetterie');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('title', 'Titre');
        yield AssociationField::new('category', 'Categorie');
        yield TextField::new('opponent', 'Adversaire');
        yield DateTimeField::new('matchDate', 'Date du match');
        yield ChoiceField::new('matchLocation', 'Lieu du match')
            ->setChoices([
                'Domicile' => 'domicile',
                'Exterieur' => 'exterieur',
            ])
            ->setRequired(false);
        yield TextField::new('venue', 'Stade / Lieu')->hideOnIndex();
        yield NumberField::new('price', 'Prix');
        yield NumberField::new('stock', 'Stock');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('image', 'Image')->renderAsEmbeddedForm(MediaCrudController::class);
    }
}
