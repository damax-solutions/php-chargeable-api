<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Store;

use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Product\Product;

interface Store
{
    /**
     * @throws InsufficientFunds
     */
    public function purchase(Identity $identity, Product $product): Receipt;
}
