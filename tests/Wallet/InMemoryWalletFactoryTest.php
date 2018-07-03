<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use PHPUnit\Framework\TestCase;

class InMemoryWalletFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_wallet()
    {
        $factory = new InMemoryWalletFactory(['john.doe' => 20, 'jane.doe' => 50]);

        $wallet = $factory->create(new UserIdentity('john.doe'));
        $this->assertEquals(20, $wallet->balance()->toInteger());

        $wallet = $factory->create(new UserIdentity('jane.doe'));
        $this->assertEquals(50, $wallet->balance()->toInteger());

        $wallet = $factory->create(new UserIdentity('baby.doe'));
        $this->assertEquals(0, $wallet->balance()->toInteger());
    }
}
