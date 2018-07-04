<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\Compiler\ProductResolverPass;
use Damax\ChargeableApi\Product\ChainResolver;
use Damax\ChargeableApi\Product\Resolver;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProductResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_chain_resolver()
    {
        $this->compile();

        $this->assertContainerBuilderHasService(Resolver::class, ChainResolver::class);
    }

    /**
     * @test
     */
    public function it_registers_tagged_services()
    {
        $this->container
            ->register('damax.chargeable_api.product_resolver.one')
            ->addTag('damax.chargeable_api.product_resolver', ['priority' => 8])
        ;
        $this->container
            ->register('damax.chargeable_api.product_resolver.two')
            ->addTag('damax.chargeable_api.product_resolver', ['priority' => 16])
        ;

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Resolver::class, 0, [
            new Reference('damax.chargeable_api.product_resolver.two'),
            new Reference('damax.chargeable_api.product_resolver.one'),
        ]);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ProductResolverPass());
    }
}
