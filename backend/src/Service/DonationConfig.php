<?php

namespace App\Service;

final class DonationConfig
{
    /**
     * @param array<int, float|int|string> $presetAmounts
     */
    public function __construct(
        private readonly array $presetAmounts = [],
        private readonly float $minimumAmount = 0.0,
        private readonly ?float $maximumAmount = null,
        private readonly string $currency = 'EUR',
    ) {
    }

    /**
     * @return array<int, float>
     */
    public function getPresetAmounts(): array
    {
        $amounts = [];
        foreach ($this->presetAmounts as $amount) {
            $normalized = round((float) $amount, 2);
            if ($normalized > 0) {
                $amounts[] = $normalized;
            }
        }

        $amounts = array_values(array_unique($amounts));
        sort($amounts);

        return $amounts;
    }

    public function getMinimumAmount(): float
    {
        return round(max(0, $this->minimumAmount), 2);
    }

    public function getMaximumAmount(): ?float
    {
        if ($this->maximumAmount === null || $this->maximumAmount <= 0) {
            return null;
        }

        return round($this->maximumAmount, 2);
    }

    public function getCurrency(): string
    {
        return strtoupper(trim($this->currency)) ?: 'EUR';
    }

    public function isAmountAllowed(float $amount): bool
    {
        $amount = round($amount, 2);
        if ($amount < $this->getMinimumAmount()) {
            return false;
        }

        $maximumAmount = $this->getMaximumAmount();
        if ($maximumAmount !== null && $amount > $maximumAmount) {
            return false;
        }

        return true;
    }

    public function isPresetAmount(float $amount): bool
    {
        return in_array(round($amount, 2), $this->getPresetAmounts(), true);
    }
}
