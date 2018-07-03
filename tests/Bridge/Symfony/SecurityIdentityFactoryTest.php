<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony;

use Damax\ChargeableApi\Bridge\Symfony\SecurityIdentityFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityIdentityFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_identity()
    {
        $storage = new TokenStorage();
        $factory = new SecurityIdentityFactory($storage);

        $storage->setToken(new UsernamePasswordToken('john.doe', 'qwerty', 'main'));

        $this->assertEquals('john.doe', (string) $factory->create());
    }
}
