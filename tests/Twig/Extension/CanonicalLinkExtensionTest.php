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
    /** @dataProvider configProvider */
    public function testGenerateUrlMethod(array $config): void
    {
        $extension = $this->getExtension($config);
        $url = $extension->generateUrl('foo');

        static::assertSame('https://example.org/foo', $url);
    }

    /** @dataProvider configProvider */
    public function testGenerateUrlMethodWithNoRouteDefaultsToCurrentRequest(array $config): void
    {
        $extension = $this->getExtension($config);
        $url = $extension->generateUrl();

        static::assertSame('https://example.org/foo', $url);
    }

    /** @dataProvider configProvider */
    public function testRenderLinkTag(array $config): void
    {
        $extension = $this->getExtension($config);
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

    protected function getExtension(array $config): CanonicalLinkExtension
    {
        $urlGenerator = new CanonicalUrlGenerator($this->getRouter(), $config);
        $requestStack = new RequestStack();
        $requestStack->push($this->getFooRequest());

        return new CanonicalLinkExtension($urlGenerator, $requestStack);
    }
}
