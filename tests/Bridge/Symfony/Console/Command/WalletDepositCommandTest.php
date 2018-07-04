<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Console\Command;

use Damax\ChargeableApi\Bridge\Symfony\Console\Command\WalletDepositCommand;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Symfony\Component\Console\Command\Command;

class WalletDepositCommandTest extends WalletCommandTestCase
{
    protected function createCommand(): Command
    {
        return new WalletDepositCommand(new InMemoryWalletFactory([]));
    }

    /**
     * @test
     */
    public function it_deposits_credit()
    {
        $code = $this->tester->execute([
            'command' => 'damax:chargeable-api:wallet:deposit',
            'identity' => 'john.doe',
            'credit' => 10,
        ]);

        $this->assertSame(0, $code);
        $this->assertEquals('[OK] Balance: 10', trim($this->tester->getDisplay()));
    }
}
