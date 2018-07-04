<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Bridge\Symfony\Console\Command;

use Damax\ChargeableApi\Bridge\Symfony\Console\Command\WalletWithdrawCommand;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Symfony\Component\Console\Command\Command;

class WalletWithdrawCommandTest extends WalletCommandTestCase
{
    protected function createCommand(): Command
    {
        return new WalletWithdrawCommand(new InMemoryWalletFactory(['john.doe' => 15]));
    }

    /**
     * @test
     */
    public function it_withdraws_credit()
    {
        $code = $this->tester->execute([
            'command' => 'damax:chargeable-api:wallet:withdraw',
            'identity' => 'john.doe',
            'credit' => 10,
        ]);

        $this->assertSame(0, $code);
        $this->assertEquals('[OK] Balance: 5', trim($this->tester->getDisplay()));
    }
}
