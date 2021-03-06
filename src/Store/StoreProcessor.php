<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Store;

use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Processor;
use Damax\ChargeableApi\Product\ProductResolutionFailed;
use Damax\ChargeableApi\Product\Resolver;

final class StoreProcessor implements Processor
{
    private $store;
    private $identityFactory;
    private $productResolver;

    public function __construct(Store $store, IdentityFactory $identityFactory, Resolver $productResolver)
    {
        $this->store = $store;
        $this->identityFactory = $identityFactory;
        $this->productResolver = $productResolver;
    }

    /**
     * @throws ProductResolutionFailed
     * @throws InsufficientFunds
     */
    public function processRequest($request): void
    {
        $this->store->purchase($this->identityFactory->create(), $this->productResolver->resolve($request));
    }
}
