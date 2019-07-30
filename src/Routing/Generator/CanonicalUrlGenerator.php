<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Routing\Generator;

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

    public function __construct(RouterInterface $router, array $config = [])
    {
        $this->router = $router;

        $this->siteUrl = $config['site_url'];
        $this->trailingSlash = $config['trailing_slash'];
    }

    /**
     * Returns the canonical URL for a route.
     *
     * @param string       $route  route to generate a URL for
     * @param string|array $params string in 'key1=val1&key2=val2' format or array of query parameters
     */
    public function generate(string $route, $params = []): string
    {
        if (!$route) {
            return '';
        }

        $params = $this->getParameters($params);
        $uri = $this->router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        $url = rtrim($this->siteUrl, '/') . '/' . ltrim($uri, '/');

        return $url;
    }

    /**
     * @param string|array $parameters
     */
    protected function getParameters($parameters = []): array
    {
        $parameters = $parameters ?: [];
        if (\is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        return $parameters;
    }
}
