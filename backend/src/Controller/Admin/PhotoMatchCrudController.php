<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;

class PhotoMatchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Photo de match')
            ->setEntityLabelInPlural('Photos de match')
            ->setDefaultSort(['date' => 'DESC']);
    }

    public function createEntity(string $entityFqcn)
    {
        $event = new Event();
        $event->setCategory('photo_match');
        return $event;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Event) {
            $entityInstance->setCategory('photo_match');
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Event) {
            $entityInstance->setCategory('photo_match');
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.category = :category')
            ->setParameter('category', 'photo_match');
        return $qb;
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
        yield TextField::new('title', 'Titre')->onlyOnIndex();
        yield TextField::new('opponent', 'Adversaire')->setRequired(false);
        yield ChoiceField::new('matchLocation', 'Lieu du match')
            ->setChoices([
                'Domicile' => 'domicile',
                'Extérieur' => 'exterieur',
            ])
            ->setRequired(false);
        yield ChoiceField::new('season', 'Saison')
            ->setChoices($seasonChoices)
            ->setRequired(false);
        yield IntegerField::new('journee', 'Journée')
            ->setRequired(false);
        yield DateTimeField::new('date', 'Date');
        yield TextEditorField::new('description', 'Compte rendu')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield AssociationField::new('image', 'Image principale')
            ->renderAsEmbeddedForm(MediaCrudController::class)
            ->hideOnIndex();
        yield CollectionField::new('media', 'Photos du match')
            ->useEntryCrudForm(MediaCrudController::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();
    }
}
