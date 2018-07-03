<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests;

use Damax\ChargeableApi\InvalidOperation;
use PHPUnit\Framework\TestCase;

class InvalidOperationTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception()
    {
        $this->assertEquals('Credit can not be negative.', InvalidOperation::negativeCredit()->getMessage());
    }
}
