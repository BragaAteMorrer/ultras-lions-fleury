<?php

namespace App\Controller\Admin;

use App\Entity\CartageRegistration;
use App\Service\CartageProfileCreator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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

class CartageRegistrationCrudController extends AbstractCrudController
{
    public function __construct(private CartageProfileCreator $profileCreator)
    {
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
            ->setDefaultSort(['createdAt' => 'DESC']);
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
            ->setChoices([
                'En attente liquide' => CartageRegistration::STATUS_PENDING_CASH,
                'En attente paiement en ligne' => CartageRegistration::STATUS_PENDING_ONLINE,
                'Paye en ligne' => CartageRegistration::STATUS_PAID_ONLINE,
                'Valide liquide' => CartageRegistration::STATUS_VALIDATED_CASH,
                'Annule' => CartageRegistration::STATUS_CANCELLED,
            ]);
        yield TextField::new('checkoutReference', 'Reference paiement')->hideOnForm();
        yield TextField::new('sumupCheckoutId', 'Checkout SumUp')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Cree le')->hideOnForm();
        yield DateTimeField::new('paidAt', 'Paye le')->hideOnForm();
        yield DateTimeField::new('validatedAt', 'Valide le')->hideOnForm();
        yield DateTimeField::new('internalRulesAcceptedAt', 'Reglement accepte le')->hideOnForm();
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
}
