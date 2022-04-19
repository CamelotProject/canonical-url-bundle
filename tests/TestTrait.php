<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

trait TestTrait
{
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
