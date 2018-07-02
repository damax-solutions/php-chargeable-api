<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InsufficientFunds;

interface Wallet
{
    public function deposit(Credit $credit): void;

    /**
     * @throws InsufficientFunds
     */
    public function withdraw(Credit $credit): void;
}
