<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Identity;

class FixedIdentityFactory implements IdentityFactory
{
    private $identity;

    public function __construct(string $identity)
    {
        $this->identity = $identity;
    }

    public function create(): Identity
    {
        return new UserIdentity($this->identity);
    }
}
