<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Credit;
use Predis\ClientInterface;

final class RedisWallet implements Wallet
{
    private $client;
    private $walletKey;
    private $identity;

    public function __construct(ClientInterface $client, string $walletKey, string $identity)
    {
        $this->client = $client;
        $this->walletKey = $walletKey;
        $this->identity = $identity;
    }

    public function balance(): Credit
    {
        $balance = $this->client->hget($this->walletKey, $this->identity) ?? 0;

        return Credit::fromInteger($balance);
    }

    public function deposit(Credit $credit): void
    {
        $this->client->hincrby($this->walletKey, $this->identity, $credit->toInteger());
    }

    public function withdraw(Credit $credit): void
    {
        $balance = $this->balance()->subtract($credit)->toInteger();

        $this->client->hset($this->walletKey, $this->identity, $balance);
    }
}
