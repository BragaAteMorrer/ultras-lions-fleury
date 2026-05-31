<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
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

            TextField::new('prenom')->onlyOnForms(),
            TextField::new('nom')->onlyOnForms(),
            DateField::new('dateNaissance')->onlyOnForms(),
            TextField::new('telephone')->onlyOnForms(),

            TextField::new('adresse')->onlyOnForms(),
            TextField::new('ville')->onlyOnForms(),
            TextField::new('codePostal')->onlyOnForms(),

            ImageField::new('photoProfil')
                ->setBasePath('/uploads/profils')
                ->setUploadDir('public/uploads/profils')
                ->setRequired(false)
                ->onlyOnForms(),

            ImageField::new('photoProfil')
                ->setBasePath('/uploads/profils')
                ->onlyOnIndex(),
                
            ChoiceField::new('tailleTshirt')->setChoices($sizes)->onlyOnForms(),
            ChoiceField::new('taillePolo')->setChoices($sizes)->onlyOnForms(),
            ChoiceField::new('taillePull')->setChoices($sizes)->onlyOnForms(),
            ChoiceField::new('tailleSweat')->setChoices($sizes)->onlyOnForms(),
            ChoiceField::new('tailleVeste')->setChoices($sizes)->onlyOnForms(),
            ChoiceField::new('tailleShort')->setChoices($sizes)->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des utilisateurs');
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
}
