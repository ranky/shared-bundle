<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Exception;


use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class FilterException extends HttpDomainException
{

    public static function notInstanceOf(mixed $value): self
    {
        return new self(
            \sprintf(
                '%s not instance of Filter',
                \is_object($value) ? $value::class : $value
            ),
            400
        );
    }
}
