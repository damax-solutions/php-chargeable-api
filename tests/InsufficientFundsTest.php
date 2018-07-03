<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests;

use Damax\ChargeableApi\InsufficientFunds;
use PHPUnit\Framework\TestCase;

class InsufficientFundsTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception()
    {
        $this->assertEquals('Insufficient credit: 10.', InsufficientFunds::notEnough(10)->getMessage());
    }
}
