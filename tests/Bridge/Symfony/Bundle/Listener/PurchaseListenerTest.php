<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Bundle\Listener;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\Listener\PurchaseListener;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Processor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PurchaseListenerTest extends TestCase
{
    /**
     * @var RequestMatcherInterface|MockObject
     */
    private $matcher;

    /**
     * @var Processor|MockObject
     */
    private $processor;

    /**
     * @var PurchaseListener
     */
    private $listener;

    protected function setUp()
    {
        $this->matcher = $this->createMock(RequestMatcherInterface::class);
        $this->processor = $this->createMock(Processor::class);
        $this->listener = new PurchaseListener($this->matcher, $this->processor);
    }

    /**
     * @test
     */
    public function it_skips_non_master_request()
    {
        $event = $this->createEvent(HttpKernelInterface::SUB_REQUEST);

        $this->matcher
            ->expects($this->never())
            ->method('matches')
        ;
        $this->processor
            ->expects($this->never())
            ->method('processRequest')
        ;

        $this->listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function it_skips_for_unmatched_request()
    {
        $event = $this->createEvent();

        $this->matcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->identicalTo($event->getRequest()))
        ;
        $this->processor
            ->expects($this->never())
            ->method('processRequest')
        ;

        $this->listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function it_processes_request()
    {
        $event = $this->createEvent();

        $this->matcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->identicalTo($event->getRequest()))
            ->willReturn(true)
        ;
        $this->processor
            ->expects($this->once())
            ->method('processRequest')
            ->with($this->identicalTo($event->getRequest()))
        ;

        $this->listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function it_denies_service_on_error()
    {
        $this->expectException(ServiceUnavailableHttpException::class);

        $event = $this->createEvent();

        $this->matcher
            ->method('matches')
            ->willReturn(true)
        ;
        $this->processor
            ->expects($this->once())
            ->method('processRequest')
            ->willThrowException(new RuntimeException('Application error.'))
        ;

        $this->listener->onKernelRequest($event);
    }

    /**
     * @test
     */
    public function it_requires_payment_on_insufficient_funds()
    {
        $event = $this->createEvent();

        $this->matcher
            ->method('matches')
            ->willReturn(true)
        ;
        $this->processor
            ->expects($this->once())
            ->method('processRequest')
            ->willThrowException(InsufficientFunds::notEnough(10))
        ;

        $this->listener->onKernelRequest($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_PAYMENT_REQUIRED, $response->getStatusCode());
    }

    private function createEvent(int $type = HttpKernelInterface::MASTER_REQUEST): GetResponseEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        return new GetResponseEvent($kernel, new Request(), $type);
    }
}
