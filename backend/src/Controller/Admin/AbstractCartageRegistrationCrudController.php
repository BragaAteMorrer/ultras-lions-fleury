<?php

namespace App\Controller\Admin;

use App\Entity\CartageRegistration;
use App\Service\CartageProfileCreator;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractCartageRegistrationCrudController extends AbstractCrudController
{
    public function __construct(
        protected CartageProfileCreator $profileCreator,
        protected MailService $mailService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return CartageRegistration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular($this->entityLabelSingular())
            ->setEntityLabelInPlural($this->entityLabelPlural())
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->allowsPaymentReminder()) {
            return $actions;
        }

        $sendReminder = Action::new('sendPaymentReminder', 'Envoyer mail')
            ->linkToCrudAction('sendPaymentReminder')
            ->setIcon('fa fa-envelope')
            ->addCssClass('btn btn-secondary');

        $sendReminderBatch = Action::new('sendPaymentReminderBatch', 'Envoyer mail')
            ->linkToCrudAction('sendPaymentReminderBatch')
            ->setIcon('fa fa-envelope')
            ->addCssClass('btn btn-secondary')
            ->createAsBatchAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $sendReminder)
            ->add(Crud::PAGE_DETAIL, $sendReminder)
            ->add(Crud::PAGE_INDEX, $sendReminderBatch);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices($this->statusChoices()))
            ->add(ChoiceFilter::new('paymentMethod', 'Paiement')->setChoices([
                'Liquide' => 'cash',
                'En ligne' => 'online',
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('qrToken', 'QR')->hideOnIndex();
        yield TextField::new('firstName', 'Prenom');
        yield TextField::new('lastName', 'Nom');
        yield EmailField::new('email', 'Email');
        yield TelephoneField::new('phone', 'Telephone');
        yield DateField::new('birthDate', 'Date de naissance')->hideOnIndex();
        yield TextField::new('address', 'Adresse')->hideOnIndex();
        yield TextField::new('postalCode', 'Code postal')->hideOnIndex();
        yield TextField::new('city', 'Ville')->hideOnIndex();
        yield TextField::new('shirtSize', 'T-shirt')->hideOnIndex();
        yield TextField::new('sweatSize', 'Sweat')->hideOnIndex();
        yield TextField::new('jacketSize', 'Veste')->hideOnIndex();
        yield TextField::new('shortSize', 'Short')->hideOnIndex();
        yield MoneyField::new('amount', 'Montant')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
        yield ChoiceField::new('paymentMethod', 'Paiement')
            ->setChoices([
                'Liquide' => 'cash',
                'En ligne' => 'online',
            ]);
        yield ChoiceField::new('status', 'Statut')
            ->setChoices($this->statusChoices());
        yield TextField::new('checkoutReference', 'Reference paiement')->hideOnForm();
        yield TextField::new('sumupCheckoutId', 'Checkout SumUp')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Cree le')->hideOnForm();
        yield DateTimeField::new('paidAt', 'Paye le')->hideOnForm();
        yield DateTimeField::new('validatedAt', 'Valide le')->hideOnForm();
        yield DateTimeField::new('internalRulesAcceptedAt', 'Reglement accepte le')->hideOnForm();
        yield TextField::new('internalRulesTitle', 'Reglement accepte')->hideOnIndex()->hideOnForm();
        yield TextareaField::new('internalRulesContent', 'Contenu accepte')->hideOnIndex()->hideOnForm();
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $statuses = $this->visibleStatuses();

        if ($statuses !== []) {
            $qb
                ->andWhere('entity.status IN (:cartageStatuses)')
                ->setParameter('cartageStatuses', $statuses);
        }

        return $qb;
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

    public function sendPaymentReminder(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $registration = $context->getEntity()->getInstance();
        if (!$registration instanceof CartageRegistration) {
            $this->addFlash('danger', 'Demande de cartage introuvable.');

            return $this->redirectToIndex($adminUrlGenerator);
        }

        $this->sendReminderForRegistration($registration);
        $this->addFlash('success', sprintf('Mail envoye a %s.', $registration->getEmail()));

        return $this->redirectToIndex($adminUrlGenerator);
    }

    public function sendPaymentReminderBatch(
        BatchActionDto $batchActionDto,
        EntityManagerInterface $entityManager,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $sent = 0;
        $registrations = $entityManager->getRepository(CartageRegistration::class)->findBy([
            'id' => $batchActionDto->getEntityIds(),
        ]);

        foreach ($registrations as $registration) {
            if (!$registration instanceof CartageRegistration || !$this->isUnpaidRegistration($registration)) {
                continue;
            }

            $this->sendReminderForRegistration($registration);
            ++$sent;
        }

        $this->addFlash($sent > 0 ? 'success' : 'warning', sprintf('%d mail(s) envoye(s).', $sent));

        return $this->redirectToIndex($adminUrlGenerator);
    }

    protected function entityLabelSingular(): string
    {
        return 'Cartage';
    }

    protected function entityLabelPlural(): string
    {
        return 'Cartages';
    }

    /**
     * @return string[]
     */
    protected function visibleStatuses(): array
    {
        return [];
    }

    protected function allowsPaymentReminder(): bool
    {
        return false;
    }

    /**
     * @return array<string, string>
     */
    protected function statusChoices(): array
    {
        return [
            'En attente liquide' => CartageRegistration::STATUS_PENDING_CASH,
            'En attente paiement en ligne' => CartageRegistration::STATUS_PENDING_ONLINE,
            'Paye en ligne' => CartageRegistration::STATUS_PAID_ONLINE,
            'Valide liquide' => CartageRegistration::STATUS_VALIDATED_CASH,
            'Annule' => CartageRegistration::STATUS_CANCELLED,
        ];
    }

    protected function isUnpaidRegistration(CartageRegistration $registration): bool
    {
        return in_array($registration->getStatus(), [
            CartageRegistration::STATUS_PENDING_CASH,
            CartageRegistration::STATUS_PENDING_ONLINE,
        ], true);
    }

    private function sendReminderForRegistration(CartageRegistration $registration): void
    {
        if (!$this->isUnpaidRegistration($registration)) {
            $this->addFlash('warning', 'Cette demande est deja payee ou validee.');

            return;
        }

        $this->mailService->send(
            to: $registration->getEmail(),
            subject: 'Cartage en attente de paiement',
            template: 'email/cartage_payment_reminder.html.twig',
            context: [
                'registration' => $registration,
                'statusUrl' => $this->generateUrl('cartage_status', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        );
    }

    private function redirectToIndex(AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $url = $adminUrlGenerator
            ->setController(static::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
