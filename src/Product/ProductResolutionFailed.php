<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Product;

use RuntimeException;

final class ProductResolutionFailed extends RuntimeException
{
    public static function unresolved(): self
    {
        return new self('Unable to resolve product.');
    }
}
