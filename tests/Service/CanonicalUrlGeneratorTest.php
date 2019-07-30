<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\Service;

use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;

class CanonicalUrlGeneratorTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testGenerateUrl(array $config): void
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('foo');

        static::assertEquals('https://example.org/foo', $url);
    }

    /**
     * @dataProvider configProvider
     */
    public function testGenerateUrlWithStringParams(array $config): void
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('foo', 'key1=val1&key2=val2');

        static::assertEquals('https://example.org/foo?key1=val1&key2=val2', $url);
    }

    /**
     * @dataProvider configProvider
     */
    public function testNonExistentRouteThrowsException(array $config): void
    {
        $this->expectException(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);

        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $urlGenerator->generate('bar');
    }

    /**
     * @dataProvider configProvider
     */
    public function testEmptyRouteReturnsEmptyString(array $config): void
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('');

        static::assertSame('', $url);
    }
}
