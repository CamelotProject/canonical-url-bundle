<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use function is_string;

final class CanonicalUrlGenerator
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
        $this->trailingSlash = (bool) $config['trailing_slash'];
    }

    /**
     * Returns the canonical URL for a route.
     *
     * @param string       $route  Route to generate a URL for
     * @param string|array $params parameters in 'key1=val1&key2=val2' format or key/value array
     */
    public function generate(string $route, $params = []): string
    {
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
        if (is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        return $parameters;
    }
}
