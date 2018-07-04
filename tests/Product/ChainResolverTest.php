<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Product;

use Damax\ChargeableApi\Product\ChainResolver;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Product\ProductResolutionFailed;
use Damax\ChargeableApi\Product\Resolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class ChainResolverTest extends TestCase
{
    /**
     * @var Resolver|MockObject
     */
    private $one;

    /**
     * @var Resolver|MockObject
     */
    private $two;

    /**
     * @var ChainResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new ChainResolver([
            $this->one = $this->createMock(Resolver::class),
            $this->two = $this->createMock(Resolver::class),
        ]);
    }

    /**
     * @test
     */
    public function it_resolves_from_first()
    {
        $request = new stdClass();
        $product = new Product('API', 10);

        $this->one
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($request))
            ->willReturn($product)
        ;
        $this->two
            ->expects($this->never())
            ->method('resolve')
        ;

        $this->assertSame($product, $this->resolver->resolve($request));
    }

    /**
     * @test
     */
    public function it_resolves_from_second()
    {
        $request = new stdClass();
        $product = new Product('API', 10);

        $this->one
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($request))
            ->willThrowException(ProductResolutionFailed::unresolved())
        ;
        $this->two
            ->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($request))
            ->willReturn($product)
        ;

        $this->assertSame($product, $this->resolver->resolve($request));
    }

    /**
     * @test
     */
    public function it_fails_to_resolve_a_product()
    {
        $this->one
            ->method('resolve')
            ->willThrowException(ProductResolutionFailed::unresolved())
        ;
        $this->two
            ->method('resolve')
            ->willThrowException(ProductResolutionFailed::unresolved())
        ;

        $this->expectException(ProductResolutionFailed::class);
        $this->expectExceptionMessage('Unable to resolve product.');

        $this->resolver->resolve(new stdClass());
    }
}
