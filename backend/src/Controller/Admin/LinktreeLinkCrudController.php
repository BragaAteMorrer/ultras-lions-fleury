<php

namespace App\Controller\Admin;

use App\Entity\LinktreeLink;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LinktreeLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LinktreeLink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Lien Linktree')
            ->setEntityLabelInPlural('Liens Linktree')
            ->setDefaultSort(['position' => 'ASC', 'id' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('title', 'Titre');
        yield TextField::new('description', 'Sous-titre')->setRequired(false)->hideOnIndex();
        yield TextField::new('url', 'URL')
            ->setHelp('Exemples : /billetterie/ ou https://www.instagram.com/ultraslionsfleury91/');
        yield TextField::new('icon', 'Icone Bootstrap')
            ->setRequired(false)
            ->setHelp('Exemples : ticket-perforated, bag, camera-reels, instagram. Laisser vide pour un lien simple.');
        yield BooleanField::new('enabled', 'Actif');
        yield BooleanField::new('featured', 'Lien principal');
        yield BooleanField::new('openInNewTab', 'Nouvel onglet');
        yield DateTimeField::new('createdAt', 'Cree le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Modifie le')->hideOnForm();
    }
}
