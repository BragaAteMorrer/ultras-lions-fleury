<?php

namespace App\Service;

use App\Entity\Merch;
use App\Entity\User;

class MerchSizingService
{
    /** @return list<string> Sizes allowed in admin stock form */
    public function getAdminSizesForCategorySlug(?string $slug): array
    {
        if ($slug === null || $slug === '') {
            return $this->getAllAdminSizes();
        }

        $slug = $this->normalizeSlug($slug);

        return match ($slug) {
            't-shirt',
            'polo',
            'chemise',
            'pull-sweat',
            'veste',
            'manteau',
            'short' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'chaussure' => array_map(static fn (int $s) => (string) $s, range(36, 46)),
            'echarpe' => ['TU'],
            default => ['TU'],
        };
    }

    /** @return array<string, string> ChoiceType choices (label => value) */
    public function getSizeChoicesForMerch(Merch $merch): array
    {
        $choices = [];

        foreach ($merch->getStocks() as $stock) {
            $label = sprintf('%s (%d dispo)', $stock->getSize(), $stock->getQuantity());
            $choices[$label] = $stock->getSize();
        }

        if ($choices) {
            return $choices;
        }

        $slug = $this->normalizeSlug($merch->getCategory()?->getSlug());

        if ($slug === null) {
            return [];
        }

        $defaultSizes = match ($slug) {
            't-shirt',
            'polo',
            'chemise',
            'pull-sweat',
            'veste',
            'manteau',
            'short' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'chaussure' => array_map(static fn (int $s) => (string) $s, range(36, 46)),
            'echarpe' => ['TU'],
            default => [],
        };

        foreach ($defaultSizes as $size) {
            $choices[sprintf('%s (0 dispo)', $size)] = $size;
        }

        return $choices;
    }

    public function getPreferredSizeForUser(Merch $merch, ?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        $slug = $this->normalizeSlug($merch->getCategory()?->getSlug());

        return match ($slug) {
            't-shirt' => $user->getTailleTshirt(),
            'polo' => $user->getTaillePolo(),
            'chemise' => $user->getTaillePolo() ?? $user->getTailleTshirt(),
            'pull-sweat' => $user->getTaillePull() ?? $user->getTailleSweat(),
            'veste' => $user->getTailleVeste(),
            'manteau' => $user->getTailleVeste(),
            'short' => $user->getTailleShort(),
            default => null,
        };
    }

    private function normalizeSlug(?string $slug): ?string
    {
        if ($slug === null) {
            return null;
        }

        $slug = trim(mb_strtolower($slug));

        return match ($slug) {
            't-shirts', 'tee-shirt', 'tee-shirts', 'tshirt', 'tshirts' => 't-shirt',
            'polos' => 'polo',
            'chemises' => 'chemise',
            'pull', 'pulls', 'sweat', 'sweats' => 'pull-sweat',
            'vestes' => 'veste',
            'manteaux' => 'manteau',
            'shorts' => 'short',
            'chaussures' => 'chaussure',
            'echarpes' => 'echarpe',
            default => $slug,
        };
    }

    /** @return list<string> */
    private function getAllAdminSizes(): array
    {
        $apparel = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];
        $shoes = array_map(static fn (int $s) => (string) $s, range(36, 46));

        return array_values(array_unique(array_merge($apparel, $shoes, ['TU'])));
    }
}
