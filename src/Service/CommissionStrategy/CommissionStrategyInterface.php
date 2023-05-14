<?php

declare(strict_types=1);

namespace App\Service\CommissionStrategy;

interface CommissionStrategyInterface
{
    public static function getName(): string;

    public function supports(string $country): bool;

    public function calculateCommission(float $amount): float;
}
