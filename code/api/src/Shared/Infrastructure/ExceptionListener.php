<?php

declare(strict_types=1);

namespace Shared\Infrastructure;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ExceptionListener
{
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(
            new JsonResponse(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        );
    }
}
