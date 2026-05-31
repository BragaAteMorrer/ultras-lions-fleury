<php

namespace App\Controller\Admin;

use App\Entity\CartageRegistration;
use App\Repository\CartageRegistrationRepository;
use App\Service\CartageProfileCreator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CartageRegistrationCrudController extends AbstractCrudController
{
    public function __construct(
        private CartageProfileCreator $profileCreator,
        private CartageRegistrationRepository $registrationRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return CartageRegistration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cartage')
            ->setEntityLabelInPlural('Cartages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(30)
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $validate = Action::new('validateCartage', 'Valider')
            ->linkToCrudAction('validateCartage')
            ->setIcon('fa fa-check')
            ->addCssClass('btn btn-success')
            ->displayIf(fn (CartageRegistration $registration) => !$registration->isCompleted() && $registration->getStatus() !== CartageRegistration::STATUS_CANCELLED);

        $validateSelected = Action::new('validateSelected', 'Valider')
            ->linkToCrudAction('validateSelected')
            ->setIcon('fa fa-check')
            ->addCssClass('btn btn-success')
            ->createAsBatchAction();

        $csv = Action::new('exportCsv', 'Exporter CSV')
            ->linkToCrudAction('exportCsv')
            ->setIcon('fa fa-file-csv')
            ->addCssClass('btn btn-secondary')
            ->createAsBatchAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $validate)
            ->add(Crud::PAGE_INDEX, $validateSelected)
            ->add(Crud::PAGE_INDEX, $csv);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $seasonChoices = [];
        foreach ($this->registrationRepository->findSeasons() as $season) {
            $seasonChoices[$season] = $season;
        }

        if ($seasonChoices === []) {
            $seasonChoices[$this->getCurrentSeason()] = $this->getCurrentSeason();
        }

        return $filters
            ->add(EntityFilter::new('qrToken', 'QR cartage'))
            ->add(ChoiceFilter::new('season', 'Saison')->setChoices($seasonChoices))
            ->add(ChoiceFilter::new('status', 'Validation')->setChoices($this->getStatusChoices()))
            ->add(ChoiceFilter::new('paymentMethod', 'Paiement')->setChoices([
                'Liquide' => 'cash',
                'En ligne' => 'online',
            ]))
            ->add(DateTimeFilter::new('createdAt', 'Date'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('qrToken', 'QR cartage');
        yield TextField::new('firstName', 'Prenom');
        yield TextField::new('lastName', 'Nom');
        yield EmailField::new('email', 'Email');
        yield TelephoneField::new('phone', 'Telephone')->hideOnIndex();
        yield TextField::new('season', 'Saison');
        yield DateField::new('birthDate', 'Date de naissance')->hideOnIndex();
        yield TextField::new('address', 'Adresse')->hideOnIndex();
        yield TextField::new('postalCode', 'Code postal')->hideOnIndex();
        yield TextField::new('city', 'Ville')->hideOnIndex();
        yield TextField::new('shirtSize', 'T-shirt')->hideOnIndex();
        yield TextField::new('poloSize', 'Polo')->hideOnIndex();
        yield TextField::new('pullSize', 'Pull')->hideOnIndex();
        yield TextField::new('sweatSize', 'Sweat')->hideOnIndex();
        yield TextField::new('jacketSize', 'Veste')->hideOnIndex();
        yield TextField::new('shortSize', 'Short')->hideOnIndex();
        yield MoneyField::new('amount', 'Montant')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
        yield ChoiceField::new('paymentMethod', 'Paiement')
            ->hideOnIndex()
            ->setChoices([
                'Liquide' => 'cash',
                'En ligne' => 'online',
            ]);
        yield ChoiceField::new('status', 'Statut')
            ->setChoices($this->getStatusChoices());
        yield TextField::new('checkoutReference', 'Reference paiement')->hideOnIndex()->hideOnForm();
        yield TextField::new('sumupCheckoutId', 'Checkout SumUp')->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('createdAt', 'Cree le')->hideOnForm();
        yield DateTimeField::new('paidAt', 'Paye le')->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('validatedAt', 'Valide le')->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('internalRulesAcceptedAt', 'Reglement accepte le')->hideOnIndex()->hideOnForm();
        yield TextField::new('internalRulesTitle', 'Reglement accepte')->hideOnIndex()->hideOnForm();
        yield TextareaField::new('internalRulesContent', 'Contenu accepte')->hideOnIndex()->hideOnForm();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $shouldCreateProfile = false;

        if ($entityInstance instanceof CartageRegistration) {
            if ($entityInstance->getStatus() === CartageRegistration::STATUS_VALIDATED_CASH && $entityInstance->getValidatedAt() === null) {
                $entityInstance->setValidatedAt(new \DateTimeImmutable());
            }

            if ($entityInstance->getStatus() === CartageRegistration::STATUS_PAID_ONLINE && $entityInstance->getPaidAt() === null) {
                $entityInstance->setPaidAt(new \DateTimeImmutable());
            }

            if (in_array($entityInstance->getStatus(), [
                CartageRegistration::STATUS_VALIDATED_CASH,
                CartageRegistration::STATUS_PAID_ONLINE,
            ], true)) {
                $shouldCreateProfile = true;
            }
        }

        parent::updateEntity($entityManager, $entityInstance);

        if ($shouldCreateProfile && $entityInstance instanceof CartageRegistration) {
            $this->profileCreator->createProfileIfNeeded($entityInstance);
            $entityManager->flush();
        }
    }

    public function validateCartage(AdminContext $context, EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $registration = $context->getEntity()->getInstance();
        if ($registration instanceof CartageRegistration) {
            $this->validateRegistration($registration);
            $entityManager->flush();
            $this->profileCreator->createProfileIfNeeded($registration);
            $entityManager->flush();
            $this->addFlash('success', 'Cartage valide.');
        }

        return $this->redirect($adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl());
    }

    public function validateSelected(BatchActionDto $batchActionDto, EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $count = 0;
        foreach ($batchActionDto->getEntityIds() as $id) {
            $registration = $entityManager->getRepository(CartageRegistration::class)->find($id);
            if (!$registration instanceof CartageRegistration || $registration->isCompleted() || $registration->getStatus() === CartageRegistration::STATUS_CANCELLED) {
                continue;
            }

            $this->validateRegistration($registration);
            $this->profileCreator->createProfileIfNeeded($registration);
            ++$count;
        }

        $entityManager->flush();
        $this->addFlash('success', sprintf('%d cartage(s) valide(s).', $count));

        return $this->redirect($adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl());
    }

    public function exportCsv(BatchActionDto $batchActionDto, EntityManagerInterface $entityManager): Response
    {
        $ids = $batchActionDto->getEntityIds();
        $registrations = $ids  $entityManager->getRepository(CartageRegistration::class)->findBy(['id' => $ids]) : [];

        $lines = [];
        $lines[] = ['ID', 'QR cartage', 'Saison', 'Prenom', 'Nom', 'Email', 'Telephone', 'Date naissance', 'Ville', 'Code postal', 'T-shirt', 'Polo', 'Pull', 'Sweat', 'Veste', 'Short', 'Montant', 'Paiement', 'Statut', 'Cree le', 'Paye le', 'Valide le'];

        foreach ($registrations as $registration) {
            if (!$registration instanceof CartageRegistration) {
                continue;
            }

            $lines[] = [
                (string) $registration->getId(),
                (string) ($registration->getQrToken()->getLabel()  ''),
                $registration->getSeason(),
                $registration->getFirstName(),
                $registration->getLastName(),
                $registration->getEmail(),
                $registration->getPhone(),
                $registration->getBirthDate()->format('d/m/Y')  '',
                $registration->getCity()  '',
                $registration->getPostalCode()  '',
                $registration->getShirtSize()  '',
                $registration->getPoloSize()  '',
                $registration->getPullSize()  '',
                $registration->getSweatSize()  '',
                $registration->getJacketSize()  '',
                $registration->getShortSize()  '',
                number_format($registration->getAmount(), 2, ',', ' '),
                $registration->getPaymentMethod(),
                $registration->getStatusLabel(),
                $registration->getCreatedAt()->format('d/m/Y H:i'),
                $registration->getPaidAt()->format('d/m/Y H:i')  '',
                $registration->getValidatedAt()->format('d/m/Y H:i')  '',
            ];
        }

        return $this->csvResponse($lines, 'demandes_cartage.csv');
    }

    private function validateRegistration(CartageRegistration $registration): void
    {
        if ($registration->isCompleted()) {
            return;
        }

        if ($registration->getPaymentMethod() === 'online') {
            $registration
                ->setStatus(CartageRegistration::STATUS_PAID_ONLINE)
                ->setPaidAt(new \DateTimeImmutable());

            return;
        }

        $registration
            ->setStatus(CartageRegistration::STATUS_VALIDATED_CASH)
            ->setValidatedAt(new \DateTimeImmutable());
    }

    /**
     * @return array<string, string>
     */
    private function getStatusChoices(): array
    {
        return [
            'En attente liquide' => CartageRegistration::STATUS_PENDING_CASH,
            'En attente paiement en ligne' => CartageRegistration::STATUS_PENDING_ONLINE,
            'Paye en ligne' => CartageRegistration::STATUS_PAID_ONLINE,
            'Valide liquide' => CartageRegistration::STATUS_VALIDATED_CASH,
            'Annule' => CartageRegistration::STATUS_CANCELLED,
        ];
    }

    private function getCurrentSeason(\DateTimeImmutable $date = null): string
    {
        $date = new \DateTimeImmutable();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $startYear = $month >= 7  $year : $year - 1;

        return sprintf('%d-%d', $startYear, $startYear + 1);
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
