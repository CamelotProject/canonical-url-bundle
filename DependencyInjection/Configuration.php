<?php

namespace Palmtree\CanonicalUrlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('palmtree_canonical_url');

        // @formatter:off
        $rootNode
            ->children()
                ->scalarNode('site_url')->defaultValue('')->end()
                ->integerNode('redirect_code')->defaultValue(301)->min(300)->max(399)->end()
            ->end()
        ;
        // @formatter:on

        return $treeBuilder;
    }
}
