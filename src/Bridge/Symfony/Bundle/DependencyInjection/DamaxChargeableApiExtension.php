<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\ChargeableApi\Bridge\Symfony\Security\TokenIdentityFactory;
use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\Product\FixedProductResolver;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Damax\ChargeableApi\Wallet\RedisWalletFactory;
use Damax\ChargeableApi\Wallet\WalletFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class DamaxChargeableApiExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $this
            ->configureWallet($config['wallet'], $container)
            ->configureIdentity($config['identity'], $container)
            ->configureProduct($config['product'], $container)
        ;
    }

    private function configureWallet(array $config, ContainerBuilder $container): self
    {
        switch ($config['type']) {
            case Configuration::WALLET_FIXED:
                $container
                    ->register(WalletFactory::class, InMemoryWalletFactory::class)
                    ->addArgument($config['accounts'])
                ;
                break;
            case Configuration::WALLET_REDIS:
                $container
                    ->register(WalletFactory::class, RedisWalletFactory::class)
                    ->addArgument(new Reference($config['redis_client_id']))
                    ->addArgument(new Reference($config['wallet_key']))
                ;
                break;
            case Configuration::WALLET_SERVICE:
                $container->setAlias(WalletFactory::class, $config['factory_service_id']);
                break;
        }

        return $this;
    }

    private function configureIdentity(array $config, ContainerBuilder $container): self
    {
        switch ($config['type']) {
            case Configuration::IDENTITY_FIXED:
                $container
                    ->register(IdentityFactory::class, FixedIdentityFactory::class)
                    ->addArgument($config['identity'])
                ;
                break;
            case Configuration::IDENTITY_SECURITY:
                $container->autowire(IdentityFactory::class, TokenIdentityFactory::class);
                break;
            case Configuration::IDENTITY_SERVICE:
                $container->setAlias(IdentityFactory::class, $config['factory_service_id']);
                break;
        }

        return $this;
    }

    private function configureProduct(array $config, ContainerBuilder $container): self
    {
        $container
            ->register(FixedProductResolver::class)
            ->addArgument($config['default']['name'])
            ->addArgument($config['default']['price'])
            ->addTag('damax.chargeable_api.product_resolver', ['priority' => -1024])
        ;

        return $this;
    }
}
