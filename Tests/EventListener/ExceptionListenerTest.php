<?php

namespace Palmtree\CanonicalUrlBundle\Tests\EventListener;

use Palmtree\CanonicalUrlBundle\EventListener\ExceptionListener;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Tests\TestHttpKernel;

class ExceptionListenerTest extends AbstractTest
{
    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testTrailingSlashRedirect(array $config)
    {
        $request = $this->getFooRequest();
        $event   = $this->getGetResponseForExceptionEvent($request);

        $listener = $this->getListener($config);

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

        $listener = $this->getListener($config);

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

        $listener = $this->getListener($config);

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

        $listener = $this->getListener($config);

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

        $listener = $this->getListener($config);

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
     * @param array $config
     * @return ExceptionListener
     */
    protected function getListener(array $config)
    {
        $router   = $this->getRouter();
        $listener = new ExceptionListener($router, $config);

        return $listener;
    }
}
