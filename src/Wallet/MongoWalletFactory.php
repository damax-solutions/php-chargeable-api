<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Identity\Identity;
use MongoDB\Client;

final class MongoWalletFactory implements WalletFactory
{
    private $client;
    private $dbName;
    private $collectionName;

    public function __construct(Client $client, string $dbName, string $collectionName)
    {
        $this->client = $client;
        $this->dbName = $dbName;
        $this->collectionName = $collectionName;
    }

    public function create(Identity $identity): Wallet
    {
        return new MongoWallet($this->client->selectCollection($this->dbName, $this->collectionName), (string) $identity);
    }
}
