<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\DependencyInjection\Compiler;

use Camelot\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use Camelot\CanonicalUrlBundle\EventListener\ExceptionListener;
use Camelot\CanonicalUrlBundle\EventListener\RequestListener;
use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPassTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testAddConfigArgumentToServiceDefinitions(array $config): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $container->setParameter('camelot_canonical_url.config', $config);

        $definitions = [
            CanonicalUrlGenerator::class => new Definition(CanonicalUrlGenerator::class),
            ExceptionListener::class => new Definition(ExceptionListener::class),
            RequestListener::class => new Definition(RequestListener::class),
        ];

        $container->addDefinitions($definitions);

        $compilerPass = new CompilerPass();

        $compilerPass->process($container);

        foreach (array_keys($definitions) as $id) {
            static::assertSame($config, $container->getDefinition($id)->getArgument(0));
        }
    }
}
