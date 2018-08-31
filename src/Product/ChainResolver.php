<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Product;

final class ChainResolver implements Resolver
{
    /**
     * @var Resolver[]
     */
    private $resolvers;

    public function __construct(iterable $resolvers = [])
    {
        foreach ($resolvers as $resolver) {
            $this->add($resolver);
        }
    }

    public function add(Resolver $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    public function resolve($request): Product
    {
        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->resolve($request);
            } catch (ProductResolutionFailed $e) {
                // Try next resolver.
            }
        }

        throw ProductResolutionFailed::unresolved();
    }
}
