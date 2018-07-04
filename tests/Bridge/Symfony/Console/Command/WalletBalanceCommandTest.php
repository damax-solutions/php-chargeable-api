<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Console\Command;

use Damax\ChargeableApi\Bridge\Symfony\Console\Command\WalletBalanceCommand;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Symfony\Component\Console\Command\Command;

/**
 * @covers \Damax\ChargeableApi\Bridge\Symfony\Console\Command\WalletCommand
 * @covers \Damax\ChargeableApi\Bridge\Symfony\Console\Command\WalletBalanceCommand
 */
class WalletBalanceCommandTest extends WalletCommandTestCase
{
    protected function createCommand(): Command
    {
        return new WalletBalanceCommand(new InMemoryWalletFactory(['john.doe' => 15]));
    }

    /**
     * @test
     */
    public function it_retrieves_balance()
    {
        $code = $this->tester->execute([
            'command' => 'damax:chargeable-api:wallet:balance',
            'identity' => 'john.doe',
        ]);

        $this->assertSame(0, $code);
        $this->assertEquals('[OK] Balance: 15', trim($this->tester->getDisplay()));
    }
}
