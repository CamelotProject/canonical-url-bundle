<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\EventListener;

use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestListener
{
    private CanonicalUrlGenerator $canonicalUrlGenerator;
    private bool $redirect;
    private int $redirectCode;

    public function __construct(CanonicalUrlGenerator $canonicalUrlGenerator, bool $redirect, int $redirectCode)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
        $this->redirect = $redirect;
        $this->redirectCode = $redirectCode;
    }

    /** Listener for the 'kernel.request' event. */
    public function onKernelRequest(RequestEvent $event): bool
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if (!$route) {
            return false;
        }

        $params = $request->attributes->get('_route_params');
        // Get full request URL without query string.
        $requestUrl = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $requestUrl = urldecode(strtok($requestUrl, '?'));
        $redirectUrl = $this->canonicalUrlGenerator->generate($route, $params);
        $canonicalUrl = urldecode(strtok($redirectUrl, '?'));

        if ($redirectUrl && strcasecmp($requestUrl, $canonicalUrl) !== 0 && $this->redirect) {
            $response = new RedirectResponse($redirectUrl, $this->redirectCode);
            $event->setResponse($response);

            return true;
        }

        return false;
    }
}
