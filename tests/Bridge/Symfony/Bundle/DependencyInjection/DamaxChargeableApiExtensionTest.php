<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Bundle\DependencyInject;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\DamaxChargeableApiExtension;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Damax\ChargeableApi\Wallet\RedisWalletFactory;
use Damax\ChargeableApi\Wallet\WalletFactory;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;

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

    protected function getContainerExtensions(): array
    {
        return [
            new DamaxChargeableApiExtension(),
        ];
    }
}
