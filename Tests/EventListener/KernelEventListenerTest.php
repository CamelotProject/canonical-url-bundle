<?php

namespace Palmtree\CanonicalUrlBundle\Tests\EventListener;

use Palmtree\CanonicalUrlBundle\EventListener\KernelEventListener;
use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Tests\TestHttpKernel;

class KernelEventListenerTest extends AbstractTest
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

        $listener = $this->getKernelEventListener($config);
        $listener->onKernelRequest($event);

        $this->assertNotSame($response, $event->getResponse());
        $this->assertTrue($event->getResponse() instanceof RedirectResponse);
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

        $listener = $this->getKernelEventListener($config);
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

        $listener = $this->getKernelEventListener($config);

        $returnValue = $listener->onKernelRequest($event);

        $this->assertFalse($returnValue);
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testTrailingSlashRedirect(array $config)
    {
        $request = $this->getFooRequest();
        $event   = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getKernelEventListener($config);

        $listener->onKernelException($event);

        /** @var RedirectResponse $response */
        $response = $event->getResponse();

        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertEquals('https://example.org/foo', $response->getTargetUrl());
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testNoTrailingSlashRedirect(array $config)
    {
        $config['trailing_slash'] = true;

        $request = $this->getBazRequest(true, false);

        $event = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getKernelEventListener($config);

        $listener->onKernelException($event);

        /** @var RedirectResponse $response */
        $response = $event->getResponse();

        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertEquals('https://example.org/baz/', $response->getTargetUrl());
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testNonMatchingAlternativeRouteReturnsFalse(array $config)
    {
        $request = Request::create('https://example.org/bar/');
        $event   = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getKernelEventListener($config);

        $returnValue = $listener->onKernelException($event);

        $this->assertFalse($returnValue);
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testKernelRequestListenerDoesNothingForNonExistentRoute(array $config)
    {
        $request = Request::create('https://example.org/bar');
        $event   = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getKernelEventListener($config);

        $returnValue = $listener->onKernelException($event);

        $this->assertFalse($returnValue);
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testRouteWithUrlParametersAndTrailingSlashRedirectsToCorrectRoute(array $config)
    {
        $request = Request::create('https://example.org/foo/bar/');
        $event   = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getKernelEventListener($config);

        $listener->onKernelException($event);

        /** @var RedirectResponse $response */
        $response = $event->getResponse();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.org/foo/bar', $response->getTargetUrl());
    }

    /**
     * @param Request $request
     * @return GetResponseForExceptionEvent
     */
    protected function getGetResponseForExceptionEvent(Request $request)
    {
        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

        return $event;
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
     * @return KernelEventListener
     */
    protected function getKernelEventListener(array $config)
    {
        $router = $this->getRouter();

        $urlGenerator = new CanonicalUrlGenerator($router, $config);
        $listener     = new KernelEventListener($router, $urlGenerator, $config);

        return $listener;
    }
}
