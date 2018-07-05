<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WalletBalanceCommand extends WalletCommand
{
    protected static $defaultName = 'damax:chargeable-api:wallet:balance';

    protected function configure()
    {
        $this
            ->setDescription('Get wallet balance.')
            ->addArgument('identity', InputArgument::REQUIRED, 'Identity.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identity = $this->identityFactory->create();

        $balance = $this->walletFactory->create($identity)->balance();

        (new SymfonyStyle($input, $output))->success(sprintf('Balance: %d', $balance->toInteger()));
    }
}
