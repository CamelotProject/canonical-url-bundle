<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\Routing\Generator;

use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator
 */
final class CanonicalUrlGeneratorTest extends AbstractTest
{
    public function testGenerateUrl(): void
    {
        $url = $this->getUrlGenerator()->generate('foo');

        static::assertSame('https://example.org/foo', $url);
    }

    public function testGenerateUrlWithStringParams(): void
    {
        $url = $this->getUrlGenerator()->generate('foo', 'key1=val1&key2=val2');

        static::assertSame('https://example.org/foo?key1=val1&key2=val2', $url);
    }

    public function testNonExistentRouteThrowsException(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->getUrlGenerator()->generate('bar');
    }

    public function testEmptyRouteThrowsException(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $url = $this->getUrlGenerator()->generate('');

        static::assertSame('', $url);
    }

    private function getUrlGenerator(): CanonicalUrlGenerator
    {
        return new CanonicalUrlGenerator($this->getRouter(), 'https://example.org');
    }
}
