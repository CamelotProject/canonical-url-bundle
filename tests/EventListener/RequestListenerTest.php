<?php

namespace Palmtree\CanonicalUrlBundle\Tests\EventListener;

use Palmtree\CanonicalUrlBundle\EventListener\RequestListener;
use Palmtree\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Tests\TestHttpKernel;

class RequestListenerTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testCanonicalRedirect(array $config)
    {
        $request = $this->getFooRequest(false);
        $event   = $this->getGetResponseEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $listener = $this->getListener($config);
        $listener->onKernelRequest($event);

        $this->assertNotSame($response, $event->getResponse());
        $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testNoRedirectWhenUrlIsCanonical(array $config)
    {
        $request = $this->getFooRequest(true, false);
        $event   = $this->getGetResponseEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $listener = $this->getListener($config);
        $listener->onKernelRequest($event);

        $this->assertSame($response, $event->getResponse());
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testKernelRequestListenerDoesNothingWithEmptyRoute(array $config)
    {
        $event = $this->getGetResponseEvent(new Request());

        $listener = $this->getListener($config);

        $returnValue = $listener->onKernelRequest($event);

        $this->assertFalse($returnValue);
    }

    /**
     * @param Request $request
     * @return GetResponseEvent
     */
    protected function getGetResponseEvent(Request $request)
    {
        $event = new GetResponseEvent(
            new TestHttpKernel(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        return $event;
    }

    /**
     * @param array $config
     * @return RequestListener
     */
    protected function getListener(array $config)
    {
        $router = $this->getRouter();

        $urlGenerator = new CanonicalUrlGenerator($router, $config);
        $listener     = new RequestListener($urlGenerator, $config);

        return $listener;
    }
}
