<?php

namespace Palmtree\CanonicalUrlBundle\EventListener;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class KernelEventListener
{
    const CONTROLLER_PATTERN = '~^/app(?:_.+)*\.php~';

    /** @var RouterInterface */
    protected $router;
    /** @var CanonicalUrlGenerator */
    protected $urlGenerator;
    /** @var bool */
    protected $redirect;
    /** @var int */
    protected $redirectCode;
    /** @var bool */
    protected $trailingSlash;

    /**
     * CanonicalUrlService constructor.
     * @param RouterInterface       $router
     * @param CanonicalUrlGenerator $urlGenerator
     * @param array                 $config
     */
    public function __construct(RouterInterface $router, CanonicalUrlGenerator $urlGenerator, array $config = [])
    {
        $this->router       = $router;
        $this->urlGenerator = $urlGenerator;

        $this->redirect      = $config['redirect'];
        $this->redirectCode  = $config['redirect_code'];
        $this->trailingSlash = $config['trailing_slash'];
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

        if (! $route) {
            return;
        }

        $requestUrl   = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $canonicalUrl = $this->urlGenerator->generateUrl($route, $request->getQueryString());

        if ($canonicalUrl && strcasecmp($requestUrl, $canonicalUrl) !== 0) {
            if ($this->redirect) {
                $event->setResponse(new RedirectResponse($canonicalUrl, $this->redirectCode));
            }
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
            $route = $this->getAlternativeRoute($uri);

            if ($route) {
                if ($this->redirect) {
                    $parameters = $request->query->all();
                    $url        = $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
                    $response   = new RedirectResponse($url, $this->redirectCode);
                    $event->setResponse($response);

                    return;
                }
            }
        }
    }

    /**
     * @param string $uri
     *
     * @return string|null
     */
    protected function getAlternativeRoute($uri)
    {
        $alternativeUri = rtrim($uri, '/');

        if ($this->trailingSlash) {
            $alternativeUri .= '/';
        }

        if ($alternativeUri === $uri) {
            return null;
        }

        $alternativeUri = preg_replace(static::CONTROLLER_PATTERN, '', $alternativeUri);

        try {
            $match = $this->router->match($alternativeUri);

            return $match['_route'];
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }
}
