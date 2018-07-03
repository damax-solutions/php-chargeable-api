<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Store;

use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Product\Resolver;
use Damax\ChargeableApi\Store\Receipt;
use Damax\ChargeableApi\Store\Store;
use Damax\ChargeableApi\Store\StoreProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class StoreProcessorTest extends TestCase
{
    /**
     * @var Store|MockObject
     */
    private $store;

    /**
     * @var IdentityFactory|MockObject
     */
    private $identityFactory;

    /**
     * @var Resolver|MockObject
     */
    private $productResolver;

    /**
     * @var StoreProcessor
     */
    private $processor;

    protected function setUp()
    {
        $this->store = $this->createMock(Store::class);
        $this->identityFactory = $this->createMock(IdentityFactory::class);
        $this->productResolver = $this->createMock(Resolver::class);
        $this->processor = new StoreProcessor($this->store, $this->identityFactory, $this->productResolver);
    }

    /**
     * @test
     */
    public function it_processes_payment()
    {
        $request = new stdClass();

        $product = new Product('service', 10);
        $identity = new UserIdentity('john.doe');

        $this->identityFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($identity)
        ;
        $this->productResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($request))
            ->willReturn($product)
        ;
        $this->store
            ->expects($this->once())
            ->method('purchase')
            ->with($this->identicalTo($identity), $this->identicalTo($product))
            ->willReturn(new Receipt($identity, $product))
        ;

        $this->processor->processRequest($request);
    }
}
