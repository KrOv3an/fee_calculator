<?php

namespace App\Tests\Commission;

use App\Service\Commission\DefaultCommissionStrategy;
use PHPUnit\Framework\TestCase;

class DefaultCommissionStrategyTest extends TestCase
{
    public function testCalculateCommission(): void
    {
        $calculator = new DefaultCommissionStrategy();
        $result = $calculator->calculateCommission(100);
        $this->assertSame(2.0, $result);
    }
}
