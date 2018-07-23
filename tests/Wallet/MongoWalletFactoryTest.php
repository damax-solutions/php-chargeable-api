<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\Wallet\MongoWallet;
use Damax\ChargeableApi\Wallet\MongoWalletFactory;
use MongoDB\Client;
use PHPUnit\Framework\TestCase;

class MongoWalletFactoryTest extends TestCase
{
    /**
     * @var Client
     */
    private $mongo;

    protected function setUp()
    {
        $this->mongo = new Client();
    }

    /**
     * @test
     */
    public function it_creates_wallet()
    {
        $wallet = (new MongoWalletFactory($this->mongo, 'api', 'wallet'))->create(new UserIdentity('john.doe'));

        $collection = $this->mongo->selectCollection('api', 'wallet');

        $this->assertInstanceOf(MongoWallet::class, $wallet);
        $this->assertAttributeEquals(['_id' => 'john.doe'], 'filters', $wallet);
        $this->assertAttributeEquals($collection, 'collection', $wallet);
    }
}
