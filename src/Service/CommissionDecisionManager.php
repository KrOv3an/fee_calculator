<?php

namespace App\Service;

use App\Service\CommissionStrategy\CommissionStrategyInterface;

/**
 * @final
 */
class CommissionDecisionManager
{
    /**
     * @var \Generator<CommissionStrategyInterface>
     */
    protected $commissionStrategies;

    /**
     * @param \Generator<CommissionStrategyInterface> $commissionStrategies
     */
    public function __construct(iterable $commissionStrategies)
    {
        $this->commissionStrategies = $commissionStrategies;
    }

    /**
     * @param string $country
     * @return CommissionStrategyInterface
     */
    public function decide(string $country): CommissionStrategyInterface
    {
        $commissionStrategy = null;

        foreach ($this->commissionStrategies as $strategy) {
            if ($strategy->supports($country)) {
                $commissionStrategy = $strategy;
            }
        }

        return $commissionStrategy ?? $this->commissionStrategies['default_commission_strategy'];
    }

}