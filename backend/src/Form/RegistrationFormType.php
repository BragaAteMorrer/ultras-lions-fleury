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
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

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
                'label' => 'form.registration.invite_code',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.registration.validation.invite_code_required']),
                ],
            ])

            /* -------------------- IDENTITÉ -------------------- */
            ->add('prenom', TextType::class, [
                'label' => 'form.registration.first_name',
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'form.registration.last_name',
                'required' => true,
            ])

            /* -------------------- LOGIN -------------------- */
            ->add('email', EmailType::class, [
                'label' => 'form.registration.email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.registration.validation.email_required']),
                    new Assert\Email(),
                ],
            ])
            /* -------------------- PASSWORD + CONFIRM -------------------- */
            ->add('plainPassword', PasswordType::class, [
                'label' => 'form.registration.password',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.registration.validation.password_required']),
                    new Assert\Length(['min' => 8, 'minMessage' => 'form.registration.validation.password_min_length']),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'form.registration.validation.password_uppercase',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'form.registration.validation.password_lowercase',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'form.registration.validation.password_number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W]/',
                        'message' => 'form.registration.validation.password_special',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'form.registration.confirm_password',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.registration.validation.confirm_password_required']),
                ],
            ])

            /* -------------------- COORDONNÉES -------------------- */
            ->add('telephone', TelType::class, [
                'label' => 'form.registration.phone',
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('form.registration.placeholder.phone'),
                ],
                'constraints' => [
                    new Assert\Length(min: 5, max: 20),
                    new Assert\Callback(function ($value, $context) {
                        if (!$value) return;

                        $lib = PhoneNumberUtil::getInstance();
                        try {
                            $parsed = $lib->parse($value, null);
                            if (!$lib->isValidNumber($parsed)) {
                                $context->buildViolation('form.registration.validation.phone_invalid')->addViolation();
                            }
                        } catch (\Exception) {
                            $context->buildViolation('form.registration.validation.phone_format')->addViolation();
                        }
                    }),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'form.registration.address',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'form.registration.city',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 100),
                ],
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'form.registration.postal_code',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9\- ]{3,12}$/',
                        'message' => 'form.registration.validation.postal_code_invalid',
                    ]),
                ],
            ])

            /* -------------------- TAILLES -------------------- */
            ->add('tailleTshirt', ChoiceType::class, [
                'label' => 'form.registration.tshirt_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.registration.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleSweat', ChoiceType::class, [
                'label' => 'form.registration.sweatshirt_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.registration.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleVeste', ChoiceType::class, [
                'label' => 'form.registration.jacket_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.registration.placeholder.choose'),
                'required' => false
            ])
            ->add('tailleShort', ChoiceType::class, [
                'label' => 'form.registration.short_size',
                'choices' => $sizes,
                'placeholder' => $this->translator->trans('form.registration.placeholder.choose'),
                'required' => false
            ])

            /* -------------------- PHOTO -------------------- */
            ->add('photoProfil', FileType::class, [
                'label' => 'form.registration.profile_photo',
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
                    new \Symfony\Component\Form\FormError($this->translator->trans('form.registration.validation.passwords_mismatch'))
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
