<?php

namespace App\Form;

use App\Entity\Merch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MerchPhysicalSaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('merch', EntityType::class, [
                'label' => 'Produit',
                'class' => Merch::class,
                'placeholder' => 'Choisir un produit',
                'required' => false,
                'help' => 'Optionnel si tu renseignes un SKU SumUp.',
                'choice_label' => static fn (?Merch $merch): string => $merch instanceof Merch
                    ? sprintf('%s (%d en stock)', (string) $merch->getTitle(), $merch->getTotalStock())
                    : '',
                'query_builder' => static fn ($repository) => $repository
                    ->createQueryBuilder('merch')
                    ->orderBy('merch.title', 'ASC'),
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'required' => false,
                'help' => 'Taille exacte du stock local, par exemple TU, M, L ou 42. Optionnel si tu renseignes un SKU SumUp.',
            ])
            ->add('sumupSku', TextType::class, [
                'label' => 'SKU SumUp',
                'required' => false,
                'help' => 'Si renseigne, le produit et la taille sont retrouves automatiquement.',
                'attr' => [
                    'placeholder' => 'Ex: TAAA4QCHFDK',
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantite',
                'data' => 1,
                'empty_data' => '1',
                'constraints' => [
                    new NotBlank(message: 'La quantite est obligatoire.'),
                    new GreaterThanOrEqual(1),
                ],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Moyen de paiement',
                'choices' => [
                    'SumUp physique' => 'sumup_pos',
                    'SumUp en ligne' => 'sumup',
                    'Liquide' => 'cash',
                    'Gratuit' => 'free',
                ],
                'data' => 'sumup_pos',
                'constraints' => [
                    new NotBlank(message: 'Le moyen de paiement est obligatoire.'),
                ],
            ])
            ->add('useMemberPrice', CheckboxType::class, [
                'label' => 'Appliquer le tarif membre',
                'required' => false,
            ])
            ->add('sumupReference', TextType::class, [
                'label' => 'Reference SumUp',
                'required' => false,
                'help' => "Optionnel: reference de transaction, ticket ou terminal pour l'audit.",
            ])
            ->add('note', TextareaType::class, [
                'label' => 'Note',
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
        ]);
    }
}
