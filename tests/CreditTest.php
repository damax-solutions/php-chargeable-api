<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InvalidOperationException;
use PHPUnit\Framework\TestCase;

class CreditTest extends TestCase
{
    private const OP_LT = 'lt';
    private const OP_GT = 'gt';
    private const OP_GTE = 'gte';

    /**
     * @test
     */
    public function it_creates_blank_credit()
    {
        $credit = Credit::blank();

        $this->assertAttributeEquals(0, 'value', $credit);

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

        $this->assertAttributeEquals(10, 'value', $newCredit);
    }

    /**
     * @test
     */
    public function negative_credit_is_not_supported()
    {
        $this->expectException(InvalidOperationException::class);
        $this->expectExceptionMessage('Credit can not be negative.');

        Credit::fromInteger(-10);
    }

    /**
     * @test
     *
     * @dataProvider provideCreditData
     */
    public function it_compares_credit(int $one, int $two, string $cmp)
    {
        $one = Credit::fromInteger($one);
        $two = Credit::fromInteger($two);

        $result = $one->greaterThanOrEquals($two);

        switch ($cmp) {
            case self::OP_GT:
            case self::OP_GTE:
                $this->assertTrue($result);
                break;
            case self::OP_LT:
                $this->assertFalse($result);
                break;
            default:
                $this->fail('Operation not matched.');
        }
    }

    /**
     * @test
     */
    public function it_subtracts_credit()
    {
        $one = Credit::fromInteger(20);
        $two = Credit::fromInteger(5);

        $this->assertAttributeEquals(15, 'value', $one->subtract($two));

        $this->expectException(InvalidOperationException::class);
        $this->expectExceptionMessage('Insufficient credit: 15.');

        $two->subtract($one);
    }

    public function provideCreditData(): array
    {
        return [
            [20, 10, self::OP_GT],
            [10, 10, self::OP_GTE],
            [10, 20, self::OP_LT],
        ];
    }
}
