<?php

namespace App\Filter\Admin;

use App\Entity\CartageRegistration;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ChoiceFilterType;

final class UserCartagePaidFilter implements FilterInterface
{
    use FilterTrait;

    private string $currentSeason = '';

    public static function new(string $propertyName, string $label, string $currentSeason): self
    {
        $filter = (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(ChoiceFilterType::class)
            ->setFormTypeOption('value_type_options.choices', [
                sprintf('Cartage paye %s', $currentSeason) => 'paid_current',
                sprintf('Cartage non paye %s', $currentSeason) => 'not_paid_current',
                sprintf('Cartage en attente %s', $currentSeason) => 'pending_current',
                'Cartage paye toutes saisons' => 'paid_any',
                'Cartage en attente toutes saisons' => 'pending_any',
            ]);

        $filter->currentSeason = $currentSeason;

        return $filter;
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $value = (string) $filterDataDto->getValue();
        if ($value === '') {
            return;
        }

        $userAlias = $filterDataDto->getEntityAlias();
        $baseParameter = $filterDataDto->getParameterName();
        $cartageAlias = 'cartage_'.$baseParameter;
        $qrAlias = 'cartage_qr_'.$baseParameter;
        $seasonParameter = $baseParameter.'_season';
        $seasonLabelsParameter = $baseParameter.'_season_labels';
        $statusesParameter = $baseParameter.'_statuses';
        $currentSeason = $this->currentSeason;

        $statuses = match ($value) {
            'pending_current', 'pending_any' => CartageRegistration::PENDING_STATUSES,
            default => CartageRegistration::COMPLETED_STATUSES,
        };

        $seasonCondition = in_array($value, ['paid_current', 'not_paid_current', 'pending_current'], true)
            ? sprintf(' AND (%s.season = :%s OR %s.label IN (:%s))', $cartageAlias, $seasonParameter, $qrAlias, $seasonLabelsParameter)
            : '';

        $existsExpression = sprintf(
            'EXISTS (SELECT %1$s.id FROM %2$s %1$s LEFT JOIN %1$s.qrToken %6$s WHERE LOWER(%1$s.email) = LOWER(%3$s.email)%4$s AND %1$s.status IN (:%5$s))',
            $cartageAlias,
            CartageRegistration::class,
            $userAlias,
            $seasonCondition,
            $statusesParameter,
            $qrAlias
        );

        $queryBuilder
            ->andWhere($value === 'not_paid_current' ? sprintf('NOT %s', $existsExpression) : $existsExpression)
            ->setParameter($statusesParameter, $statuses);

        if ($seasonCondition !== '') {
            $queryBuilder
                ->setParameter($seasonParameter, $currentSeason)
                ->setParameter($seasonLabelsParameter, $this->getSeasonLabels($currentSeason));
        }
    }

    /**
     * @return string[]
     */
    private function getSeasonLabels(string $season): array
    {
        return array_values(array_unique([
            $season,
            str_replace('-', '/', $season),
        ]));
    }
}
