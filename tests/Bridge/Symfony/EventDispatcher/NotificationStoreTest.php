<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\EventDispatcher;

use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseCompleted;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseRejected;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Events;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\NotificationStore;
use Damax\ChargeableApi\Identity\UserIdentity;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Store\Receipt;
use Damax\ChargeableApi\Store\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseCompleted
 * @covers \Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseRejected
 * @covers \Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\NotificationStore
 */
class NotificationStoreTest extends TestCase
{
    /**
     * @var Store|MockObject
     */
    private $decorated;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var NotificationStore
     */
    private $store;

    protected function setUp()
    {
        $this->decorated = $this->createMock(Store::class);
        $this->dispatcher = new EventDispatcher();
        $this->store = new NotificationStore($this->decorated, $this->dispatcher);
    }

    /**
     * @test
     */
    public function it_emits_finished_purchase_event()
    {
        $identity = new UserIdentity('john.doe');
        $product = new Product('service', 10);

        $this->decorated
            ->expects($this->once())
            ->method('purchase')
            ->with($this->identicalTo($identity), $this->identicalTo($product))
            ->willReturn($receipt = new Receipt($identity, $product))
        ;

        /** @var PurchaseCompleted $finishedEvent */
        $finishedEvent = null;

        $this->dispatcher->addListener(Events::PURCHASE_FINISHED, function (PurchaseCompleted $event) use (&$finishedEvent) {
            $finishedEvent = $event;
        });
        $this->dispatcher->addListener(Events::PURCHASE_REJECTED, function () {
            $this->fail('Purchase rejected listener must not be called.');
        });

        $this->assertSame($receipt, $this->store->purchase($identity, $product));

        $this->assertInstanceOf(PurchaseCompleted::class, $finishedEvent);
        $this->assertSame($receipt, $finishedEvent->receipt());
        $this->assertSame($identity, $finishedEvent->identity());
        $this->assertSame($product, $finishedEvent->product());
    }

    /**
     * @test
     */
    public function it_emits_rejected_purchase_event()
    {
        $identity = new UserIdentity('john.doe');
        $product = new Product('service', 10);

        $this->decorated
            ->expects($this->once())
            ->method('purchase')
            ->with($this->identicalTo($identity), $this->identicalTo($product))
            ->willThrowException(InsufficientFunds::notEnough(10))
        ;

        /** @var PurchaseRejected $rejectedEvent */
        $rejectedEvent = null;

        $this->dispatcher->addListener(Events::PURCHASE_FINISHED, function () {
            $this->fail('Purchase finished listener must not be called.');
        });
        $this->dispatcher->addListener(Events::PURCHASE_REJECTED, function (PurchaseRejected $event) use (&$rejectedEvent) {
            $rejectedEvent = $event;
        });

        try {
            $this->store->purchase($identity, $product);
        } catch (InsufficientFunds $e) {
            $this->assertInstanceOf(PurchaseRejected::class, $rejectedEvent);
            $this->assertSame($identity, $rejectedEvent->identity());
            $this->assertSame($product, $rejectedEvent->product());

            return;
        }

        $this->fail('Insufficient credit exception must be thrown.');
    }
}
