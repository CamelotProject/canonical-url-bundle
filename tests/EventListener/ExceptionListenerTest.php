<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\EventListener;

use Camelot\CanonicalUrlBundle\EventListener\ExceptionListener;
use Camelot\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\EventListener\ExceptionListener
 */
final class ExceptionListenerTest extends AbstractTest
{
    /** @dataProvider configProvider */
    public function testTrailingSlashRedirect(array $config): void
    {
        $request = $this->getFooRequest();
        $event = $this->getExceptionEvent($request);
        $listener = $this->getListener($config);
        $listener->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/foo', $response->getTargetUrl());
    }

    /** @dataProvider configProvider */
    public function testNoTrailingSlashRedirect(array $config): void
    {
        $config['trailing_slash'] = true;
        $request = $this->getBazRequest(true, false);
        $event = $this->getExceptionEvent($request);
        $listener = $this->getListener($config);
        $listener->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/baz/', $response->getTargetUrl());
    }

    /** @dataProvider configProvider */
    public function testNonMatchingAlternativeRouteReturnsFalse(array $config): void
    {
        $request = Request::create('https://example.org/bar/');
        $event = $this->getExceptionEvent($request);
        $listener = $this->getListener($config);
        $returnValue = $listener->onKernelException($event);

        static::assertFalse($returnValue);
    }

    /** @dataProvider configProvider */
    public function testKernelRequestListenerDoesNothingForNonExistentRoute(array $config): void
    {
        $request = Request::create('https://example.org/bar');
        $event = $this->getExceptionEvent($request);
        $listener = $this->getListener($config);
        $returnValue = $listener->onKernelException($event);

        static::assertFalse($returnValue);
    }

    /** @dataProvider configProvider */
    public function testRouteWithUrlParametersAndTrailingSlashRedirectsToCorrectRoute(array $config): void
    {
        $request = Request::create('https://example.org/foo/bar/');
        $event = $this->getExceptionEvent($request);
        $listener = $this->getListener($config);
        $listener->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/foo/bar', $response->getTargetUrl());
    }

    protected function getExceptionEvent(Request $request): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernel::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new NotFoundHttpException('')
        );
    }

    protected function getListener(array $config): ExceptionListener
    {
        $router = $this->getRouter();

        return new ExceptionListener($router, $config);
    }
}
