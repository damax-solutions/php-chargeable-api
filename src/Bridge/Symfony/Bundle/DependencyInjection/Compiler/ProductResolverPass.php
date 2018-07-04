<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Damax\ChargeableApi\Product\ChainResolver;
use Damax\ChargeableApi\Product\Resolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProductResolverPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $resolvers = $this->findAndSortTaggedServices('damax.chargeable_api.product_resolver', $container);

        $container
            ->register(Resolver::class, ChainResolver::class)
            ->addArgument($resolvers)
        ;
    }
}
