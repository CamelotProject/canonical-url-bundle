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
        $routeCollection->setHost('example.org');

        return $routeCollection;
    }

    /**
     * @param bool $secure
     * @return Request
     */
    protected function getFooRequest($secure = true)
    {
        $scheme = ($secure) ? 'https' : 'http';

        $request = Request::create("$scheme://example.org/foo/");
        $request->attributes->set('_route', 'foo');

        return $request;
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
