<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Product;

use Damax\ChargeableApi\Product\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_product()
    {
        $product = new Product('service', 10);

        $this->assertEquals('service', $product->name());
        $this->assertSame(10, $product->price()->toInteger());
    }
}
