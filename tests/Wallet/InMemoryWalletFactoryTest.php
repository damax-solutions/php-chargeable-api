<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Credit;
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
        $this->assertAttributeEquals(Credit::fromInteger(20), 'amount', $wallet);

        $wallet = $factory->create(new UserIdentity('jane.doe'));
        $this->assertAttributeEquals(Credit::fromInteger(50), 'amount', $wallet);

        $wallet = $factory->create(new UserIdentity('baby.doe'));
        $this->assertAttributeEquals(Credit::blank(), 'amount', $wallet);
    }
}
