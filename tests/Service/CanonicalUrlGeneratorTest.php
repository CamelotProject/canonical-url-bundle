<?php

namespace Palmtree\CanonicalUrlBundle\Tests\Service;

use Palmtree\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;

class CanonicalUrlGeneratorTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testGenerateUrl(array $config)
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('foo');

        $this->assertEquals('https://example.org/foo', $url);
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testGenerateUrlWithStringParams(array $config)
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('foo', 'key1=val1&key2=val2');

        $this->assertEquals('https://example.org/foo?key1=val1&key2=val2', $url);
    }

    /**
     * @dataProvider configProvider
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @param array $config
     */
    public function testNonExistentRouteThrowsException(array $config)
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $urlGenerator->generate('bar');
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testEmptyRouteReturnsEmptyString(array $config)
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);

        $url = $urlGenerator->generate('');

        $this->assertSame('', $url);
    }
}
