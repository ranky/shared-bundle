<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Exception;

class HttpDomainException extends \DomainException
{
    public const DEFAULT_STATUS_CODE = 400; /* 400 bad request */

    public function __construct(
        string $message,
        private readonly int $statusCode = self::DEFAULT_STATUS_CODE,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
