<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Exception;

use Psr\Log\LoggerInterface;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ApiProblemExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ApiProblemException) {
            $apiProblem = $exception->getApiProblem();

            if (!$apiProblem->getDetails() && $this->kernel->isDebug()) {
                $apiProblem->addDetail('file', $exception->getFile());
                $apiProblem->addDetail('line', $exception->getLine());
                $apiProblem->addDetail('trace', $exception->getTrace());
            }
            $this->logger->notice(\sprintf('ApiProblemException: %s', $apiProblem->getTitle()), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'status_code' => $exception->getStatusCode(),
                'type' => $apiProblem->getType(),
            ]);
            $response = new JsonResponse($apiProblem, $exception->getStatusCode());
            $response->headers->set('Content-Type', 'application/problem+json');
            $event->setResponse($response);
        }
    }
}
