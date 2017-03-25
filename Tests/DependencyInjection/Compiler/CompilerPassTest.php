<?php

namespace Palmtree\CanonicalUrlBundle\Tests\DependencyInjection\Compiler;

use Palmtree\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use Palmtree\CanonicalUrlBundle\EventListener\KernelEventListener;
use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPassTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testAddConfigArgumentToServiceDefinitions(array $config)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $container->setParameter('palmtree.canonical_url.config', $config);

        $definitions = [
            'palmtree_canonical_url.url_generator'         => new Definition(CanonicalUrlGenerator::class),
            'palmtree_canonical_url.kernel_event_listener' => new Definition(KernelEventListener::class),
        ];

        $container->addDefinitions($definitions);

        $compilerPass = new CompilerPass();

        $compilerPass->process($container);

        foreach ($definitions as $id => $definition) {
            $this->assertEquals($config, $container->getDefinition($id)->getArgument(0));
        }
    }

    public function configProvider()
    {
        return array(
            'config' => array(
                array(
                    'site_url'       => 'https://example.org',
                    'redirect'       => true,
                    'redirect_code'  => 302,
                    'trailing_slash' => false,
                )
            )
        );
    }
}
