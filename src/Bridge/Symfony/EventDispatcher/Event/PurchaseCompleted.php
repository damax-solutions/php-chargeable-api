<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event;

use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Store\Receipt;
use Symfony\Component\EventDispatcher\Event;

final class PurchaseCompleted extends Event
{
    private $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function receipt(): Receipt
    {
        return $this->receipt;
    }

    public function identity(): Identity
    {
        return $this->receipt->identity();
    }

    public function product(): Product
    {
        return $this->receipt->product();
    }
}
