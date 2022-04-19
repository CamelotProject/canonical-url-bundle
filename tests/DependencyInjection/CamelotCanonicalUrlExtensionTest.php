<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\DependencyInjection;

use Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension;
use Camelot\CanonicalUrlBundle\Tests\TestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension
 */
final class CamelotCanonicalUrlExtensionTest extends TestCase
{
    use TestTrait;

    public function configProvider(): array
    {
        return [
            'config' => [
                [
                    'site_url' => 'https://example.org',
                    'redirect' => true,
                    'redirect_code' => 302,
                    'trailing_slash' => false,
                ],
            ],
        ];
    }

    /** @dataProvider configProvider */
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

        static::assertSame('camelot_canonical_url', $extension->getAlias());
    }
}
