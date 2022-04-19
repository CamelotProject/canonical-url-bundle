<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\EventListener;

use Camelot\CanonicalUrlBundle\EventListener\RequestListener;
use Camelot\CanonicalUrlBundle\Tests\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\EventListener\RequestListener
 */
final class RequestListenerTest extends KernelTestCase
{
    use TestTrait;

    public function testCanonicalRedirect(): void
    {
        $request = $this->getFooRequest(false);
        $event = $this->getRequestEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $this->getListener()->onKernelRequest($event);

        static::assertNotSame($response, $event->getResponse());
        static::assertInstanceOf(RedirectResponse::class, $event->getResponse());
    }

    public function testNoRedirectWhenUrlIsCanonical(): void
    {
        $request = $this->getFooRequest(true, false);
        $event = $this->getRequestEvent($request);

        $response = new Response();
        $event->setResponse($response);

        $this->getListener()->onKernelRequest($event);

        static::assertSame(200, $event->getResponse()->getStatusCode());
    }

    public function testKernelRequestListenerDoesNothingWithEmptyRoute(): void
    {
        $event = $this->getRequestEvent(new Request());
        $returnValue = $this->getListener()->onKernelRequest($event);

        static::assertFalse($returnValue);
    }

    protected function getRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(
            $this->createMock(HttpKernel::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }

    protected function getListener(): RequestListener
    {
        $urlGenerator = static::getContainer()->get(UrlGeneratorInterface::class);

        return new RequestListener($urlGenerator, true, 302);
    }
}
