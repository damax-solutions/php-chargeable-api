<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Store;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\Identity\Identity;
use DateTimeImmutable;
use DateTimeInterface;

final class Receipt
{
    private $identity;
    private $amount;
    private $date;

    public function __construct(Identity $identity, Credit $amount, DateTimeInterface $date = null)
    {
        $this->identity = $identity;
        $this->amount = $amount;
        $this->date = $date ?? new DateTimeImmutable();
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function amount(): Credit
    {
        return $this->amount;
    }

    public function date(): DateTimeInterface
    {
        return $this->date;
    }
}
