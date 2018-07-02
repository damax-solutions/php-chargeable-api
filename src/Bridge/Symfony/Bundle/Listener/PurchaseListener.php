<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle\Listener;

use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Processor;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

class PurchaseListener
{
    private $requestMatcher;
    private $processor;

    public function __construct(RequestMatcherInterface $requestMatcher, Processor $processor)
    {
        $this->requestMatcher = $requestMatcher;
        $this->processor = $processor;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$this->requestMatcher->matches($request)) {
            return;
        }

        try {
            $this->processor->purchase($request);
        } catch (InsufficientFunds $e) {
            $event->setResponse(Response::create('', Response::HTTP_PAYMENT_REQUIRED));
        } catch (Throwable $e) {
            throw new ServiceUnavailableHttpException(null, null, $e);
        }
    }
}
