<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\Wallet\RedisWallet;
use Damax\ChargeableApi\Wallet\RedisWalletFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\ClientInterface;

class RedisWalletFactoryTest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var RedisWalletFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->factory = new RedisWalletFactory($this->client, 'wallet');
    }

    /**
     * @test
     */
    public function it_creates_wallet()
    {
        $wallet = $this->factory->create(new UserIdentity('john.doe'));

        $this->assertInstanceOf(RedisWallet::class, $wallet);
        $this->assertAttributeSame($this->client, 'client', $wallet);
        $this->assertAttributeEquals('wallet', 'walletKey', $wallet);
        $this->assertAttributeEquals('john.doe', 'identity', $wallet);
    }
}
