<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Store;

use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Store\Receipt;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_receipt()
    {
        $identity = new UserIdentity('john.doe');
        $product = new Product('API', 10);
        $dateTime = new DateTimeImmutable();
        $receipt = new Receipt($identity, $product, $dateTime);

        $this->assertSame($identity, $receipt->identity());
        $this->assertSame($product, $receipt->product());
        $this->assertEquals(10, $receipt->price()->toInteger());
        $this->assertSame($dateTime, $receipt->date());
    }
}
