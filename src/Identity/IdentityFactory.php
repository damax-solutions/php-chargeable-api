<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Identity;

interface IdentityFactory
{
    public function create(): Identity;
}
