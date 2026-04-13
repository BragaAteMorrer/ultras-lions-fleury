<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MerchPurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['require_email']) {
            $builder->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ]);
        }

        if ($options['sizes']) {
            $builder->add('size', ChoiceType::class, [
                'label' => 'Taille',
                'choices' => $options['sizes'],
                'data' => $options['preferred_size'],
            ]);
        }

        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantite',
                'data' => 1,
                'empty_data' => '1',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(1),
                ],
            ])
            ->add('note', TextareaType::class, [
                'label' => 'Note (optionnel)',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'require_email' => false,
            'sizes' => [],
            'preferred_size' => null,
        ]);

        $resolver->setAllowedTypes('require_email', 'bool');
        $resolver->setAllowedTypes('sizes', 'array');
        $resolver->setAllowedTypes('preferred_size', ['null', 'string']);
    }
}