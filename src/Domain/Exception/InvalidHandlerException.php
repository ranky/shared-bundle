<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Exception;

final class InvalidHandlerException extends HttpDomainException
{
    /**
     * @template T of object
     *
     * @param class-string<T> $expectedType
     * @param T $handler
     *
     * @return self
     */
    public static function instanceOf(string $expectedType, object $handler): self
    {
        return new self(
            \sprintf(
                'Handler %s is not an instance of %s',
                $handler::class,
                $expectedType
            )
        );
    }
}
