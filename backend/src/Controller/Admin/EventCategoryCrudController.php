<?php

namespace App\Controller\Admin;

use App\Entity\EventCategory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventCategoryCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return EventCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Categorie')
            ->setEntityLabelInPlural('Categories');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('slug')->onlyOnIndex();
        yield ChoiceField::new('section', 'Section')
            ->setChoices([
                'Articles' => 'articles',
                'Photos de match' => 'photos_de_match',
                'Evenements' => 'evenements',
                'Medias' => 'medias',
            ]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof EventCategory && !$entityInstance->getSlug()) {
            $entityInstance->setSlug($this->slugger->slug((string) $entityInstance->getName())->lower()->toString());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof EventCategory && !$entityInstance->getSlug()) {
            $entityInstance->setSlug($this->slugger->slug((string) $entityInstance->getName())->lower()->toString());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
