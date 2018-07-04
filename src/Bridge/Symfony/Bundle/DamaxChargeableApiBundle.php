<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony\Bundle;

use Damax\ChargeableApi\Bridge\Symfony\Bundle\DependencyInjection\Compiler\ProductResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DamaxChargeableApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProductResolverPass());
    }
}
