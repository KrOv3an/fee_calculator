<?php

namespace App\Tests\Commission;

use App\Service\CommissionStrategy\EuCommissionStrategy;
use PHPUnit\Framework\TestCase;

class EuCommissionStrategyTest extends TestCase
{
    public function testCalculateCommission(): void
    {
        $calculator = new EuCommissionStrategy();
        $result = $calculator->calculateCommission(100);
        $this->assertSame(1.0, $result);
    }
}
