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
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* =======================================================
         *  INFORMATIONS PRINCIPALES
         * ======================================================= */
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.profile.email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])

            ->add('prenom', TextType::class, [
                'label' => 'form.profile.first_name',
                'required' => false,
            ])

            ->add('nom', TextType::class, [
                'label' => 'form.profile.last_name',
                'required' => false,
            ])

            ->add('dateNaissance', DateType::class, [
                'label' => 'form.profile.birth_date',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\LessThan(
                        value: '-12 years',
                        message: 'form.profile.validation.minimum_age'
                    ),
                    new Assert\GreaterThan('-120 years'),
                ]
            ]);

        /* =======================================================
         *  COORDONNÉES – international + libphonenumber
         * ======================================================= */
        $builder
            ->add('telephone', TelType::class, [
                'label' => 'form.profile.phone',
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('form.profile.placeholder.phone'),
                ],
                'constraints' => [
                    new Assert\Length(min: 5, max: 20),
                    new Assert\Callback(function ($value, $context) {
                        if (!$value) return;

                        $lib = PhoneNumberUtil::getInstance();

                        try {
                            $parsed = $lib->parse($value, null);

                            if (!$lib->isValidNumber($parsed)) {
                                $context->buildViolation('form.profile.validation.phone_invalid')->addViolation();
                            }
                        } catch (\Exception) {
                            $context->buildViolation('form.profile.validation.phone_format')->addViolation();
                        }
                    })
                ],
            ]);

        /* =======================================================
         *  ADRESSE – internationale + validation
         * ======================================================= */
        $builder
            ->add('adresse', TextType::class, [
                'label' => 'form.profile.address',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\Regex([
                        'pattern' => '/^[0-9A-Za-zÀ-ÿ\'\-,. ]{3,255}$/u',
                        'message' => 'form.profile.validation.address_invalid',
                    ]),
                ],
                'attr' => ['placeholder' => $this->translator->trans('form.profile.placeholder.address')]
            ])

            ->add('ville', TextType::class, [
                'label' => 'form.profile.city',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 100),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-zÀ-ÿ\'\- ]{2,100}$/u',
                        'message' => 'form.profile.validation.city_invalid',
                    ]),
                ],
                'attr' => ['placeholder' => $this->translator->trans('form.profile.placeholder.city')]
            ])

            ->add('codePostal', TextType::class, [
                'label' => 'form.profile.postal_code',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9\- ]{3,12}$/',
                        'message' => 'form.profile.validation.postal_code_invalid',
                    ])
                ],
                'attr' => ['placeholder' => $this->translator->trans('form.profile.placeholder.postal_code')]
            ]);

        /* =======================================================
         *  PHOTOS
         * ======================================================= */
        $builder
            ->add('photoProfil', FileType::class, [
                'label' => 'form.profile.profile_photo',
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
                'label' => 'form.profile.tshirt_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
                'required' => false
            ])
            ->add('taillePolo', ChoiceType::class, [
                'label' => 'form.profile.polo_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
                'required' => false
            ])
            ->add('taillePull', ChoiceType::class, [
                'label' => 'form.profile.pullover_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleSweat', ChoiceType::class, [
                'label' => 'form.profile.sweatshirt_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleVeste', ChoiceType::class, [
                'label' => 'form.profile.jacket_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleShort', ChoiceType::class, [
                'label' => 'form.profile.short_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.profile.placeholder.choose'),
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
