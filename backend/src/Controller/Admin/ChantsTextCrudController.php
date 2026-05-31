<?php

namespace App\Controller\Admin;

use App\Entity\SiteConfig;
use App\Repository\SiteConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ChantsTextCrudController extends AbstractCrudController
{
    private EntityManagerInterface $em;
    private SiteConfigRepository $siteConfigRepository;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(
        EntityManagerInterface $em,
        SiteConfigRepository $siteConfigRepository,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->em = $em;
        $this->siteConfigRepository = $siteConfigRepository;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return SiteConfig::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Texte Chants')
            ->setEntityLabelInPlural('Texte Chants')
            ->setPageTitle(Crud::PAGE_EDIT, 'Texte page Chants');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextareaField::new('chantsSubtitle', 'Sous-titre Chants')
            ->setFormTypeOption('attr', ['rows' => 4]);
    }

    public function index(AdminContext $context): RedirectResponse
    {
        $config = $this->siteConfigRepository->findOneBy([], ['id' => 'ASC']);
        if (!$config) {
            $config = $this->siteConfigRepository->createDefaultConfig($this->em);
        }

        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($config->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
