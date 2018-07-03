<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Credit;

final class InMemoryWallet implements Wallet
{
    private $balance;

    public function __construct(int $balance)
    {
        $this->balance = Credit::fromInteger($balance);
    }

    public function balance(): Credit
    {
        return $this->balance;
    }

    public function deposit(Credit $credit): void
    {
        $this->balance = $this->balance()->add($credit);
    }

    public function withdraw(Credit $credit): void
    {
        $this->balance = $this->balance()->subtract($credit);
    }
}
