<?php

namespace Palmtree\CanonicalUrlBundle\Tests\Twig\Extension;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Palmtree\CanonicalUrlBundle\Twig\Extension\CanonicalLinkExtension;
use Symfony\Component\HttpFoundation\RequestStack;

class CanonicalLinkExtensionTest extends AbstractTest
{
    /** @dataProvider configProvider */
    public function testGenerateUrlMethod(array $config)
    {
        $extension = $this->getExtension($config);

        $url = $extension->generateUrl('foo');

        $this->assertEquals('https://example.org/foo', $url);
    }

    /** @dataProvider configProvider */
    public function testRenderLinkTag($config)
    {
        $extension = $this->getExtension($config);

        $loader = new \Twig_Loader_Filesystem();
        $loader->setPaths(__DIR__ . '/../../../Resources/views', 'PalmtreeCanonicalUrl');

        $twig = new \Twig_Environment($loader, array(
            'debug'      => true,
            'cache'      => false,
            'autoescape' => 'html',
        ));

        $twig->addExtension($extension);

        $html = $extension->renderLinkTag($twig, 'https://example.org');
        $html = trim($html);

        $this->assertEquals('<link rel="canonical" href="https://example.org">', $html);
    }

    protected function getExtension($config)
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);
        $requestStack = new RequestStack();

        $requestStack->push($this->getFooRequest());

        $extension = new CanonicalLinkExtension($urlGenerator, $requestStack);

        return $extension;
    }
}
