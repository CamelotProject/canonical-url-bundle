<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use function is_string;
use function ltrim;
use function parse_str;
use function rtrim;

final class CanonicalUrlGenerator
{
    private RouterInterface $router;
    private string $siteUrl;
    private bool $trailingSlash;

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
     * @param array|string $params parameters in 'key1=val1&key2=val2' format or key/value array
     */
    public function generate(string $route, null|string|array $params = []): string
    {
        $params = $this->getParameters($params);
        $uri = $this->router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);

        return rtrim($this->siteUrl, '/') . '/' . ltrim($uri, '/');
    }

    private function getParameters(null|string|array $parameters): array
    {
        if (is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        return (array) $parameters;
    }
}
