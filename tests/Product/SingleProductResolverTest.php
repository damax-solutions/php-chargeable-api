<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Product;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\Product\SingleProductResolver;
use PHPUnit\Framework\TestCase;

class SingleProductResolverTest extends TestCase
{
    /**
     * @test
     */
    public function it_resolves_product()
    {
        $resolver = new SingleProductResolver('service', 10);

        $this->assertEquals('service', $resolver->resolve(null)->name());
        $this->assertEquals(Credit::fromInteger(10), $resolver->resolve(null)->price());
    }
}
