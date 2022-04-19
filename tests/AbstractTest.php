<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

abstract class AbstractTest extends TestCase
{
    protected function getRouter(?RouteCollection $routeCollection = null): Router
    {
        if (!$routeCollection) {
            $routeCollection = $this->getFooRouteCollection();
        }

        $loader = $this->createMock(LoaderInterface::class);
        $loader
            ->expects(static::any())
            ->method('load')
            ->willReturn($routeCollection)
        ;
        $context = new RequestContext();
        $context->setScheme('https')->setHost('example.org');

        /** @var LoaderInterface $loader */
        $router = new Router($loader, '');
        $router->setContext($context);

        return $router;
    }

    protected function getFooRouteCollection(): RouteCollection
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('foo', new Route('/foo'));
        $routeCollection->add('foo/{noop}', new Route('/foo/{noop}'));
        $routeCollection->add('baz', new Route('/baz/'));
        $routeCollection->setHost('example.org');

        return $routeCollection;
    }

    protected function getMockRequest(string $path, bool $secure = true, bool $trailingSlash = true): Request
    {
        $scheme = ($secure) ? 'https' : 'http';
        $uri = "{$scheme}://example.org/{$path}";

        if ($trailingSlash) {
            $uri .= '/';
        }

        $request = Request::create($uri);
        $request->attributes->set('_route', $path);

        return $request;
    }

    protected function getFooRequest(bool $secure = true, bool $trailingSlash = true): Request
    {
        return $this->getMockRequest('foo', $secure, $trailingSlash);
    }

    protected function getBazRequest(bool $secure = true, bool $trailingSlash = true): Request
    {
        return $this->getMockRequest('baz', $secure, $trailingSlash);
    }
}
