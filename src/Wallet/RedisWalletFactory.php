<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Identity\Identity;
use Predis\ClientInterface;

final class RedisWalletFactory implements WalletFactory
{
    private $client;
    private $walletKey;

    public function __construct(ClientInterface $client, string $walletKey)
    {
        $this->client = $client;
        $this->walletKey = $walletKey;
    }

    public function create(Identity $identity): Wallet
    {
        return new RedisWallet($this->client, $this->walletKey, (string) $identity);
    }
}
