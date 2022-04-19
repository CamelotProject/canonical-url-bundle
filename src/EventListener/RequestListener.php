<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RequestListener
{
    private UrlGeneratorInterface $urlGenerator;
    private bool $redirect;
    private int $redirectCode;

    public function __construct(UrlGeneratorInterface $urlGenerator, bool $redirect, int $redirectCode)
    {
        $this->urlGenerator = $urlGenerator;
        $this->redirect = $redirect;
        $this->redirectCode = $redirectCode;
    }

    /** Listener for the 'kernel.request' event. */
    public function onKernelRequest(RequestEvent $event): bool
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $params = $request->attributes->get('_route_params', []);

        if (!$route) {
            return false;
        }

        // Get full request URL without query string.
        $requestUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();
        $canonicalUrl = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);

        if ($requestUrl !== $canonicalUrl && $this->redirect) {
            $response = new RedirectResponse($canonicalUrl, $this->redirectCode);
            $event->setResponse($response);

            return true;
        }

        return false;
    }
}
