<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Repository\EventCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class PhotoMatchCrudController extends AbstractCrudController
{
    private EventCategoryRepository $eventCategoryRepository;

    public function __construct(EventCategoryRepository $eventCategoryRepository)
    {
        $this->eventCategoryRepository = $eventCategoryRepository;
    }

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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('opponent', 'Adversaire'));
    }

    public function createEntity(string $entityFqcn)
    {
        $event = new Event();
        $event->setCategory($this->eventCategoryRepository->findOneBySection('photos_de_match'));
        return $event;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Event && $entityInstance->getCategory() === null) {
            $entityInstance->setCategory($this->eventCategoryRepository->findOneBySection('photos_de_match'));
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Event && $entityInstance->getCategory() === null) {
            $entityInstance->setCategory($this->eventCategoryRepository->findOneBySection('photos_de_match'));
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
        $qb->join('entity.category', 'cat')
            ->andWhere('cat.section = :section')
            ->setParameter('section', 'photos_de_match');
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
        yield TextField::new('opponent', 'Adversaire')
            ->setRequired(false)
            ->setSortable(true);
        yield ChoiceField::new('matchLocation', 'Lieu du match')
            ->setChoices([
                'Domicile' => 'domicile',
                'Exterieur' => 'exterieur',
            ])
            ->setRequired(false);
        yield ChoiceField::new('season', 'Saison')
            ->setChoices($seasonChoices)
            ->setRequired(false);
        yield IntegerField::new('journee', 'Journee')
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
