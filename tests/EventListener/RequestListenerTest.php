<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\EventListener;

use Camelot\CanonicalUrlBundle\EventListener\RequestListener;
use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListenerTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     */
    public function testCanonicalRedirect(array $config): void
    {
        $request = $this->getFooRequest(false);
        $event = $this->getRequestEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $listener = $this->getListener($config);
        $listener->onKernelRequest($event);

        static::assertNotSame($response, $event->getResponse());
        static::assertInstanceOf(RedirectResponse::class, $event->getResponse());
    }

    /**
     * @dataProvider configProvider
     */
    public function testNoRedirectWhenUrlIsCanonical(array $config): void
    {
        $request = $this->getFooRequest(true, false);
        $event = $this->getRequestEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $listener = $this->getListener($config);
        $listener->onKernelRequest($event);

        static::assertSame($response, $event->getResponse());
    }

    /**
     * @dataProvider configProvider
     */
    public function testKernelRequestListenerDoesNothingWithEmptyRoute(array $config): void
    {
        $event = $this->getRequestEvent(new Request());

        $listener = $this->getListener($config);

        $returnValue = $listener->onKernelRequest($event);

        static::assertFalse($returnValue);
    }

    protected function getRequestEvent(Request $request): RequestEvent
    {
        $event = new RequestEvent(
            $this-> createMock(HttpKernel::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        return $event;
    }

    protected function getListener(array $config): RequestListener
    {
        $router = $this->getRouter();

        $urlGenerator = new CanonicalUrlGenerator($router, $config);
        $listener = new RequestListener($urlGenerator, $config);

        return $listener;
    }
}
