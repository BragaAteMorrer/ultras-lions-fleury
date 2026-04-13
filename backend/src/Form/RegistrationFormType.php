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
    PasswordType,
    FileType,
    ChoiceType,
    TelType
};
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sizes = [
            'XS' => 'XS',
            'S' => 'S',
            'M' => 'M',
            'L' => 'L',
            'XL' => 'XL',
            'XXL'=> 'XXL',
        ];

        $builder

            /* -------------------- CODE INVITATION -------------------- */
            ->add('inviteCode', TextType::class, [
                'mapped' => false,
                'label' => 'Code d’invitation',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le code est obligatoire.']),
                ],
            ])

            /* -------------------- IDENTITÉ -------------------- */
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])

            /* -------------------- LOGIN -------------------- */
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            /* -------------------- PASSWORD + CONFIRM -------------------- */
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Mot de passe requis']),
                    new Assert\Length(['min' => 8, 'minMessage' => 'Minimum 8 caractères']),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Il doit contenir au moins une MAJUSCULE.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Il doit contenir au moins une minuscule.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Il doit contenir au moins un chiffre.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W]/',
                        'message' => 'Il doit contenir au moins un caractère spécial.',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Tu dois confirmer le mot de passe']),
                ],
            ])

            /* -------------------- COORDONNÉES -------------------- */
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '+33612345678 / +32460123456…',
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
                    }),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 100),
                ],
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9\- ]{3,12}$/',
                        'message' => 'Code postal invalide.',
                    ]),
                ],
            ])

            /* -------------------- TAILLES -------------------- */
            ->add('tailleTshirt', ChoiceType::class, [
                'label' => 'T-shirt',
                'choices' => $sizes,
                'placeholder' => 'Choisir…',
                'required' => false
            ])
            ->add('tailleSweat', ChoiceType::class, [
                'label' => 'Sweat',
                'choices' => $sizes,
                'placeholder' => 'Choisir…',
                'required' => false
            ])
            ->add('tailleVeste', ChoiceType::class, [
                'label' => 'Veste',
                'choices' => $sizes,
                'placeholder' => 'Choisir…',
                'required' => false
            ])
            ->add('tailleShort', ChoiceType::class, [
                'label' => 'Short',
                'choices' => $sizes,
                'placeholder' => 'Choisir…',
                'required' => false
            ])

            /* -------------------- PHOTO -------------------- */
            ->add('photoProfil', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
            ]);

        /* -------------------------------
         * VALIDATION : MDP == CONFIRM
         * ------------------------------- */
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $pwd  = $form->get('plainPassword')->getData();
            $confirm = $form->get('confirmPassword')->getData();

            if ($pwd !== $confirm) {
                $form->get('confirmPassword')->addError(
                    new \Symfony\Component\Form\FormError("Les mots de passe ne correspondent pas.")
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
