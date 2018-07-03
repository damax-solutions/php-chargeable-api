<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\EventDispatcher;

use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseFinished;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseRejected;
use Damax\ChargeableApi\Identity\Identity;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Store\Receipt;
use Damax\ChargeableApi\Store\Store;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class NotificationStore implements Store
{
    private $store;
    private $dispatcher;

    public function __construct(Store $store, EventDispatcherInterface $dispatcher)
    {
        $this->store = $store;
        $this->dispatcher = $dispatcher;
    }

    public function purchase(Identity $identity, Product $product): Receipt
    {
        try {
            $receipt = $this->store->purchase($identity, $product);
        } catch (InsufficientFunds $e) {
            $this->dispatcher->dispatch(Events::PURCHASE_REJECTED, new PurchaseRejected($identity, $product));

            throw $e;
        }

        $this->dispatcher->dispatch(Events::PURCHASE_FINISHED, new PurchaseFinished($receipt));

        return $receipt;
    }
}
