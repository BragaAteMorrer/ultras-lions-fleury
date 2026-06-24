<?php

namespace App\Controller\Admin;

use App\Entity\Chant;
use App\Entity\SiteConfig;
use App\Repository\SiteConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ChantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Chant')
            ->setEntityLabelInPlural('Chants');
    }

    public function configureActions(Actions $actions): Actions
    {
        $editChantsText = Action::new('editChantsText', 'Texte page')
            ->createAsGlobalAction()
            ->linkToCrudAction('editChantsText');

        return $actions
            ->add(Crud::PAGE_INDEX, $editChantsText);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('title', 'Titre');

        yield TextField::new('audioFile', 'Audio')
            ->setFormType(VichFileType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms();

        yield TextField::new('audioPath', 'Audio')->onlyOnIndex();

        yield TextareaField::new('lyrics', 'Paroles')
            ->setFormTypeOption('attr', ['rows' => 12])
            ->hideOnIndex();

        yield DateTimeField::new('updatedAt', 'MAJ')->hideOnForm();
    }

    public function editChantsText(
        SiteConfigRepository $siteConfigRepository,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $config = $siteConfigRepository->findOneBy([], ['id' => 'ASC']);

        if (!$config) {
            $config = new SiteConfig();

            $em->persist($config);
            $em->flush();
        }

        $url = $adminUrlGenerator
            ->unsetAll()
            ->setController(ChantsTextCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($config->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}