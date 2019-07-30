<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle;

use Camelot\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CamelotCanonicalUrlBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompilerPass());
    }
}
