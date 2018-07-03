<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Security;

use Damax\ChargeableApi\Bridge\Symfony\Security\TokenIdentityFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TokenIdentityFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_identity()
    {
        $storage = new TokenStorage();
        $factory = new TokenIdentityFactory($storage);

        $storage->setToken(new UsernamePasswordToken('john.doe', 'qwerty', 'main'));

        $this->assertEquals('john.doe', (string) $factory->create());
    }
}
