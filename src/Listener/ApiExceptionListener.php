<?php
declare(strict_types=1);

namespace App\Listener;

use App\Exception\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $e = $event->getException();

        if (!$e instanceof ApiException) {
            return;
        }

        $event->setResponse(new JsonResponse([
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
