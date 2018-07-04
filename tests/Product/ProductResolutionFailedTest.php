<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Product;

use Damax\ChargeableApi\Product\ProductResolutionFailed;
use PHPUnit\Framework\TestCase;

class ProductResolutionFailedTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception()
    {
        $this->assertEquals('Unable to resolve product.', ProductResolutionFailed::unresolved()->getMessage());
    }
}
