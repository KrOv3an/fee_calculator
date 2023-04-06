<?php

declare(strict_types=1);

namespace App\Service\Commission;

class EuCommissionStrategy implements CommissionStrategyInterface
{
    public function calculateCommission(float $amount): float
    {
        return $amount * 0.01;
    }
}
