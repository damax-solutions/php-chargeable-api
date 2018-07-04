<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Store;

use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Store\WalletStore;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use PHPUnit\Framework\TestCase;

class WalletStoreTest extends TestCase
{
    /**
     * @var WalletStore
     */
    private $store;

    protected function setUp()
    {
        $this->store = new WalletStore(new InMemoryWalletFactory([
            'john.doe' => 10,
            'jane.doe' => 50,
        ]));
    }

    /**
     * @test
     */
    public function it_purchases_product()
    {
        $receipt = $this->store->purchase(
            $identity = new UserIdentity('john.doe'),
            $product = new Product('API', 5)
        );

        $this->assertSame($identity, $receipt->identity());
        $this->assertSame($product, $receipt->product());
    }

    /**
     * @test
     */
    public function not_enough_funds_for_product_purchase()
    {
        $this->expectException(InsufficientFunds::class);
        $this->expectExceptionMessage('Insufficient credit: 1.');

        $this->store->purchase(new UserIdentity('jane.doe'), new Product('service', 51));
    }
}
