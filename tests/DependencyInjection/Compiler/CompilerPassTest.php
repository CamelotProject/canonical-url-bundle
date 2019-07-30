<?php

namespace Palmtree\CanonicalUrlBundle\Tests\DependencyInjection\Compiler;

use Palmtree\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use Palmtree\CanonicalUrlBundle\EventListener\ExceptionListener;
use Palmtree\CanonicalUrlBundle\EventListener\RequestListener;
use Palmtree\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPassTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testAddConfigArgumentToServiceDefinitions(array $config)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $container->setParameter('palmtree_canonical_url.config', $config);

        $definitions = [
            'palmtree_canonical_url.url_generator'      => new Definition(CanonicalUrlGenerator::class),
            'palmtree_canonical_url.exception_listener' => new Definition(ExceptionListener::class),
            'palmtree_canonical_url.request_listener'   => new Definition(RequestListener::class),
        ];

        $container->addDefinitions($definitions);

        $compilerPass = new CompilerPass();

        $compilerPass->process($container);

        foreach ($definitions as $id => $definition) {
            $this->assertSame($config, $container->getDefinition($id)->getArgument(0));
        }
    }
}
