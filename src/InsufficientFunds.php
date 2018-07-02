<?php

declare(strict_types=1);

namespace Damax\ChargeableApi;

final class InsufficientFunds extends InvalidOperation
{
    public static function notEnough(int $credit): self
    {
        return new self(sprintf('Insufficient credit: %d.', $credit));
    }
}
