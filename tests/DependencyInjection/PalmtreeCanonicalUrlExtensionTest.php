<?php

namespace Palmtree\CanonicalUrlBundle\Tests\DependencyInjection;

use Palmtree\CanonicalUrlBundle\DependencyInjection\PalmtreeCanonicalUrlExtension;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PalmtreeCanonicalUrlExtensionTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testSetConfigurationParameter(array $config)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new PalmtreeCanonicalUrlExtension();

        $extension->load([$config], $container);

        $this->assertSame($config, $container->getParameter('palmtree_canonical_url.config'));
    }

    /**
     *
     */
    public function testGetAlias()
    {
        $extension = new PalmtreeCanonicalUrlExtension();

        $this->assertEquals('palmtree_canonical_url', $extension->getAlias());
    }
}
