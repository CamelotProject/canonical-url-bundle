<?php

namespace Palmtree\CanonicalUrlBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class CanonicalUrlService
{
    const CONTROLLER_PATTERN = '~^/app(?:_.+)*\.php~';

    protected $router;
    protected $siteUrl;
    protected $redirectCode;

    /**
     * CanonicalUrlService constructor.
     * @param RouterInterface $router
     * @param string          $siteUrl
     * @param int             $redirectCode
     */
    public function __construct(RouterInterface $router, $siteUrl = null, $redirectCode = null)
    {
        $this->router       = $router;
        $this->siteUrl      = $siteUrl;
        $this->redirectCode = $redirectCode;
    }

    /**
     * Listener for the 'kernel.request' event.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $route = $request->get('_route');

        if (! $route || $route[0] === '_') {
            return;
        }

        $requestUrl   = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $canonicalUrl = $this->generateUrl($route, $request->getQueryString());

        if (strcasecmp($requestUrl, $canonicalUrl) !== 0) {
            $event->setResponse(new RedirectResponse($canonicalUrl, $this->redirectCode));
        }
    }

    /**
     * Listener for the 'kernel.exception' event.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            // We're about to throw a 404 error, try to resolve it
            $request = $event->getRequest();

            $uri = $request->getRequestUri();

            // See if there's a matching route without a trailing slash
            $match = $this->getUnslashedRoute($uri);

            if ($match) {
                $event->setResponse(new RedirectResponse($this->router->generate($match), $this->redirectCode));

                return;
            }
        }
    }

    /**
     * @param string $uri
     *
     * @return string|null
     */
    protected function getUnslashedRoute($uri)
    {
        $unslashedUri = rtrim($uri, '/');

        if ($unslashedUri === $uri) {
            return null;
        }

        $unslashedUri = preg_replace(static::CONTROLLER_PATTERN, '', $unslashedUri);

        try {
            $match = $this->router->match($unslashedUri);

            return $match['_route'];
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * Returns a canonical URL for a route.
     *
     * @param string       $route
     * @param array|string $parameters
     *
     * @return string
     */
    public function generateUrl($route, $parameters = [])
    {
        if (! $route) {
            return '';
        }

        if (is_string($parameters)) {
            parse_str($parameters, $parameters);
        }

        if (! $parameters) {
            $parameters = [];
        }

        $uri = $this->router->generate($route, $parameters);
        $url = rtrim($this->siteUrl, '/') . '/' . ltrim($uri, '/');

        return $url;
    }
}
