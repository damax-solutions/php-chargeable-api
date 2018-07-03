<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Store;

use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Wallet\WalletFactory;

final class WalletStore implements Store
{
    private $walletFactory;

    public function __construct(WalletFactory $walletFactory)
    {
        $this->walletFactory = $walletFactory;
    }

    public function purchase(Identity $identity, Product $product): Receipt
    {
        $this->walletFactory->create($identity)->withdraw($product->price());

        return new Receipt($identity, $product);
    }
}
