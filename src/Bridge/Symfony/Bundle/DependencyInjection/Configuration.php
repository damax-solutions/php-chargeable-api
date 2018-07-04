<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const WALLET_FIXED = 'fixed';
    public const WALLET_REDIS = 'redis';
    public const WALLET_SERVICE = 'service';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('damax_chargeable_api');
        $rootNode
            ->children()
                ->append($this->walletNode('wallet'))
            ->end()
        ;

        return $treeBuilder;
    }

    private function walletNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifTrue(function (array $config): bool {
                    return !isset($config['type']);
                })
                ->then(function (array $config): array {
                    return ['type' => self::WALLET_FIXED, 'accounts' => $config];
                })
            ->end()
            ->validate()
                ->ifTrue(function (array $config): bool {
                    return self::WALLET_SERVICE === $config['type'] && empty($config['factory_service_id']);
                })
                ->thenInvalid('Service id must be specified.')
            ->end()
            ->validate()
                ->ifTrue(function (array $config): bool {
                    return self::WALLET_REDIS === $config['type'] && (empty($config['redis_client_id']) || empty($config['wallet_key']));
                })
                ->thenInvalid('Wallet key and Redis client must be specified.')
            ->end()
            ->children()
                ->enumNode('type')
                    ->values([self::WALLET_FIXED, self::WALLET_REDIS, self::WALLET_SERVICE])
                    ->defaultValue(self::WALLET_FIXED)
                ->end()
                ->scalarNode('redis_client_id')->end()
                ->scalarNode('wallet_key')->end()
                ->scalarNode('factory_service_id')->end()
                ->arrayNode('accounts')
                    ->useAttributeAsKey(true)
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
