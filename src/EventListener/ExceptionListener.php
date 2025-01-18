<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = [
            'error' => $exception->getMessage(),
        ];
        $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } elseif ($exception instanceof \App\Exception\ValidationException) {
            $statusCode = JsonResponse::HTTP_BAD_REQUEST;
        }

        $event->setResponse(new JsonResponse($response, $statusCode));
    }
}