<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function is_string;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('camelot_canonical_url');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('site_url')
                    ->isRequired()->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($value) { return !is_string($value); })
                        ->thenInvalid('site_url must be a string')
                    ->end()
                ->end()
                ->booleanNode('redirect')->defaultTrue()->treatNullLike(true)->end()
                ->integerNode('redirect_code')->defaultValue(301)->treatNullLike(301)->min(300)->max(399)->end()
                ->booleanNode('trailing_slash')->defaultFalse()->treatNullLike(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
