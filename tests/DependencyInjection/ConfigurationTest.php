<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\DependencyInjection;

use Camelot\CanonicalUrlBundle\DependencyInjection\Configuration;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\DependencyInjection\Configuration
 */
final class ConfigurationTest extends AbstractTest
{
    /**
     * Some basic tests to make sure the configuration is correctly processed in
     * the standard case.
     */
    public function testProcessSimpleCase(): void
    {
        $configs = [
            [
                'site_url' => 'https://example.org',
                'redirect' => true,
                'redirect_code' => 302,
                'trailing_slash' => true,
            ],
            [
                'trailing_slash' => false,
            ],
        ];

        $config = $this->process($configs);

        static::assertArrayHasKey('site_url', $config);
        static::assertTrue($config['redirect']);
        static::assertFalse($config['trailing_slash']);
    }

    public function testInvalidSiteUrl(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configs = [['site_url' => false]];
        $this->process($configs);
    }

    public function testInvalidRedirectCode(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configs = [['redirect_code' => 404]];
        $this->process($configs);
    }

    public function testInvalidRedirect(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configs = [['redirect' => 9]];
        $this->process($configs);
    }

    /** Processes an array of configurations and returns a compiled version. */
    protected function process(array $configs): array
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
