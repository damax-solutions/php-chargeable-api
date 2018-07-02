<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Identity\Identity;

final class InMemoryWalletFactory implements WalletFactory
{
    private $credits;

    public function __construct(array $credits)
    {
        $this->credits = $credits;
    }

    public function create(Identity $identity): Wallet
    {
        return new InMemoryWallet($this->credits[(string) $identity] ?? 0);
    }
}
