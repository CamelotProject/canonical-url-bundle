<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ExceptionListener
{
    public const CONTROLLER_PATTERN = '~^/app(?:_.+)*\.php~';

    private RouterInterface $router;
    private bool $redirect;
    private int $redirectCode;
    private bool $trailingSlash;

    public function __construct(RouterInterface $router, array $config = [])
    {
        $this->router = $router;
        $this->redirect = (bool) $config['redirect'];
        $this->redirectCode = (int) $config['redirect_code'];
        $this->trailingSlash = (bool) $config['trailing_slash'];
    }

    /**
     * Listener for the 'kernel.exception' event.
     */
    public function onKernelException(ExceptionEvent $event): bool
    {
        if (!$event->getException() instanceof NotFoundHttpException) {
            return false;
        }
        // We're about to throw a 404 error, try to resolve it
        $request = $event->getRequest();
        $uri = strtok($request->getRequestUri(), '?');
        $match = $this->getAlternativeRoute($uri);

        if ($match === null || !$this->redirect) {
            return false;
        }

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

    /**
     * See if there's a matching route without a trailing slash.
     */
    private function getAlternativeRoute(string $uri): ?array
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
            return $this->router->match($alternativeUri);
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }
}
