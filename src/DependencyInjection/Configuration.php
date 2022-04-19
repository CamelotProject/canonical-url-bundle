<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('camelot_canonical_url');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('redirect')->defaultTrue()->treatNullLike(true)->end()
                ->integerNode('redirect_code')->defaultValue(301)->treatNullLike(301)->min(300)->max(399)->end()
                ->booleanNode('trailing_slash')->defaultFalse()->treatNullLike(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
