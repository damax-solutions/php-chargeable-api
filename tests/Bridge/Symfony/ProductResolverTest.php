<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony;

use Damax\ChargeableApi\Bridge\Symfony\ProductResolver;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Product\ProductResolutionFailed;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class ProductResolverTest extends TestCase
{
    /**
     * @var ProductResolver
     */
    private $resolver;

    /**
     * @var RequestMatcherInterface|MockObject
     */
    private $matcherOne;

    /**
     * @var RequestMatcherInterface|MockObject
     */
    private $matcherTwo;

    protected function setUp()
    {
        $this->resolver = new ProductResolver();

        $this->matcherOne = $this->createMock(RequestMatcherInterface::class);
        $this->matcherTwo = $this->createMock(RequestMatcherInterface::class);

        $this->resolver->addProduct(new Product('One', 10), $this->matcherOne);
        $this->resolver->addProduct(new Product('Two', 10), $this->matcherTwo);
    }

    /**
     * @test
     */
    public function it_resolves_first_product()
    {
        $request = new Request();

        $this->matcherOne
            ->expects($this->once())
            ->method('matches')
            ->with($this->identicalTo($request))
            ->willReturn(true)
        ;
        $this->matcherTwo
            ->expects($this->never())
            ->method('matches')
        ;

        $this->assertEquals('One', $this->resolver->resolve($request)->name());
    }

    /**
     * @test
     */
    public function it_resolves_second_product()
    {
        $request = new Request();

        $this->matcherOne
            ->expects($this->once())
            ->method('matches')
            ->with($this->identicalTo($request))
            ->willReturn(false)
        ;
        $this->matcherTwo
            ->expects($this->once())
            ->method('matches')
            ->with($this->identicalTo($request))
            ->willReturn(true)
        ;

        $this->assertEquals('Two', $this->resolver->resolve($request)->name());
    }

    /**
     * @test
     */
    public function it_resolves_no_product()
    {
        $request = new Request();

        $this->expectException(ProductResolutionFailed::class);
        $this->expectExceptionMessage('Unable to resolve product.');

        $this->resolver->resolve($request);
    }
}
