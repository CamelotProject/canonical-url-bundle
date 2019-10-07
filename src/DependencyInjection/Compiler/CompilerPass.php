<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\DependencyInjection\Compiler;

use Camelot\CanonicalUrlBundle\EventListener\ExceptionListener;
use Camelot\CanonicalUrlBundle\EventListener\RequestListener;
use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundleConfig = $container->getParameter('camelot_canonical_url.config');

        $definition = $container->getDefinition(CanonicalUrlGenerator::class);
        $definition->addArgument($bundleConfig);

        $definition = $container->getDefinition(RequestListener::class);
        $definition->addArgument($bundleConfig);

        $definition = $container->getDefinition(ExceptionListener::class);
        $definition->addArgument($bundleConfig);
    }
}
