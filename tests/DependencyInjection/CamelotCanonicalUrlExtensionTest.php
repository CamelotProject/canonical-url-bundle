<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\DependencyInjection;

use Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CamelotCanonicalUrlExtensionTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testSetConfigurationParameter(array $config): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new CamelotCanonicalUrlExtension();

        $extension->load([$config], $container);

        static::assertSame($config, $container->getParameter('camelot_canonical_url.config'));
    }

    public function testGetAlias(): void
    {
        $extension = new CamelotCanonicalUrlExtension();

        static::assertEquals('camelot_canonical_url', $extension->getAlias());
    }
}
