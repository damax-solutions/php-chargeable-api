<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event;

use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\Product\Product;
use Symfony\Component\EventDispatcher\Event;

final class PurchaseRejected extends Event
{
    private $identity;
    private $product;

    public function __construct(Identity $identity, Product $product)
    {
        $this->identity = $identity;
        $this->product = $product;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function product(): Product
    {
        return $this->product;
    }
}
