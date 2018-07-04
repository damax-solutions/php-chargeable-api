<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Bundle\DependencyInject;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\DamaxChargeableApiExtension;
use Damax\ChargeableApi\Bridge\Symfony\Bundle\Listener\PurchaseListener;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\NotificationStore;
use Damax\ChargeableApi\Bridge\Symfony\Security\TokenIdentityFactory;
use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\Processor;
use Damax\ChargeableApi\Product\FixedProductResolver;
use Damax\ChargeableApi\Store\Store;
use Damax\ChargeableApi\Store\StoreProcessor;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Damax\ChargeableApi\Wallet\RedisWalletFactory;
use Damax\ChargeableApi\Wallet\WalletFactory;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher;

class DamaxChargeableApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_registers_in_memory_wallet()
    {
        $this->load([
            'wallet' => [
                'john.doe' => 15,
                'jane.doe' => 25,
            ],
        ]);

        $this->assertContainerBuilderHasService(WalletFactory::class, InMemoryWalletFactory::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(WalletFactory::class, 0, [
            'john.doe' => 15,
            'jane.doe' => 25,
        ]);
    }

    /**
     * @test
     */
    public function it_registers_redis_wallet()
    {
        $this->load([
            'wallet' => [
                'type' => 'redis',
                'wallet_key' => 'wallet',
                'redis_client_id' => 'snc_redis.default',
            ],
        ]);

        $this->assertContainerBuilderHasService(WalletFactory::class, RedisWalletFactory::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(WalletFactory::class, 0, new Reference('snc_redis.default'));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(WalletFactory::class, 1, 'wallet');
    }

    /**
     * @test
     */
    public function it_registers_custom_wallet_service()
    {
        $this->load([
            'wallet' => [
                'type' => 'service',
                'factory_service_id' => 'wallet_factory_service',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(WalletFactory::class, 'wallet_factory_service');
    }

    /**
     * @test
     */
    public function it_registers_security_identity()
    {
        $this->load();

        $this->assertContainerBuilderHasService(IdentityFactory::class, TokenIdentityFactory::class);
    }

    /**
     * @test
     */
    public function it_registers_fixed_identity()
    {
        $this->load(['identity' => 'john.doe']);

        $this->assertContainerBuilderHasService(IdentityFactory::class, FixedIdentityFactory::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(IdentityFactory::class, 0, 'john.doe');
    }

    /**
     * @test
     */
    public function it_registers_custom_identity_service()
    {
        $this->load([
            'identity' => [
                'type' => 'service',
                'factory_service_id' => 'identity_factory_service',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(IdentityFactory::class, 'identity_factory_service');
    }

    /**
     * @test
     */
    public function it_registers_default_product_resolver()
    {
        $this->load(['product' => 5]);

        $this->assertContainerBuilderHasService(FixedProductResolver::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(FixedProductResolver::class, 0, 'API');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(FixedProductResolver::class, 1, 5);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(FixedProductResolver::class, 'damax.chargeable_api.product_resolver', ['priority' => -1024]);
    }

    /**
     * @test
     */
    public function it_registers_store_services()
    {
        $this->load();

        $this->assertContainerBuilderHasService(Store::class, NotificationStore::class);
        $this->assertContainerBuilderHasService(Processor::class, StoreProcessor::class);
    }

    /**
     * @test
     */
    public function it_registers_listener()
    {
        $this->load([
            'listener' => [
                'priority' => 6,
                'matcher' => '^/api/',
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(PurchaseListener::class, 'kernel.event_listener', [
            'event' => 'kernel.request',
            'method' => 'onKernelRequest',
            'priority' => 6,
        ]);

        /** @var Definition $matcher */
        $matcher = $this->container->getDefinition(PurchaseListener::class)->getArgument(0);

        $this->assertEquals(RequestMatcher::class, $matcher->getClass());
        $this->assertEquals('^/api/', $matcher->getArgument(0));
        $this->assertNull($matcher->getArgument(1)); // Host
        $this->assertNull($matcher->getArgument(2)); // Methods
        $this->assertNull($matcher->getArgument(3)); // IPs
    }

    protected function getContainerExtensions(): array
    {
        return [
            new DamaxChargeableApiExtension(),
        ];
    }
}
