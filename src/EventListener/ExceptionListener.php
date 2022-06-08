<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {

        if ($_ENV['APP_ENV'] == 'dev') {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse('Not enough permission', Response::HTTP_FORBIDDEN));
        }

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new JsonResponse('No Route Found', Response::HTTP_NOT_FOUND));
        }
    }
}
