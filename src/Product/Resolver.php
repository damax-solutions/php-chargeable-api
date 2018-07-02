<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Product;

interface Resolver
{
    /**
     * @throws ProductResolutionFailed
     */
    public function resolve($request): Product;
}
