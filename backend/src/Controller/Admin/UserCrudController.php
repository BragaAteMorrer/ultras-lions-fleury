<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Filter\Admin\UserCartagePaidFilter;
use App\Repository\CartageQrTokenRepository;
use App\Repository\CartageRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly CartageRegistrationRepository $cartageRegistrationRepository,
        private readonly CartageQrTokenRepository $cartageQrTokenRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $sizes = [
            'XS' => 'XS',
            'S'  => 'S',
            'M'  => 'M',
            'L'  => 'L',
            'XL' => 'XL',
            'XXL'=> 'XXL',
        ];

        return [
            ImageField::new('photoProfil', 'Photo')
                ->setBasePath('/uploads/profils')
                ->onlyOnIndex(),

            ImageField::new('photoProfil', 'Photo')
                ->setBasePath('/uploads/profils')
                ->onlyOnDetail(),

            EmailField::new('email'),

            TextField::new('plainPassword', 'Nouveau mot de passe')
                ->onlyOnForms()
                ->setRequired(false),

            ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ]),

            TextField::new('cartageStatus', 'Cartage')
                ->hideOnForm()
                ->formatValue(fn ($value, ?User $user) => $user instanceof User ? $this->getCartageStatusLabel($user) : ''),

            TextField::new('prenom', 'Prenom'),
            TextField::new('nom', 'Nom'),
            DateField::new('dateNaissance', 'Date de naissance')->hideOnIndex(),
            TelephoneField::new('telephone', 'Telephone'),

            TextField::new('adresse', 'Adresse')->hideOnIndex(),
            TextField::new('ville', 'Ville'),
            TextField::new('codePostal', 'Code postal')->hideOnIndex(),

            ImageField::new('photoProfil')
                ->setBasePath('/uploads/profils')
                ->setUploadDir('public/uploads/profils')
                ->setRequired(false)
                ->onlyOnForms(),
                
            ChoiceField::new('tailleTshirt', 'T-shirt')->setChoices($sizes),
            ChoiceField::new('taillePolo', 'Polo')->setChoices($sizes)->hideOnIndex(),
            ChoiceField::new('taillePull', 'Pull')->setChoices($sizes)->hideOnIndex(),
            ChoiceField::new('tailleSweat', 'Sweat')->setChoices($sizes),
            ChoiceField::new('tailleVeste', 'Veste')->setChoices($sizes),
            ChoiceField::new('tailleShort', 'Short')->setChoices($sizes),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des utilisateurs')
            ->setPaginatorPageSize(30)
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewProfile = Action::new('viewProfile', 'Voir profil')
            ->setIcon('fa fa-eye')
            ->linkToUrl(fn (User $user) => $this->urlGenerator->generate('profile_show', ['id' => $user->getId()]))
            ->setHtmlAttributes(['target' => '_blank', 'rel' => 'noopener'])
            ->addCssClass('btn btn-secondary');

        $csv = Action::new('exportCsv', 'Exporter CSV')
            ->linkToCrudAction('exportCsv')
            ->setIcon('fa fa-file-csv')
            ->addCssClass('btn btn-secondary')
            ->createAsBatchAction();

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) => $action->setLabel('Visualiser')->setIcon('fa fa-id-card'))
            ->add(Crud::PAGE_INDEX, $viewProfile)
            ->add(Crud::PAGE_DETAIL, $viewProfile)
            ->add(Crud::PAGE_INDEX, $csv);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('email', 'Email'))
            ->add(TextFilter::new('prenom', 'Prenom'))
            ->add(TextFilter::new('nom', 'Nom'))
            ->add(TextFilter::new('ville', 'Ville'))
            ->add(UserCartagePaidFilter::new('cartageStatus', 'Cartage', $this->getCurrentSeason()));
    }

    public function exportCsv(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        $ids = $batchActionDto->getEntityIds();
        $users = $ids ? $em->getRepository(User::class)->findBy(['id' => $ids]) : [];

        $lines = [];
        $lines[] = ['Email', 'Prenom', 'Nom', 'Roles', 'Telephone', 'Ville', 'Code postal', 'T-shirt', 'Sweat', 'Veste', 'Short', 'Cartage saison '.$this->getCurrentSeason()];

        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $lines[] = [
                $user->getEmail() ?? '',
                $user->getPrenom() ?? '',
                $user->getNom() ?? '',
                implode(',', $user->getRoles()),
                $user->getTelephone() ?? '',
                $user->getVille() ?? '',
                $user->getCodePostal() ?? '',
                $user->getTailleTshirt() ?? '',
                $user->getTailleSweat() ?? '',
                $user->getTailleVeste() ?? '',
                $user->getTailleShort() ?? '',
                $this->getCartageStatusLabel($user),
            ];
        }

        return $this->csvResponse($lines, 'utilisateurs.csv');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->handlePassword($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->handlePassword($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handlePassword(User $user): void
    {
        $plain = $user->getPlainPassword();
        if ($plain) {
            $hashed = $this->hasher->hashPassword($user, $plain);
            $user->setPassword($hashed);
            $user->setPlainPassword(null);
        }
    }

    private function getCartageStatusLabel(User $user): string
    {
        $email = $user->getEmail();
        if (!$email) {
            return 'Aucun email';
        }

        $registration = $this->cartageRegistrationRepository->findLatestForSeasonByEmail($email, $this->getCurrentSeason());
        if (!$registration) {
            return 'Non effectue';
        }

        return $registration->getStatusLabel();
    }

    private function getCurrentSeason(?\DateTimeImmutable $date = null): string
    {
        $qrToken = $this->cartageQrTokenRepository->findLatestUsable();
        $season = $qrToken ? $this->extractSeason($qrToken->getLabel()) : null;
        if ($season !== null) {
            return $season;
        }

        $date ??= new \DateTimeImmutable();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $startYear = $month >= 7 ? $year : $year - 1;

        return sprintf('%d-%d', $startYear, $startYear + 1);
    }

    private function extractSeason(string $value): ?string
    {
        if (preg_match('/(20\d{2})\s*[\/-]\s*(20\d{2})/', $value, $matches) !== 1) {
            return null;
        }

        return $matches[1].'-'.$matches[2];
    }

    /**
     * @param array<int, array<int, string>> $lines
     */
    private function csvResponse(array $lines, string $filename): Response
    {
        $out = '';
        foreach ($lines as $line) {
            $escaped = array_map(static function ($value) {
                $value = (string) $value;
                $value = str_replace('"', '""', $value);

                return '"'.$value.'"';
            }, $line);
            $out .= implode(';', $escaped)."\r\n";
        }

        return new Response($out, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }
}
