<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\Twig\Extension;

use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Camelot\CanonicalUrlBundle\Twig\Extension\CanonicalLinkExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function dirname;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\Twig\Extension\CanonicalLinkExtension
 */
final class CanonicalLinkExtensionTest extends AbstractTest
{
    public function testGenerateUrlMethod(): void
    {
        $extension = $this->getExtension();
        $url = $extension->generateUrl('foo');

        static::assertSame('https://example.org/foo', $url);
    }

    public function testGenerateUrlMethodWithNoRouteDefaultsToCurrentRequest(): void
    {
        $extension = $this->getExtension();
        $url = $extension->generateUrl();

        static::assertSame('https://example.org/foo', $url);
    }

    public function testRenderLinkTag(): void
    {
        $extension = $this->getExtension();
        $loader = new FilesystemLoader();
        $loader->setPaths(dirname(__DIR__, 3) . '/src/Resources/views', 'CamelotCanonicalUrl');
        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false,
            'autoescape' => 'html',
        ]);
        $twig->addExtension($extension);
        $html = $extension->renderLinkTag($twig, 'https://example.org');
        $html = trim($html);

        static::assertSame('<link rel="canonical" href="https://example.org">', $html);
    }

    protected function getExtension(): CanonicalLinkExtension
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), 'https://example.org');
        $requestStack = new RequestStack();
        $requestStack->push($this->getFooRequest());

        return new CanonicalLinkExtension($urlGenerator, $requestStack);
    }
}
