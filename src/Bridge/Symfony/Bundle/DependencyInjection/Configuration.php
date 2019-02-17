<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const WALLET_REDIS = 'redis';
    public const WALLET_MONGO = 'mongo';
    public const WALLET_FIXED = 'fixed';
    public const WALLET_SERVICE = 'service';

    public const IDENTITY_FIXED = 'fixed';
    public const IDENTITY_SECURITY = 'security';
    public const IDENTITY_SERVICE = 'service';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('damax_chargeable_api');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->append($this->walletNode('wallet'))
                ->append($this->identityNode('identity'))
                ->append($this->productNode('product'))
                ->append($this->listenerNode('listener'))
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
            ->validate()
                ->ifTrue(function (array $config) {
                    return self::WALLET_MONGO === $config['type'] && (empty($config['mongo_client_id']) || empty($config['db_name']) || empty($config['collection_name']));
                })
                ->thenInvalid('Mongo client, database and collection name must be specified.')
            ->end()
            ->children()
                ->enumNode('type')
                    ->values([self::WALLET_REDIS, self::WALLET_MONGO, self::WALLET_FIXED, self::WALLET_SERVICE])
                    ->defaultValue(self::WALLET_FIXED)
                ->end()

                // Redis
                ->scalarNode('redis_client_id')->cannotBeEmpty()->end()
                ->scalarNode('wallet_key')->cannotBeEmpty()->end()

                // Mongo
                ->scalarNode('mongo_client_id')->cannotBeEmpty()->end()
                ->scalarNode('db_name')->cannotBeEmpty()->end()
                ->scalarNode('collection_name')->cannotBeEmpty()->end()

                // Fixed
                ->arrayNode('accounts')
                    ->useAttributeAsKey(true)
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()
                        ->isRequired()
                    ->end()
                ->end()

                // Service
                ->scalarNode('factory_service_id')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    private function identityNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                ->then(function (string $config): array {
                    return ['type' => self::IDENTITY_FIXED, 'identity' => $config];
                })
            ->end()
            ->validate()
                ->ifTrue(function (array $config): bool {
                    return self::IDENTITY_SERVICE === $config['type'] && empty($config['factory_service_id']);
                })
                ->thenInvalid('Service id must be specified.')
            ->end()
            ->validate()
                ->ifTrue(function (array $config): bool {
                    return self::IDENTITY_FIXED === $config['type'] && empty($config['identity']);
                })
                ->thenInvalid('Identity must be specified.')
            ->end()
            ->children()
                ->enumNode('type')
                    ->values([self::IDENTITY_FIXED, self::IDENTITY_SECURITY, self::IDENTITY_SERVICE])
                    ->defaultValue(self::IDENTITY_SECURITY)
                ->end()
                ->scalarNode('identity')->cannotBeEmpty()->end()
                ->scalarNode('factory_service_id')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    private function productNode(string $name): ArrayNodeDefinition
    {
        $defaults = ['name' => 'API', 'price' => 1];

        return (new ArrayNodeDefinition($name))
            ->requiresAtLeastOneElement()
            ->beforeNormalization()
                ->ifString()
                ->then(function (string $config) use ($defaults): array {
                    return [array_replace($defaults, ['name' => $config])];
                })
            ->end()
            ->beforeNormalization()
                ->ifTrue(function ($config): bool {
                    return is_numeric($config);
                })
                ->then(function (int $config) use ($defaults): array {
                    return [array_replace($defaults, ['price' => $config])];
                })
            ->end()
            ->defaultValue([$defaults])
            ->arrayPrototype()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('price')
                        ->min(1)
                        ->defaultValue(1)
                    ->end()
                    ->append($this->matcherNode('matcher'))
                ->end()
            ->end()
        ;
    }

    private function listenerNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->addDefaultsIfNotSet()
            ->children()
                ->integerNode('priority')
                    ->max(7)
                    ->defaultValue(4)
                ->end()
                ->append($this->matcherNode('matcher'))
            ->end()
        ;
    }

    private function matcherNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->beforeNormalization()
                ->ifString()
                ->then(function (string $config): array {
                    return ['path' => $config];
                })
            ->end()
            ->children()
                ->scalarNode('path')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('ips')
                    ->scalarPrototype()->cannotBeEmpty()->end()
                ->end()
                ->arrayNode('methods')
                    ->scalarPrototype()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;
    }
}
