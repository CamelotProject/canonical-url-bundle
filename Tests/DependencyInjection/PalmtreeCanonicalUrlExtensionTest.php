<?php

namespace Palmtree\CanonicalUrlBundle\Tests\DependencyInjection;

use Palmtree\CanonicalUrlBundle\DependencyInjection\PalmtreeCanonicalUrlExtension;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PalmtreeCanonicalUrlExtensionTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testSetConfigurationParameter(array $config)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new PalmtreeCanonicalUrlExtension();

        $extension->load(array($config), $container);

        $this->assertEquals($config, $container->getParameter('palmtree.canonical_url.config'));
    }

    public function testGetAlias()
    {
        $extension = new PalmtreeCanonicalUrlExtension();

        $this->assertEquals(
            'palmtree_canonical_url',
            $extension->getAlias(),
            'getAlias returns "palmtree_canonical_url"'
        );
    }
}
