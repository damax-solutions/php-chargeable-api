<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection;

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
        $this->configureWallet($config['wallet'], $container);
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
}
