<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Store;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\Product\Product;
use DateTimeImmutable;
use DateTimeInterface;

final class Receipt
{
    private $identity;
    private $product;
    private $date;

    public function __construct(Identity $identity, Product $product, DateTimeInterface $date = null)
    {
        $this->identity = $identity;
        $this->product = $product;
        $this->date = $date ?? new DateTimeImmutable();
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function price(): Credit
    {
        return $this->product->price();
    }

    public function date(): DateTimeInterface
    {
        return $this->date;
    }
}
