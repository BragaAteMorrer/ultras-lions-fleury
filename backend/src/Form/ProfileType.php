<?php

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    EmailType,
    DateType,
    TelType,
    ChoiceType,
    FileType
};
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* =======================================================
         *  INFORMATIONS PRINCIPALES
         * ======================================================= */
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])

            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\LessThan(
                        value: '-12 years',
                        message: 'Tu dois avoir au moins 12 ans.'
                    ),
                    new Assert\GreaterThan('-120 years'),
                ]
            ]);

        /* =======================================================
         *  COORDONNÉES – international + libphonenumber
         * ======================================================= */
        $builder
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '+33612345678 ou +32460123456 (international)',
                ],
                'constraints' => [
                    new Assert\Length(min: 5, max: 20),
                    new Assert\Callback(function ($value, $context) {
                        if (!$value) return;

                        $lib = PhoneNumberUtil::getInstance();

                        try {
                            $parsed = $lib->parse($value, null);

                            if (!$lib->isValidNumber($parsed)) {
                                $context->buildViolation('Numéro de téléphone invalide.')->addViolation();
                            }
                        } catch (\Exception) {
                            $context->buildViolation('Format téléphonique incorrect.')->addViolation();
                        }
                    })
                ],
            ]);

        /* =======================================================
         *  ADRESSE – internationale + validation
         * ======================================================= */
        $builder
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\Regex([
                        'pattern' => '/^[0-9A-Za-zÀ-ÿ\'\-,. ]{3,255}$/u',
                        'message' => 'Adresse invalide (caractères non autorisés).',
                    ]),
                ],
                'attr' => ['placeholder' => 'Ex : 45 rue de la République']
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 100),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-zÀ-ÿ\'\- ]{2,100}$/u',
                        'message' => 'Ville invalide.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Ex : Montréal, Bruxelles, Paris']
            ])

            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9\- ]{3,12}$/',
                        'message' => 'Code postal invalide.',
                    ])
                ],
                'attr' => ['placeholder' => 'Ex : 75001, H2Y 1C6…']
            ]);

        /* =======================================================
         *  PHOTOS
         * ======================================================= */
        $builder
            ->add('photoProfil', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
            ]);

        /* =======================================================
         *  TAILLES DES VÊTEMENTS
         * ======================================================= */
        $sizes = [
            'XS' => 'XS',
            'S'  => 'S',
            'M'  => 'M',
            'L'  => 'L',
            'XL' => 'XL',
            'XXL'=> 'XXL',
        ];

        $builder
            ->add('tailleTshirt', ChoiceType::class, [
                'label' => 'Taille T-shirt',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ])
            ->add('taillePolo', ChoiceType::class, [
                'label' => 'Taille Polo',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ])
            ->add('taillePull', ChoiceType::class, [
                'label' => 'Taille Pull',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ])
            ->add('tailleSweat', ChoiceType::class, [
                'label' => 'Taille Sweat',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ])
            ->add('tailleVeste', ChoiceType::class, [
                'label' => 'Taille Veste',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ])
            ->add('tailleShort', ChoiceType::class, [
                'label' => 'Taille Short',
                'choices' => $sizes,
                'placeholder' => 'Choisir...',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
