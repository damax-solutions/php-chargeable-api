<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Identity;

use Damax\ChargeableApi\Identity\UserIdentity;
use PHPUnit\Framework\TestCase;

class UserIdentityTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_identity()
    {
        $this->assertEquals('john.doe', (string) new UserIdentity('john.doe'));
    }
}
