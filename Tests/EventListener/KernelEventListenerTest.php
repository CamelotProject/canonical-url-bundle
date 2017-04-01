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
        $event = new GetResponseEvent(
            new TestHttpKernel(),
            $this->getFooRequest(false),
            HttpKernelInterface::MASTER_REQUEST
        );

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
        $event = new GetResponseEvent(
            new TestHttpKernel(),
            $this->getFooRequest(true, false),
            HttpKernelInterface::MASTER_REQUEST
        );

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
    public function testTrailingSlashRedirect(array $config)
    {
        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $this->getFooRequest(),
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

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

        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $this->getBazRequest(true, false),
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

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
    public function testKernelRequestListenerDoesNothingWithEmptyRoute(array $config)
    {
        $event = new GetResponseEvent(
            new TestHttpKernel(),
            new Request(),
            HttpKernelInterface::MASTER_REQUEST
        );

        $listener = $this->getKernelEventListener($config);

        $returnValue = $listener->onKernelRequest($event);

        $this->assertFalse($returnValue);
    }

    /**
     * @dataProvider configProvider
     * @param array $config
     */
    public function testNonMatchingAlternativeRouteReturnsFalse(array $config)
    {
        $request = Request::create('https://example.org/bar/');

        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

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

        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

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
        $request  = Request::create('https://example.org/foo/bar/');
        $listener = $this->getKernelEventListener($config);

        $event = new GetResponseForExceptionEvent(
            new TestHttpKernel(),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );

        $listener->onKernelException($event);

        /** @var RedirectResponse $response */
        $response = $event->getResponse();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.org/foo/bar', $response->getTargetUrl());
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
