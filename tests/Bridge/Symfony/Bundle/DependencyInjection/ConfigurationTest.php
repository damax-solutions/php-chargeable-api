<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Bundle\DependencyInject;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_processes_empty_config()
    {
        $config = [];

        $this->assertProcessedConfigurationEquals([$config], [
            'wallet' => [
                'type' => 'fixed',
                'accounts' => [],
            ],
            'identity' => [
                'type' => 'security',
            ],
            'product' => [
                'default' => [
                    'name' => 'API',
                    'price' => 1,
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_processes_simplified_wallet_config()
    {
        $config = [
            'wallet' => [
                'john.doe' => 15,
                'jane.doe' => 25,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'wallet' => [
                'type' => 'fixed',
                'accounts' => [
                    'john.doe' => 15,
                    'jane.doe' => 25,
                ],
            ],
        ], 'wallet');
    }

    /**
     * @test
     */
    public function it_requires_factory_service_id_for_wallet()
    {
        $config = [
            'wallet' => [
                'type' => 'service',
            ],
        ];

        $this->assertPartialConfigurationIsInvalid([$config], 'wallet', 'Service id must be specified.');
    }

    /**
     * @test
     */
    public function it_configures_wallet_factory_service_id()
    {
        $config = [
            'wallet' => [
                'type' => 'service',
                'factory_service_id' => 'custom_wallet_factory',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'wallet' => [
                'type' => 'service',
                'factory_service_id' => 'custom_wallet_factory',
                'accounts' => [],
            ],
        ], 'wallet');
    }

    /**
     * @test
     */
    public function it_requires_necessary_config_for_redis_wallet()
    {
        $config = [
            'wallet' => [
                'type' => 'redis',
            ],
        ];

        $this->assertPartialConfigurationIsInvalid([$config], 'wallet', 'Wallet key and Redis client must be specified.');
    }

    /**
     * @test
     */
    public function it_configures_redis_wallet()
    {
        $config = [
            'wallet' => [
                'type' => 'redis',
                'wallet_key' => 'wallet',
                'redis_client_id' => 'snc_redis.default',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'wallet' => [
                'type' => 'redis',
                'wallet_key' => 'wallet',
                'redis_client_id' => 'snc_redis.default',
                'accounts' => [],
            ],
        ], 'wallet');
    }

    /**
     * @test
     */
    public function it_processes_simplified_identity_config()
    {
        $config = [
            'identity' => 'john.doe',
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'identity' => [
                'type' => 'fixed',
                'identity' => 'john.doe',
            ],
        ], 'identity');
    }

    /**
     * @test
     */
    public function it_requires_identity_value_for_fixed_identity()
    {
        $config = [
            'identity' => [
                'type' => 'fixed',
            ],
        ];

        $this->assertPartialConfigurationIsInvalid([$config], 'identity', 'Identity must be specified.');
    }

    /**
     * @test
     */
    public function it_configures_fixed_identity()
    {
        $config = [
            'identity' => [
                'type' => 'fixed',
                'identity' => 'john.doe',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'identity' => [
                'type' => 'fixed',
                'identity' => 'john.doe',
            ],
        ], 'identity');
    }

    /**
     * @test
     */
    public function it_requires_factory_service_id_for_identity()
    {
        $config = [
            'identity' => [
                'type' => 'service',
            ],
        ];

        $this->assertPartialConfigurationIsInvalid([$config], 'identity', 'Service id must be specified.');
    }

    /**
     * @test
     */
    public function it_configures_identity_factory_service_id()
    {
        $config = [
            'identity' => [
                'type' => 'service',
                'factory_service_id' => 'custom_identity_factory',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'identity' => [
                'type' => 'service',
                'factory_service_id' => 'custom_identity_factory',
            ],
        ], 'identity');
    }

    /**
     * @test
     */
    public function it_processes_simplified_product_name_config()
    {
        $config = [
            'product' => 'Service',
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'product' => [
                'default' => [
                    'name' => 'Service',
                    'price' => 1,
                ],
            ],
        ], 'product');
    }

    /**
     * @test
     */
    public function it_processes_simplified_product_price_config()
    {
        $config = [
            'product' => 10,
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'product' => [
                'default' => [
                    'name' => 'API',
                    'price' => 10,
                ],
            ],
        ], 'product');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
