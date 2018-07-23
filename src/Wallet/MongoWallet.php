<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Wallet;

use Damax\ChargeableApi\Credit;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

final class MongoWallet implements Wallet
{
    private const FIELD_BALANCE = 'balance';

    private $collection;
    private $filters;

    public function __construct(Collection $collection, string $identity)
    {
        $this->collection = $collection;
        $this->filters = ['_id' => $identity];
    }

    public function balance(): Credit
    {
        $options = ['projection' => [self::FIELD_BALANCE => 1]];

        /** @var BSONDocument $doc */
        if (null === $doc = $this->collection->findOne($this->filters, $options)) {
            return Credit::blank();
        }

        return Credit::fromInteger($doc[self::FIELD_BALANCE]);
    }

    public function deposit(Credit $credit): void
    {
        $this->collection->updateOne($this->filters, ['$inc' => [self::FIELD_BALANCE => $credit->toInteger()]], ['upsert' => true]);
    }

    public function withdraw(Credit $credit): void
    {
        $balance = $this->balance()->subtract($credit)->toInteger();

        $this->collection->replaceOne($this->filters, [self::FIELD_BALANCE => $balance], ['upsert' => true]);
    }
}
