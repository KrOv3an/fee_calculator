<?php

declare(strict_types=1);

namespace App\Service\Commission;

interface CommissionStrategyInterface
{
    public function calculateCommission(float $amount): float;
}
