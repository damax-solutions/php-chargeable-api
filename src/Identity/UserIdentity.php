<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Identity;

final class UserIdentity implements Identity
{
    private $identity;

    public function __construct(string $identity)
    {
        $this->identity = $identity;
    }

    public function __toString(): string
    {
        return $this->identity;
    }
}
