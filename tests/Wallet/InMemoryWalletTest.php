<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Wallet\InMemoryWallet;
use PHPUnit\Framework\TestCase;

class InMemoryWalletTest extends TestCase
{
    /**
     * @var InMemoryWallet
     */
    private $wallet;

    protected function setUp()
    {
        $this->wallet = new InMemoryWallet(10);
    }

    /**
     * @test
     */
    public function it_deposits_credit()
    {
        $this->wallet->deposit(Credit::fromInteger(5));

        $this->assertAttributeEquals(Credit::fromInteger(15), 'amount', $this->wallet);
    }

    /**
     * @test
     */
    public function it_withdraws_credit()
    {
        $this->wallet->withdraw(Credit::fromInteger(10));

        $this->assertAttributeEquals(Credit::blank(), 'amount', $this->wallet);
    }

    /**
     * @test
     */
    public function overdraft_must_not_be_possible()
    {
        $this->expectException(InsufficientFunds::class);
        $this->expectExceptionMessage('Insufficient credit: 5.');

        $this->wallet->withdraw(Credit::fromInteger(15));
    }
}
