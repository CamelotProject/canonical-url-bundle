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
     * @param string       $route      Route to generate a URL for.
     * @param string|array $parameters String in 'key1=val1&key2=val2' format or array of query parameters.
     *
     * @return string
     */
    public function generate($route, $parameters = [])
    {
        if (!$route) {
            return '';
        }

        $parameters = $this->getParameters($parameters);

        try {
            $uri = $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $exception) {
            return '';
        }

        $url = rtrim($this->siteUrl, '/') . '/' . ltrim($uri, '/');

        return $url;
    }

    /**
     * @param string|array $parameters
     * @return array
     */
    protected function getParameters($parameters = [])
    {
        if (is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        if (!$parameters) {
            $parameters = [];
        }

        return $parameters;
    }
}
