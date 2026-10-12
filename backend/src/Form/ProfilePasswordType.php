<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfilePasswordType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'form.profile_password.current',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.profile_password.validation.current_required']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'form.profile_password.new',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.profile_password.validation.new_required']),
                    new Assert\Length(['min' => 8, 'minMessage' => 'form.profile_password.validation.min_length']),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'form.profile_password.validation.uppercase',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'form.profile_password.validation.lowercase',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'form.profile_password.validation.number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W]/',
                        'message' => 'form.profile_password.validation.special',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'form.profile_password.confirm',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'form.profile_password.validation.confirm_required']),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event): void {
            $form = $event->getForm();
            $password = (string) $form->get('plainPassword')->getData();
            $confirm = (string) $form->get('confirmPassword')->getData();

            if ($password !== $confirm) {
                $form->get('confirmPassword')->addError(
                    new \Symfony\Component\Form\FormError($this->translator->trans('form.profile_password.validation.passwords_mismatch'))
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_token_id' => 'profile_change_password',
        ]);
    }
}
