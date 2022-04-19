<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\EventListener;

use Camelot\CanonicalUrlBundle\EventListener\ExceptionListener;
use Camelot\CanonicalUrlBundle\Tests\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\EventListener\ExceptionListener
 */
final class ExceptionListenerTest extends KernelTestCase
{
    use TestTrait;

    public function testTrailingSlashRedirect(): void
    {
        $request = $this->getFooRequest();
        $event = $this->getExceptionEvent($request);
        $this->getListener()->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/foo', $response->getTargetUrl());
    }

    public function testNoTrailingSlashRedirect(): void
    {
        $request = $this->getBazRequest(true, false);
        $event = $this->getExceptionEvent($request);
        $router = static::getContainer()->get(RouterInterface::class);
        (new ExceptionListener($router, true, 302, true))->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/baz/', $response->getTargetUrl());
    }

    public function testNonMatchingAlternativeRouteReturnsFalse(): void
    {
        $request = Request::create('https://example.org/bar/');
        $event = $this->getExceptionEvent($request);
        $returnValue = $this->getListener()->onKernelException($event);

        static::assertFalse($returnValue);
    }

    public function testKernelRequestListenerDoesNothingForNonExistentRoute(): void
    {
        $request = Request::create('https://example.org/bar');
        $event = $this->getExceptionEvent($request);
        $returnValue = $this->getListener()->onKernelException($event);

        static::assertFalse($returnValue);
    }

    public function testRouteWithUrlParametersAndTrailingSlashRedirectsToCorrectRoute(): void
    {
        $request = Request::create('https://example.org/foo/bar/');
        $event = $this->getExceptionEvent($request);
        $this->getListener()->onKernelException($event);
        $response = $event->getResponse();

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('https://example.org/foo/bar', $response->getTargetUrl());
    }

    protected function getExceptionEvent(Request $request): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernel::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new NotFoundHttpException('')
        );
    }

    protected function getListener(): ExceptionListener
    {
        $router = static::getContainer()->get(RouterInterface::class);

        return new ExceptionListener($router, true, 302, false);
    }
}
