<?php

namespace App\Form;

use App\Service\DonationConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class DonationType extends AbstractType
{
    public function __construct(private readonly DonationConfig $donationConfig)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $presetChoices = [];
        foreach ($this->donationConfig->getPresetAmounts() as $amount) {
            $label = number_format($amount, 0, ',', ' ') . ' €';
            $presetChoices[$label] = (string) $amount;
        }
        $presetChoices['Autre montant'] = 'custom';

        $builder
            ->add('submissionToken', HiddenType::class)
            ->add('amountChoice', ChoiceType::class, [
                'choices' => $presetChoices,
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'donation.form.validation.amount_choice_required'),
                ],
            ])
            ->add('customAmount', MoneyType::class, [
                'currency' => $this->donationConfig->getCurrency(),
                'scale' => 2,
                'required' => false,
                'label' => false,
                'html5' => true,
                'attr' => [
                    'inputmode' => 'decimal',
                    'placeholder' => '25,00',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'donation.form.first_name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'donation.form.validation.first_name_required'),
                    new Assert\Length(max: 120),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'donation.form.last_name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'donation.form.validation.last_name_required'),
                    new Assert\Length(max: 120),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'donation.form.email',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'donation.form.validation.email_required'),
                    new Assert\Email(message: 'donation.form.validation.email_invalid'),
                    new Assert\Length(max: 180),
                ],
            ])
            ->add('anonymous', CheckboxType::class, [
                'label' => 'donation.form.anonymous',
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'donation.form.message',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'maxlength' => 500,
                ],
                'constraints' => [
                    new Assert\Length(max: 500, maxMessage: 'donation.form.validation.message_too_long'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DonationFormData::class,
            'translation_domain' => 'messages',
        ]);
    }
}
