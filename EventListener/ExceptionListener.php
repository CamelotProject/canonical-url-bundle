<?php

namespace Palmtree\CanonicalUrlBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ExceptionListener
{
    const CONTROLLER_PATTERN = '~^/app(?:_.+)*\.php$~';

    /** @var RouterInterface */
    protected $router;
    /** @var bool */
    protected $redirect;
    /** @var int */
    protected $redirectCode;
    /** @var bool */
    protected $trailingSlash;

    /**
     * KernelEventListener constructor.
     * @param RouterInterface $router
     * @param array           $config
     */
    public function __construct(RouterInterface $router, array $config = [])
    {
        $this->router = $router;

        $this->redirect      = $config['redirect'];
        $this->redirectCode  = $config['redirect_code'];
        $this->trailingSlash = $config['trailing_slash'];
    }

    /**
     * Listener for the 'kernel.exception' event.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return bool
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            // We're about to throw a 404 error, try to resolve it
            $request = $event->getRequest();

            $uri = strtok($request->getRequestUri(), '?');

            // See if there's a matching route without a trailing slash
            $match = $this->getAlternativeRoute($uri);

            if ($match !== null) {
                if ($this->redirect) {
                    $params = $request->query->all();

                    foreach ($match as $key => $value) {
                        if ($key[0] !== '_') {
                            $params[$key] = $value;
                        }
                    }

                    $url = $this->router->generate($match['_route'], $params, UrlGeneratorInterface::ABSOLUTE_URL);

                    $response = new RedirectResponse($url, $this->redirectCode);
                    $event->setResponse($response);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $uri
     *
     * @return array|null
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

            return $match;
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }
}
