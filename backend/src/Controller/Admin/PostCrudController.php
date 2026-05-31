<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Repository\EventCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('title', 'Titre');
        yield TextField::new('slug')->onlyOnIndex();
        yield TextEditorField::new('content', 'Contenu')
            ->setFormTypeOption('attr', ['data-ea-trix-preview' => '1'])
            ->hideOnIndex();
        yield AssociationField::new('category', 'Categorie');
        yield AssociationField::new('image', 'Image')->renderAsEmbeddedForm(MediaCrudController::class);
        yield CollectionField::new('media', 'Images')
            ->useEntryCrudForm(MediaCrudController::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();
        yield DateTimeField::new('createdAt', 'Cree le');
        yield DateTimeField::new('updatedAt', 'Mis a jour')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Post) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        $this->ensureSlugAndDates($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Post) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        if (!$entityInstance->getSlug()) {
            $this->ensureSlugAndDates($entityInstance);
        } else {
            $entityInstance->setUpdatedAt(new \DateTime());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function ensureSlugAndDates(Post $post): void
    {
        $title = $post->getTitle();
        if ($title) {
            $post->setSlug($this->slugger->slug($title)->lower()->toString());
        }

        if (!$post->getCreatedAt()) {
            $post->setCreatedAt(new \DateTime());
        }
        $post->setUpdatedAt(new \DateTime());
    }
}
