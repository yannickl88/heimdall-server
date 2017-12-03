<?php
declare(strict_types=1);

namespace App\Tests\Exception;

use App\Exception\ConfigException;
use App\Listener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @covers \App\Listener\ApiExceptionListener
 */
class ApiExceptionListenerTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        self::assertSame([KernelEvents::EXCEPTION => 'onKernelException'], ApiExceptionListener::getSubscribedEvents());
    }

    public function testOnKernelException()
    {
        $kernel = $this->prophesize(HttpKernelInterface::class);

        $event = new GetResponseForExceptionEvent(
            $kernel->reveal(),
            Request::create('/'),
            HttpKernelInterface::MASTER_REQUEST,
            ConfigException::identifierNotFound('foobar')
        );

        $listener = new ApiExceptionListener();
        $listener->onKernelException($event);

        self::assertSame(
            '{"error_code":1002,"error_message":"Unknown identifier \u0022foobar\u0022."}',
            $event->getResponse()->getContent()
        );
    }

    public function testOnKernelExceptionNonApiException()
    {
        $kernel = $this->prophesize(HttpKernelInterface::class);

        $event = new GetResponseForExceptionEvent(
            $kernel->reveal(),
            Request::create('/'),
            HttpKernelInterface::MASTER_REQUEST,
            new \RuntimeException('foobar')
        );

        $listener = new ApiExceptionListener();
        $listener->onKernelException($event);

        self::assertNull($event->getResponse());
    }
}
