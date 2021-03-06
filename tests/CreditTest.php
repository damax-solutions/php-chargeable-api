<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InvalidOperation;
use PHPUnit\Framework\TestCase;

class CreditTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_blank_credit()
    {
        $credit = Credit::blank();

        $this->assertEquals(0, $credit->toInteger());

        return $credit;
    }

    /**
     * @depends it_creates_blank_credit
     *
     * @test
     */
    public function it_adds_credit(Credit $credit)
    {
        $newCredit = $credit->add(Credit::fromInteger(10));

        $this->assertEquals(10, $newCredit->toInteger());
    }

    /**
     * @test
     */
    public function negative_credit_is_not_supported()
    {
        $this->expectException(InvalidOperation::class);
        $this->expectExceptionMessage('Credit can not be negative.');

        Credit::fromInteger(-10);
    }

    /**
     * @test
     */
    public function it_subtracts_credit()
    {
        $one = Credit::fromInteger(20);
        $two = Credit::fromInteger(5);

        $this->assertEquals(15, $one->subtract($two)->toInteger());

        $this->expectException(InvalidOperation::class);
        $this->expectExceptionMessage('Insufficient credit: 15.');

        $two->subtract($one);
    }
}
