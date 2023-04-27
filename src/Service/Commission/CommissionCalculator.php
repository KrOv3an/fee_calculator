<?php

declare(strict_types=1);

namespace App\Service\Commission;

use App\Service\CommissionStrategy\CommissionStrategyInterface;

class CommissionCalculator
{
    public function __construct(private readonly CommissionStrategyInterface $commissionStrategy)
    {
    }

    public function calculateCommission(float $amount): float
    {
        return $this->commissionStrategy->calculateCommission($amount);
    }
}

