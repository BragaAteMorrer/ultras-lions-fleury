<?php

namespace App\Controller\Admin;

use App\Entity\BilletwebLead;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class BilletwebLeadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BilletwebLead::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Pre-inscription Billetweb')
            ->setEntityLabelInPlural('Pre-inscriptions Billetweb')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::EDIT);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('ticket', 'Adversaire / match'));
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $sort = $searchDto->getSort();

        if (isset($sort['ticketOpponent'])) {
            $direction = strtoupper((string) $sort['ticketOpponent']) === 'ASC' ? 'ASC' : 'DESC';

            $qb
                ->leftJoin('entity.ticket', 'ticketSort')
                ->resetDQLPart('orderBy')
                ->addOrderBy('ticketSort.opponent', $direction)
                ->addOrderBy('entity.createdAt', 'DESC');
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('ticket', 'Match');
        yield TextField::new('ticketOpponent', 'Adversaire')
            ->onlyOnIndex()
            ->setSortable(true);
        yield TextField::new('firstName', 'Prenom');
        yield TextField::new('lastName', 'Nom');
        yield TextField::new('email', 'Email');
        yield AssociationField::new('user', 'Compte')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Date');
    }
}
