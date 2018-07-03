<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Wallet\RedisWallet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\ClientInterface;

class RedisWalletTest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var RedisWallet
     */
    private $wallet;

    protected function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->wallet = new RedisWallet($this->client, 'wallet', 'john.doe');
    }

    /**
     * @test
     */
    public function it_retrieves_balance()
    {
        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('hget', ['wallet', 'john.doe'])
            ->willReturn(10)
        ;

        $this->assertEquals(10, $this->wallet->balance()->toInteger());
    }

    /**
     * @test
     */
    public function it_retrieves_balance_for_missing_field()
    {
        $this->assertEquals(0, $this->wallet->balance()->toInteger());
    }

    /**
     * @test
     */
    public function it_deposits_credit()
    {
        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('hincrby', ['wallet', 'john.doe', 10])
        ;

        $this->wallet->deposit(Credit::fromInteger(10));
    }

    /**
     * @test
     */
    public function it_withdraws_credit()
    {
        $this->client
            ->expects($this->exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['hget', ['wallet', 'john.doe']],
                ['hset', ['wallet', 'john.doe', 5]]
            )
            ->willReturnOnConsecutiveCalls(15)
        ;

        $this->wallet->withdraw(Credit::fromInteger(10));
    }

    /**
     * @test
     */
    public function overdraft_must_not_be_possible()
    {
        $this->expectException(InsufficientFunds::class);
        $this->expectExceptionMessage('Insufficient credit: 5.');

        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('hget', ['wallet', 'john.doe'])
            ->willReturn(5)
        ;

        $this->wallet->withdraw(Credit::fromInteger(10));
    }
}
