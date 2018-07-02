<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Credit;

final class InMemoryWallet implements Wallet
{
    private $amount;

    public function __construct(int $credit)
    {
        $this->amount = Credit::fromInteger($credit);
    }

    public function deposit(Credit $credit): void
    {
        $this->amount = $this->amount->add($credit);
    }

    public function withdraw(Credit $credit): void
    {
        $this->amount = $this->amount->subtract($credit);
    }
}
