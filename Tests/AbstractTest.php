<?php

namespace Palmtree\CanonicalUrlBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

abstract class AbstractTest extends TestCase
{
    /**
     * @param RouteCollection|null $routeCollection
     *
     * @return Router
     */
    protected function getRouter($routeCollection = null)
    {
        if (!$routeCollection) {
            $routeCollection = $this->getFooRouteCollection();
        }

        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('load')->willReturn($routeCollection);

        /** @var LoaderInterface $loader */
        $context = new RequestContext();
        $context->setScheme('https')->setHost('example.org');

        $router = new Router($loader, '');
        $router->setContext($context);

        return $router;
    }

    /**
     * @return RouteCollection
     */
    protected function getFooRouteCollection()
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('foo', new Route('/foo'));
        $routeCollection->add('baz', new Route('/baz/'));
        $routeCollection->setHost('example.org');

        return $routeCollection;
    }

    /**
     * @param bool $secure
     * @param bool $trailingSlash
     * @return Request
     */
    protected function getMockRequest($path, $secure = true, $trailingSlash = true)
    {
        $scheme = ($secure) ? 'https' : 'http';
        $uri    = "$scheme://example.org/$path";

        if ($trailingSlash) {
            $uri .= '/';
        }

        $request = Request::create($uri);
        $request->attributes->set('_route', $path);

        return $request;
    }

    /**
     * @param bool $secure
     * @param bool $trailingSlash
     * @return Request
     */
    protected function getFooRequest($secure = true, $trailingSlash = true)
    {
        return $this->getMockRequest('foo', $secure, $trailingSlash);
    }

    /**
     * @param bool $secure
     * @param bool $trailingSlash
     * @return Request
     */
    protected function getBazRequest($secure = true, $trailingSlash = true)
    {
        return $this->getMockRequest('baz', $secure, $trailingSlash);
    }

    /**
     * @return array
     */
    public function configProvider()
    {
        return [
            'config' => [
                [
                    'site_url'       => 'https://example.org',
                    'redirect'       => true,
                    'redirect_code'  => 302,
                    'trailing_slash' => false,
                ]
            ]
        ];
    }
}
