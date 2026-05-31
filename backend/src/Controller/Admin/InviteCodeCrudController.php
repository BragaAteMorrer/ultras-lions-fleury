<?php

namespace App\Controller\Admin;

use App\Entity\InviteCode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InviteCodeCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return InviteCode::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $generate5 = Action::new('generate5', 'Generer 5')
            ->createAsGlobalAction()
            ->linkToUrl(fn () => $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('generateBatch')
                ->set('count', 5)
                ->generateUrl());

        $generate20 = Action::new('generate20', 'Generer 20')
            ->createAsGlobalAction()
            ->linkToUrl(fn () => $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('generateBatch')
                ->set('count', 20)
                ->generateUrl());

        $generate100 = Action::new('generate100', 'Generer 100')
            ->createAsGlobalAction()
            ->linkToUrl(fn () => $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('generateBatch')
                ->set('count', 100)
                ->generateUrl());

        return $actions
            ->add(Crud::PAGE_INDEX, $generate5)
            ->add(Crud::PAGE_INDEX, $generate20)
            ->add(Crud::PAGE_INDEX, $generate100);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code')->onlyOnIndex(),
            BooleanField::new('used'),
            DateTimeField::new('expiresAt'),
        ];
    }

    public function generateBatch(AdminContext $context, EntityManagerInterface $em): RedirectResponse
    {
        $count = (int) $context->getRequest()->query->get('count', 5);
        if ($count < 1) {
            $count = 1;
        }
        if ($count > 200) {
            $count = 200;
        }

        for ($i = 0; $i < $count; $i++) {
            $invite = new InviteCode();
            $em->persist($invite);
        }
        $em->flush();

        $this->addFlash('success', sprintf('%d codes crees.', $count));

        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
