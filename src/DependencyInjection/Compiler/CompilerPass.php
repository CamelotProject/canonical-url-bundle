<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundleConfig = $container->getParameter('camelot_canonical_url.config');

        $definition = $container->getDefinition('camelot_canonical_url.url_generator');
        $definition->addArgument($bundleConfig);

        $definition = $container->getDefinition('camelot_canonical_url.request_listener');
        $definition->addArgument($bundleConfig);

        $definition = $container->getDefinition('camelot_canonical_url.exception_listener');
        $definition->addArgument($bundleConfig);
    }
}
