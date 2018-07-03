<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\EventDispatcher;

final class Events
{
    public const PURCHASE_FINISHED = 'damax.chargeable-api.purchase_finished';
    public const PURCHASE_REJECTED = 'damax.chargeable-api.purchase_rejected';
}
