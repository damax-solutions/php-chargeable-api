<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\Listener\PurchaseListener;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\NotificationStore;
use Damax\ChargeableApi\Bridge\Symfony\Security\TokenIdentityFactory;
use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\Processor;
use Damax\ChargeableApi\Product\FixedProductResolver;
use Damax\ChargeableApi\Store\Store;
use Damax\ChargeableApi\Store\StoreProcessor;
use Damax\ChargeableApi\Store\WalletStore;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Damax\ChargeableApi\Wallet\MongoWalletFactory;
use Damax\ChargeableApi\Wallet\RedisWalletFactory;
use Damax\ChargeableApi\Wallet\WalletFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class DamaxChargeableApiExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this
            ->configureWallet($config['wallet'], $container)
            ->configureIdentity($config['identity'], $container)
            ->configureProduct($config['product'], $container)
            ->configureListener($config['listener'], $container)
            ->configureStore($container)
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
                    ->addArgument($config['wallet_key'])
                ;
                break;
            case Configuration::WALLET_MONGO:
                $container
                    ->register(WalletFactory::class, MongoWalletFactory::class)
                    ->addArgument(new Reference($config['mongo_client_id']))
                    ->addArgument($config['db_name'])
                    ->addArgument($config['collection_name'])
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

    private function configureListener(array $config, ContainerBuilder $container): self
    {
        $ips = !empty($config['matcher']['ips']) ? $config['matcher']['ips'] : null;
        $methods = !empty($config['matcher']['methods']) ? $config['matcher']['methods'] : null;

        $matcher = (new Definition(RequestMatcher::class))
            ->addArgument($config['matcher']['path'] ?? null)
            ->addArgument($config['matcher']['host'] ?? null)
            ->addArgument($methods)
            ->addArgument($ips)
        ;

        $container
            ->register(PurchaseListener::class)
            ->addArgument($matcher)
            ->addArgument(new Reference(Processor::class))
            ->addTag('kernel.event_listener', [
                'event' => 'kernel.request',
                'method' => 'onKernelRequest',
                'priority' => $config['priority'],
            ])
        ;

        return $this;
    }

    private function configureStore(ContainerBuilder $container): self
    {
        // Decorated store.
        $store = (new Definition(WalletStore::class))->setAutowired(true);

        $container
            ->autowire(Store::class, NotificationStore::class)
            ->addArgument($store)
        ;
        $container->autowire(Processor::class, StoreProcessor::class);

        return $this;
    }
}
