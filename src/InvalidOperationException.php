<?php

declare(strict_types=1);

namespace Damax\ChargeableApi;

use DomainException;

final class InvalidOperationException extends DomainException
{
    public static function negativeCredit(): self
    {
        return new self('Credit can not be negative.');
    }

    public static function insufficientCredit(int $credit): self
    {
        return new self(sprintf('Insufficient credit: %d.', $credit));
    }
}
