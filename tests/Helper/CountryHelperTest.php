<?php

namespace App\Tests\Helper;

use App\Service\Helper\CountryHelper;
use PHPUnit\Framework\TestCase;

class CountryHelperTest extends TestCase
{
    public function testIsEu(): void
    {
        $this->assertTrue(CountryHelper::isEu('FR'));
        $this->assertFalse(CountryHelper::isEu('UK'));
    }
}
