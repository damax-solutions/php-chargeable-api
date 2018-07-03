<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Store;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\Identity\UserIdentity;
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
        $amount = Credit::fromInteger(10);
        $dateTime = new DateTimeImmutable();
        $receipt = new Receipt($identity, $amount, $dateTime);

        $this->assertSame($identity, $receipt->identity());
        $this->assertSame($amount, $receipt->amount());
        $this->assertSame($dateTime, $receipt->date());
    }
}
