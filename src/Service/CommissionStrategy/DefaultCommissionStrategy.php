<?php

declare(strict_types=1);

namespace App\Service\CommissionStrategy;

class DefaultCommissionStrategy implements CommissionStrategyInterface
{

    public const STRATEGY_NAME = 'default_commission_strategy';

    public static function getName(): string
    {
        return self::STRATEGY_NAME;
    }

    public function supports(string $country): bool
    {
        return false;
    }

    public function calculateCommission(float $amount): float
    {
        return $amount * 0.02;
    }
}
