<?php

namespace App\Form;

use App\Entity\MerchStock;
use App\Service\MerchSizingService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MerchStockType extends AbstractType
{
    public function __construct(
        private readonly MerchSizingService $sizingService
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var MerchStock|null $stock */
        $stock = $builder->getData();
        $currentSize = $stock?->getSize();

        $sizes = $this->sizingService
            ->getAdminSizesForCategorySlug($options['category_slug']);

        if ($currentSize && !in_array($currentSize, $sizes, true)) {
            $sizes[] = $currentSize;
        }

        $choices = array_combine($sizes, $sizes);

        $builder->add('size', ChoiceType::class, [
            'label' => 'Taille',
            'choices' => $choices,
            'disabled' => $stock && $stock->getId() !== null,
        ]);

        $builder->add('quantity', IntegerType::class, [
            'label' => 'Quantite',
            'empty_data' => '0',
            'data' => 0,
            'constraints' => [
                new NotBlank(),
                new GreaterThanOrEqual(0),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MerchStock::class,
            'category_slug' => null,
        ]);
    }
}
