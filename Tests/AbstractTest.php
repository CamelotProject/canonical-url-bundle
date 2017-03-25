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
    public function getRouter($routeCollection = null)
    {
        if (! $routeCollection) {
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

    public function getFooRouteCollection()
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

    public function configProvider()
    {
        return array(
            'config' => array(
                array(
                    'site_url'       => 'https://example.org',
                    'redirect'       => true,
                    'redirect_code'  => 302,
                    'trailing_slash' => false,
                )
            )
        );
    }
}
