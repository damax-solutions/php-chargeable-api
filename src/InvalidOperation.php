<?php

declare(strict_types=1);

namespace Damax\ChargeableApi;

use DomainException;

class InvalidOperation extends DomainException
{
    public static function negativeCredit(): self
    {
        return new self('Credit can not be negative.');
    }
}
