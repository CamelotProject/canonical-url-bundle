<?php

namespace Palmtree\CanonicalUrlBundle\EventListener;

use Palmtree\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /** @var CanonicalUrlGenerator */
    protected $canonicalUrlGenerator;
    /** @var bool */
    protected $redirect;
    /** @var int */
    protected $redirectCode;

    /**
     * KernelEventListener constructor.
     * @param CanonicalUrlGenerator $urlGenerator
     * @param array                 $config
     */
    public function __construct(CanonicalUrlGenerator $urlGenerator, array $config = [])
    {
        $this->canonicalUrlGenerator = $urlGenerator;

        $this->redirect     = $config['redirect'];
        $this->redirectCode = $config['redirect_code'];
    }

    /**
     * Listener for the 'kernel.request' event.
     *
     * @param GetResponseEvent $event
     *
     * @return bool
     */
    public function onKernelRequest(GetResponseEvent $event)
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
        // Compare without query string
        $canonicalUrl = urldecode(strtok($redirectUrl, '?'));

        if ($redirectUrl && strcasecmp($requestUrl, $canonicalUrl) !== 0) {
            if ($this->redirect) {
                $response = new RedirectResponse($redirectUrl, $this->redirectCode);
                $event->setResponse($response);

                return true;
            }
        }

        return false;
    }
}
