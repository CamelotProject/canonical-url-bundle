<?php

namespace Palmtree\CanonicalUrlBundle\Tests\EventListener;

use Palmtree\CanonicalUrlBundle\EventListener\KernelEventListener;
use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Palmtree\CanonicalUrlBundle\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        $this->assertNotEquals($response, $event->getResponse());
        $this->assertTrue($event->getResponse() instanceof RedirectResponse);
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
        $this->assertTrue($response->getTargetUrl() === 'https://example.org/foo');
    }
}
