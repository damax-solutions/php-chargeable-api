<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Product;

use Damax\ChargeableApi\Product\FixedProductResolver;
use PHPUnit\Framework\TestCase;
use stdClass;

class FixedProductResolverTest extends TestCase
{
    /**
     * @test
     */
    public function it_resolves_product()
    {
        $request = new stdClass();

        $resolver = new FixedProductResolver('service', 10);

        $this->assertEquals('service', $resolver->resolve($request)->name());
        $this->assertEquals(10, $resolver->resolve($request)->price()->toInteger());
    }
}
