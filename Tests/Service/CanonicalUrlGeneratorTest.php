<?php

namespace Palmtree\CanonicalUrlBundle\Tests\Service;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
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
}
