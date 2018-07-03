<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Identity\Identity;

final class InMemoryWalletFactory implements WalletFactory
{
    private $accounts;

    public function __construct(array $accounts)
    {
        $this->accounts = $accounts;
    }

    public function create(Identity $identity): Wallet
    {
        return new InMemoryWallet($this->accounts[(string) $identity] ?? 0);
    }
}
