<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Identity;

use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use PHPUnit\Framework\TestCase;

class FixedIdentityFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_identity()
    {
        $identity = (new FixedIdentityFactory('john.doe'))->create();

        $this->assertEquals('john.doe', (string) $identity);
    }
}
