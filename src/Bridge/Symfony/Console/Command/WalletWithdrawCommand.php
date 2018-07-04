<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Console\Command;

use Damax\ChargeableApi\Credit;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WalletWithdrawCommand extends WalletCommand
{
    protected static $defaultName = 'damax:chargeable-api:wallet:withdraw';

    protected function configure()
    {
        $this
            ->setDescription('Withdraw credit.')
            ->addArgument('identity', InputArgument::REQUIRED, 'Identity.')
            ->addArgument('credit', InputArgument::REQUIRED, 'Credit.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identity = $this->identityFactory->create();

        $credit = Credit::fromInteger((int) $input->getArgument('credit'));

        $wallet = $this->walletFactory->create($identity);
        $wallet->withdraw($credit);

        (new SymfonyStyle($input, $output))->success(sprintf('Balance: %d', $wallet->balance()->toInteger()));
    }
}
