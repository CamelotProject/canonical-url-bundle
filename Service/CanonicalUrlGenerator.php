<?php

namespace Palmtree\CanonicalUrlBundle\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class CanonicalUrlGenerator
{
    /** @var RouterInterface */
    protected $router;
    /** @var string */
    protected $siteUrl;
    /** @var bool */
    protected $trailingSlash;

    /**
     * CanonicalUrlService constructor.
     * @param RouterInterface $router
     * @param array           $config
     */
    public function __construct(RouterInterface $router, array $config = [])
    {
        $this->router = $router;

        $this->siteUrl       = $config['site_url'];
        $this->trailingSlash = $config['trailing_slash'];
    }

    /**
     * Returns the canonical URL for a route.
     *
     * @param string       $route
     * @param array|string $parameters
     *
     * @return string
     */
    public function generateUrl($route, $parameters = [])
    {
        if (!$route) {
            return '';
        }

        if (is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        if (!$parameters) {
            $parameters = [];
        }

        try {
            $uri = $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $exception) {
            return '';
        }

        $url = rtrim($this->siteUrl, '/') . '/' . ltrim($uri, '/');

        return $url;
    }
}
