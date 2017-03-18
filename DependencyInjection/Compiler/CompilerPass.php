<?php

namespace Palmtree\CanonicalUrlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('palmtree.canonical_url');
        $definition->addArgument($container->getParameter('palmtree.canonical_url.site_url'));
        $definition->addArgument($container->getParameter('palmtree.canonical_url.redirect_code'));
    }
}
