<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Identity\Identity;

interface WalletFactory
{
    public function create(Identity $identity): Wallet;
}
