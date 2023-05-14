<?php

declare(strict_types=1);

namespace App\Service\CommissionStrategy;

class EuCommissionStrategy implements CommissionStrategyInterface
{
    public const STRATEGY_NAME = 'eu_commission_strategy';

    const COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK'
    ];

    public static function getName(): string
    {
        return self::STRATEGY_NAME;
    }

    public function supports(string $country): bool
    {
        return in_array($country, self::COUNTRIES);
    }

    public function calculateCommission(float $amount): float
    {
        return $amount * 0.01;
    }
}
