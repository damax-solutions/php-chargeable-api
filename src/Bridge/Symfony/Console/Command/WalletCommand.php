<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Console\Command;

use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use Damax\ChargeableApi\Identity\IdentityFactory;
use Damax\ChargeableApi\Wallet\WalletFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class WalletCommand extends Command
{
    protected $walletFactory;

    /**
     * @var IdentityFactory
     */
    protected $identityFactory;

    public function __construct(WalletFactory $walletFactory)
    {
        parent::__construct();

        $this->walletFactory = $walletFactory;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->identityFactory = new FixedIdentityFactory($input->getArgument('identity') ?? '');
    }
}
